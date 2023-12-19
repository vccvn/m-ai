<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Repositories\Payments\AgentPaymentLogRepository;
use App\Repositories\Promotions\CampaignRepository;
use App\Services\EKYC\EKYCService;
use App\Services\ESMS\ESMSService;
use App\Services\Reports\ReportService;
use Gomee\Files\Filemanager;
use Illuminate\Console\Command;

class TestSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'test Hệ thống';
    /**
     * filemanager
     *
     * @var Filemanager
     */
    protected $fileManager = null;
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info("Test:...");
        try {
            $vars = [];
            $text = '{abc} tesrt {{yy}} Haha {@test(:a,:b,$a,@abc)}';
            $varCount = preg_match_all('/\{([^@\{][A-z0-9_]+)\}/', $text, $matches);
            if($varCount){
                for ($i=0; $i < count($matches[0]); $i++) {
                    $vars[] = [
                        'key' => $matches[1][$i],
                        'raw' => $matches[0][$i]
                    ] ;
                }
            }
            print_r($vars);
            return 0;
            /**
             * @var EKYCService
             */
            $eKYC = app(EKYCService::class);
            $eKYC->verifyCICard(storage_path('ekyc/test.jpeg'));
            return ;

            /**
             * @var ESMSService
             */
            $eSMS = app(ESMSService::class);
            if(!$eSMS->sendMessage('0945786960', '234567 la ma xac minh dang ky Baotrixemay cua ban')){
                $this->warn($eSMS->getErrorMessage());
            }else{
                $this->info('Gửi tin nhắn thành công');
            }
        } catch (\Throwable $th) {
            $this->error($th->getMessage());
        }
        return 0;
    }

    public function removeHtml($path)
    {
        if ($list = $this->fileManager->getList($path)) {
            foreach ($list as $item) {
                if ($item->type == 'folder') {
                    $this->removeHtml($item->path);
                } elseif ($item->extension == 'html') {
                    $this->fileManager->delete($item->path);
                    echo "delete $item->path\n";
                }
            }
        }
    }
}
