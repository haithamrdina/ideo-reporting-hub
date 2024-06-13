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

class ExportTicketJob implements ShouldQueue
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
        $csvExporter->beforeEach(function ($ticket) {
            $ticket->project_id = Project::find($ticket->project_id)->name;
            $ticket->group_id = Group::find($ticket->group_id)->name;
            $ticket->learner_docebo_id = Learner::where('docebo_id', $ticket->learner_docebo_id)->first()->username;
            $ticket->status = $ticket->status;
            $ticket->subject = $ticket->subject;
            $ticket->ticket_created_at = $ticket->ticket_created_at;
            $ticket->ticket_updated_at = $ticket->ticket_updated_at;
        });
        $csvExporter->build($this->data, $this->fields);
        $writer = $csvExporter->getWriter();
        Storage::put($this->filename, "\xEF\xBB\xBF" . $writer->getContent());
        $end_datetime = date('Y-m-d H:i:s');
        Log::info("['end'][$end_datetime]: Export data tickets has finished.");
    }
}