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

class ExportCegosJob implements ShouldQueue
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
        Log::info("['start'][$start_datetime]: Export data softkills has started.");
        $userfields = config('tenantconfigfields.userfields');
        $enrollfields = config('tenantconfigfields.enrollmentfields');
        $csvExporter = new Export();
        $csvExporter->beforeEach(function ($enroll) use ($userfields, $enrollfields) {
            $timeConversionService = new TimeConversionService();
            $learner = Learner::where('docebo_id', $enroll->learner_docebo_id)->first();
            $enroll->project_id = Project::find($enroll->project_id)->name;
            $enroll->group_id = Group::find($enroll->group_id)->name;
            $enroll->module_docebo_id = Module::where('docebo_id', $enroll->module_docebo_id)->first()->name;
            $enroll->learner_docebo_id = $learner->username;
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
        });
        $csvExporter->build($this->data, $this->fields);
        $writer = $csvExporter->getWriter();
        Storage::put($this->filename, "\xEF\xBB\xBF" . $writer->getContent());
        $end_datetime = date('Y-m-d H:i:s');
        Log::info("['end'][$end_datetime]: Export data softkills has finished.");
    }
}