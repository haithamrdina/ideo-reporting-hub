<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\ExportReady;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NotifyUserOfCompletedExport implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $data;

    public function __construct($user, $data)
    {
        $this->user = $user;
        $this->data = $data;
    }

    public function handle()
    {
        $start_datetime = date('Y-m-d H:i:s');
        Log::info("['start'][$start_datetime]: Notify user has started.");
        $this->user->notify(new ExportReady($this->data));
        $end_datetime = date('Y-m-d H:i:s');
        Log::info("['end'][$end_datetime]: Notify user has finished.");
    }
}