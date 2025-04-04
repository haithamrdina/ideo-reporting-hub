<?php

namespace App\Jobs;

use App\Models\Group;
use App\Models\Learner;
use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Laracsv\Export;

class ExportTicketsJob implements ShouldQueue
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
        Log::info("['start'][$start_datetime]: Export data tickets has started.");
        $csvExporter = new Export();

        // Définir le délimiteur comme tabulation au lieu de la virgule par défaut
        $csvExporter->getWriter()->setDelimiter("\t");

        $csvExporter->beforeEach(function ($ticket) {
            $ticket->project_id = Project::find($ticket->project_id)->name;
            $ticket->group_id = Group::find($ticket->group_id)->name;
            $ticket->learner_docebo_id = Learner::where('docebo_id', $ticket->learner_docebo_id)->first()->username;
            $ticket->status = $ticket->status;
            $ticket->subject = $ticket->subject;
            $ticket->ticket_created_at = $ticket->ticket_created_at;
            $ticket->ticket_updated_at = $ticket->ticket_updated_at;
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
        Log::info("['end'][$end_datetime]: Export data tickets has finished.");
    }
}
