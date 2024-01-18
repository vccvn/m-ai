<?php

namespace App\Console\Commands;

use App\Services\GPT\PromptService;
use Illuminate\Console\Command;

class UpdatePrompt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gpt:update-prompt {print=0 : In ra từng dòng}';

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
    public function handle(PromptService $promptService)
    {
        $p = $this->argument('print');
        $pp = $p && $p != "0"? $p : "false";
        $isPrint = $pp === true || in_array($pp, [1, "true", "print", "echo"]);
        // dump($isPrint);
        $promptService->updateAllPrompt( $isPrint );
        return Command::SUCCESS;
    }
}
