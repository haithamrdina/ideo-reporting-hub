<?php

namespace App\Console\Commands;

use App\Jobs\UpdateGroupJob;
use App\Mail\TestMail;
use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class DailyUpdateData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:daily-update-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Mail::to('your_test_mail@gmail.com')->send(new TestMail([
            'title' => 'The Title',
            'body' => 'The Body',
        ]));
    }
}
