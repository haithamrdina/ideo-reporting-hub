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
use Laracsv\Export;

class ExportInactiveJob implements ShouldQueue
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
        Log::info("['start'][$start_datetime]: Export data inscrits inactifs has started.");
        $userfields = config('tenantconfigfields.userfields');
        $csvExporter = new Export();
        $csvExporter->beforeEach(function ($learner) use ($userfields) {
            $learner->project_id = Project::find($learner->project_id)->name;
            $learner->group_id = Group::find($learner->group_id)->name;
            $learner->username = $learner->username;
            $learner->lastname = $learner->lastname;
            $learner->firstname = $learner->firstname;
            $learner->creation_date = $learner->creation_date;

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
        });
        $csvExporter->build($this->data, $this->fields);
        $writer = $csvExporter->getWriter();
        Storage::put($this->filename, "\xEF\xBB\xBF" . $writer->getContent());
        $end_datetime = date('Y-m-d H:i:s');
        Log::info("['end'][$end_datetime]: Export data inscrits inactifs has finished.");
    }
}