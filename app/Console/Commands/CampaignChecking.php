<?php

namespace App\Console\Commands;

use App\Services\Promotions\CampaignService;
use Gomee\Files\Filemanager;
use Illuminate\Console\Command;

class CampaignChecking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'campaign:checking';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kiểm tra Chiến dịch quảng cáo';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(CampaignService $campaignService, Filemanager $filemanager)
    {
        $s = true;

        try {
            $campaignService->checkRunningCampaigns();
            $campaignService->clearCampaignExpired();
        } catch (\Throwable $th) {
            $filemanager->append("[". date('Y-m-d H:i:s') . "] " .$th->getMessage() . "\n", storage_path('logs/campaign-errors.log'));
            $s = false;
        }
        // $filemanager->append(date('Y-m-d H:i:s') . " - Chạy task kiểm tra " . ($s?"Thành công": "Thất bại") . "\n", storage_path('logs/campaign-checking.log'));
        return Command::SUCCESS;
    }
}
