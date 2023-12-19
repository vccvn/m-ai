<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ShowSetting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'settings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'hiển thị setting';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        print_r(discount_setting());
        return Command::SUCCESS;
    }
}
