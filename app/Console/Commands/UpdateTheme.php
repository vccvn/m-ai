<?php

namespace App\Console\Commands;

use App\Services\Themes\ThemeService;
use Illuminate\Console\Command;

class UpdateTheme extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theme:update {id=0:Id của theme} {active=0:active}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cập nhật theme';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(ThemeService $themeService)
    {
        $id = $this->argument('id');
        if($id == 0)
            $themeService->updateAllTheme();
        $themeService->devUpdate($id, $this->argument('active') != 0);

        return Command::SUCCESS;
    }
}
