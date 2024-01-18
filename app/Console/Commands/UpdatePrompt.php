<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdatePrompt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gpt:update-prompt {print=0:In ra từng dòng}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all prompt';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        return Command::SUCCESS;
    }
}
