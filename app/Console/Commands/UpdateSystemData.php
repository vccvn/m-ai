<?php

namespace App\Console\Commands;

use App\Repositories\Options\SettingRepository;
use Illuminate\Console\Command;

class UpdateSystemData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-system-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(SettingRepository $settingRepository)
    {
        $settingRepository->createNewData();
        return Command::SUCCESS;
    }
}
