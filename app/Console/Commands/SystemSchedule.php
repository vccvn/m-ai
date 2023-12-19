<?php

namespace App\Console\Commands;
use App\Repositories\Payments\AgentPaymentLogRepository;
use App\Services\ESMS\ESMSService;
use App\Services\Reports\ReportService;
use Gomee\Apis\Api;
use Gomee\Files\Filemanager;
use Illuminate\Console\Command;

class SystemSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:schedule';

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

        $apiUrl = 'https://trekka.vcc.vn/api';
        $api = new Api();
        $api->setResponseType('json');
        try {
            $rs = $api->post($apiUrl . '/schedules/agent-report', ['key' => 5]);
            print_r($rs);
        } catch (\Throwable $th) {
            //throw $th;
        }
        return 0;
    }


}
