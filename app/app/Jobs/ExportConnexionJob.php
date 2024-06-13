<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Laracsv\Export;

class ExportConnexionJob implements ShouldQueue
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
        Log::info("['start'][$start_datetime]: Export data connexion has started.");
        $csvExporter = new Export();
        $csvExporter->build(collect($this->data), $this->fields);
        $writer = $csvExporter->getWriter();
        Storage::put($this->filename, "\xEF\xBB\xBF" . $writer->getContent());
        $end_datetime = date('Y-m-d H:i:s');
        Log::info("['end'][$end_datetime]: Export data connexion has finished.");
    }
}