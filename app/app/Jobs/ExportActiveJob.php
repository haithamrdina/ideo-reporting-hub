<?php

namespace App\Jobs;

use App\Models\Group;
use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Services\TimeConversionService;
use Illuminate\Support\Facades\DB;
use Laracsv\Export;

class ExportActiveJob implements ShouldQueue
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
        Log::info("['start'][$start_datetime]: Export data inscrits actifs has started.");
        $userfields = config('tenantconfigfields.userfields');
        $enrollfields = config('tenantconfigfields.enrollmentfields');
        $csvExporter = new Export();

        // Définir le délimiteur comme tabulation au lieu de la virgule par défaut
        $csvExporter->getWriter()->setDelimiter("\t");

        $csvExporter->beforeEach(function ($learner) use ($userfields, $enrollfields) {
            $timeConversionService = new TimeConversionService();
            $learner->project_id = Project::find($learner->project_id)->name;
            $learner->group_id = Group::find($learner->group_id)->name;
            $learner->username = $learner->username;
            $learner->lastname = $learner->lastname;
            $learner->firstname = $learner->firstname;
            $learner->creation_date = $learner->creation_date;
            $learner->last_access_date = $learner->last_access_date;

            if (isset($userfields['matricule']) && $userfields['matricule'] === true) {
                $learner->matricule = $learner->matricule;
            }
            if (isset($userfields['fonction']) && $userfields['fonction'] === true) {
                $learner->fonction = $learner->fonction;
            }
            if (isset($userfields['direction']) && $userfields['direction'] === true) {
                $learner->direction = $learner->direction;
            }
            if (isset($userfields['categorie']) && $userfields['categorie'] === true) {
                $learner->categorie = $learner->categorie;
            }
            if (isset($userfields['sexe']) && $userfields['sexe'] === true) {
                $learner->sexe = $learner->sexe;
            }

            $totalCegosTimes = DB::table('enrollmodules')
                ->selectRaw('SUM(session_time) as total_session_time')
                ->selectRaw('SUM(cmi_time) as total_cmi_time')
                ->selectRaw('SUM(calculated_time) as total_calculated_time')
                ->selectRaw('SUM(recommended_time) as total_recommended_time')
                ->where('learner_docebo_id', '=', $learner->docebo_id)
                ->first();
            $totalMoocTimes = DB::table('enrollmoocs')
                ->selectRaw('SUM(session_time) as total_session_time')
                ->selectRaw('SUM(cmi_time) as total_cmi_time')
                ->selectRaw('SUM(calculated_time) as total_calculated_time')
                ->selectRaw('SUM(recommended_time) as total_recommended_time')
                ->where('learner_docebo_id', '=', $learner->docebo_id)
                ->first();
            $totalSpeexTimes = DB::table('langenrolls')
                ->selectRaw('SUM(session_time) as total_session_time')
                ->selectRaw('SUM(cmi_time) as total_cmi_time')
                ->selectRaw('SUM(calculated_time) as total_calculated_time')
                ->selectRaw('SUM(recommended_time) as total_recommended_time')
                ->where('learner_docebo_id', '=', $learner->docebo_id)
                ->first();
            $totalTickets = DB::table('tickets')->where('learner_docebo_id', '=', $learner->docebo_id)->count();
            $totalCalls = DB::table('calls')->where('learner_docebo_id', '=', $learner->docebo_id)->count();
            $learner->session_time = $timeConversionService->convertSecondsToTime($totalCegosTimes->total_session_time + $totalMoocTimes->total_session_time + $totalSpeexTimes->total_session_time);
            if (isset($enrollfields['cmi_time']) && $enrollfields['cmi_time'] === true) {
                $learner->cmi_time = $timeConversionService->convertSecondsToTime($totalCegosTimes->total_cmi_time + $totalMoocTimes->total_cmi_time + $totalSpeexTimes->total_cmi_time);
            }
            if (isset($enrollfields['calculated_time']) && $enrollfields['calculated_time'] === true) {
                $learner->calculated_time = $timeConversionService->convertSecondsToTime($totalCegosTimes->total_calculated_time + $totalMoocTimes->total_calculated_time + $totalSpeexTimes->total_calculated_time);
            }
            if (isset($enrollfields['recommended_time']) && $enrollfields['recommended_time'] === true) {
                $learner->recommended_time = $timeConversionService->convertSecondsToTime($totalCegosTimes->total_recommended_time + $totalMoocTimes->total_recommended_time + $totalSpeexTimes->total_recommended_time);
            }
            $learner->count_ticket = $totalTickets;
            $learner->count_call = $totalCalls;
        });

        // Construction du CSV avec les données et les champs définis
        $csvExporter->build($this->data, $this->fields);
        $writer = $csvExporter->getWriter();

        // Ajout du BOM UTF-8 pour assurer la compatibilité avec Excel
        Storage::put($this->filename, "\xEF\xBB\xBF" . $writer->getContent());

        // Si vous préférez enregistrer en format Excel directement
        // Décommentez le code ci-dessous et commentez la ligne Storage::put au-dessus

        // Changer l'extension du fichier en .xlsx
        $excelFilename = str_replace('.csv', '.xlsx', $this->filename);

        // Créer un objet Excel avec le contenu CSV
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
        $reader->setDelimiter("\t");
        $reader->setInputEncoding('UTF-8');
        $csv_content = $writer->getContent();

        // Écrire le contenu dans un fichier temporaire
        $tempFile = tempnam(sys_get_temp_dir(), 'csv');
        file_put_contents($tempFile, "\xEF\xBB\xBF" . $csv_content);

        // Lire le fichier CSV dans un objet SpreadSheet
        $spreadsheet = $reader->load($tempFile);

        // Supprimer le fichier temporaire
        unlink($tempFile);

        // Enregistrer en format Excel
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        // Écrire dans le stockage
        $tempExcelFile = tempnam(sys_get_temp_dir(), 'xlsx');
        $writer->save($tempExcelFile);
        Storage::put($excelFilename, file_get_contents($tempExcelFile));
        unlink($tempExcelFile);


        $end_datetime = date('Y-m-d H:i:s');
        Log::info("['end'][$end_datetime]: Export data inscrits actifs has finished.");
    }
}
