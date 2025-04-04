<?php

namespace App\Jobs;

use App\Models\Group;
use App\Models\Learner;
use App\Models\Module;
use App\Models\Project;
use App\Services\TimeConversionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Laracsv\Export;

class ExportEniJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $filename;
    protected $fields;
    /**
     * Create a new job instance.
     */
    public function __construct($data, $fields, $filename)
    {
        $this->data = $data;
        $this->fields = $fields;
        $this->filename = $filename;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $start_datetime = date('Y-m-d H:i:s');
        Log::info("['start'][$start_datetime]: Export data digital has started.");
        $userfields = config('tenantconfigfields.userfields');
        $enrollfields = config('tenantconfigfields.enrollmentfields');
        $csvExporter = new Export();

        // Définir le délimiteur comme tabulation au lieu de la virgule par défaut
        $csvExporter->getWriter()->setDelimiter("\t");

        $csvExporter->beforeEach(function ($enroll) use ($userfields, $enrollfields) {
            $timeConversionService = new TimeConversionService();
            $learner = Learner::where('docebo_id', $enroll->learner_docebo_id)->first();
            if ($learner) {
                $enroll->project_id = Project::find($enroll->project_id)->name;
                $enroll->group_id = Group::find($enroll->group_id)->name;
                $enroll->module_docebo_id = Module::where('docebo_id', $enroll->module_docebo_id)->first()->name;
                $enroll->learner_docebo_id = $learner->username;
                if (isset($userfields['matricule']) && $userfields['matricule'] === true) {
                    $enroll->matricule = Learner::where('docebo_id', $enroll->learner_docebo_id)->first()->matricule;
                }
                $enroll->enrollment_created_at = $enroll->enrollment_created_at != null ? $enroll->enrollment_created_at : '******';
                if ($enroll->status == 'waiting') {
                    $enroll->status = "En attente";
                } elseif ($enroll->status == 'enrolled') {
                    $enroll->status = "Inscrit";
                } elseif ($enroll->status == 'in_progress') {
                    $enroll->status = "En cours";
                } elseif ($enroll->status == 'completed') {
                    $enroll->status = "Terminé";
                }
                $enroll->enrollment_updated_at = $enroll->enrollment_updated_at != null ? $enroll->enrollment_updated_at : '******';
                $enroll->enrollment_completed_at = $enroll->enrollment_completed_at != null ? $enroll->enrollment_completed_at : '******';
                $enroll->session_time = $enroll->session_time != null ? $timeConversionService->convertSecondsToTime($enroll->session_time) : '******';
                if (isset($enrollfields['cmi_time']) && $enrollfields['cmi_time'] === true) {
                    $enroll->cmi_time = $enroll->cmi_time != null ? $timeConversionService->convertSecondsToTime($enroll->cmi_time) : '******';
                }
                if (isset($enrollfields['calculated_time']) && $enrollfields['calculated_time'] === true) {
                    $enroll->calculated_time = $enroll->calculated_time != null ? $timeConversionService->convertSecondsToTime($enroll->calculated_time) : '******';
                }
                if (isset($enrollfields['recommended_time']) && $enrollfields['recommended_time'] === true) {
                    $enroll->recommended_time = $enroll->recommended_time != null ? $timeConversionService->convertSecondsToTime($enroll->recommended_time) : '******';
                }
            }
        });

        // Build the CSV with the data and defined fields
        $csvExporter->build($this->data, $this->fields);
        $writer = $csvExporter->getWriter();

        // Store the CSV file
        Storage::put($this->filename, "\xEF\xBB\xBF" . $writer->getContent());

        // Change the file extension to .xlsx
        $excelFilename = str_replace('.csv', '.xlsx', $this->filename);

        // Create a spreadsheet object with the CSV content
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
        $reader->setDelimiter("\t");
        $reader->setInputEncoding('UTF-8');
        $csv_content = $writer->getContent();

        // Write the content to a temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'csv');
        file_put_contents($tempFile, "\xEF\xBB\xBF" . $csv_content);

        // Read the CSV file into a SpreadSheet object
        $spreadsheet = $reader->load($tempFile);

        // Delete the temporary file
        unlink($tempFile);

        // Save as Excel format
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        // Write to storage
        $tempExcelFile = tempnam(sys_get_temp_dir(), 'xlsx');
        $writer->save($tempExcelFile);
        Storage::put($excelFilename, file_get_contents($tempExcelFile));
        unlink($tempExcelFile);

        $end_datetime = date('Y-m-d H:i:s');
        Log::info("['end'][$end_datetime]: Export data digital has finished.");
    }
}
