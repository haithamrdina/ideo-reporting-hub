<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\ExportReady;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Rap2hpoutre\FastExcel\FastExcel;

class NotifyUserOfCompletedExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    public $data;

    public function __construct($user, $data)
    {
        $this->user = $user;
        $this->data = $data;
    }

    public function handle()
    {
        (new FastExcel($this->data['enrollements']))->download('rapport_formation_softskills.xlsx', function ($enroll) {
            $userfields = config('tenantconfigfields.userfields');
            $enrollfields = config('tenantconfigfields.enrollmentfields');
            $data = [
                'Branche' => $enroll->project->name ?? '******',
                'Filiale' => $enroll->group->name ?? '******',
                'Module' => $enroll->module->name ?? '******',
                'Username' => $enroll->module->name ?? '******',
            ];

            if (isset($userfields['matricule']) && $userfields['matricule'] === true) {
                $data['Matricule'] = $enroll->learner->matricule ?? 'Matricule';
            }

            $data['Date d\'inscription'] = $enroll->enrollment_created_at ?? 'Date d\'inscription';
            $data['Statut'] = $enroll->status ?? '******';
            $data['Date du dernière modification'] = $enroll->enrollment_updated_at ?? '******';
            $data['Date d\'achèvement'] = $enroll->enrollment_completed_at ?? '******';
            $data['Temps de session'] = $enroll->session_time ?? '******';

            if (isset($enrollfields['cmi_time']) && $enrollfields['cmi_time'] === true) {
                $data['Temps d\'engagement'] = $enroll->cmi_time ?? '******';
            }

            if (isset($enrollfields['calculated_time']) && $enrollfields['calculated_time'] === true) {
                $data['Temps calculé'] = $enroll->calculated_time ?? '******';
            }

            if (isset($enrollfields['recommended_time']) && $enrollfields['recommended_time'] === true) {
                $data['Temps pédagogique recommandé'] = $enroll->recommended_time ?? '******';
            }

            return $data;
        });
        $this->user->notify(new ExportReady($this->data['details']));
    }
}
