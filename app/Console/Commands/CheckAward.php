<?php

namespace App\Console\Commands;

use App\Services\Awards\AwardService;
use Gomee\Files\Filemanager;
use Illuminate\Console\Command;

class CheckAward extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:award';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto check award';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(AwardService $awardService)
    {
        try {
            $awardService->autoCheck();
            $filemanager = new Filemanager();
            $filemanager->append($message = "\n[".date('Y-m-d H:i:s')."] Chạy  task kiểm tra trao thưởng", storage_path('cronjob/awards.log'));
            $this->info($message);
        } catch (\Throwable $th) {
            //throw $th;
        }
        return Command::SUCCESS;
    }
}
