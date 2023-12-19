<?php

namespace App\Jobs;

use Carbon\Carbon;
use Gomee\Files\Filemanager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $dateTime = null;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->dateTime = Carbon::now()->toDateTimeString();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $filemanager = new Filemanager();
        $filemanager->append($this->dateTime . ' - ' . (Carbon::now()->toDateTimeString()) . "\n", storage_path('logs/queue-test.log'));
    }
}
