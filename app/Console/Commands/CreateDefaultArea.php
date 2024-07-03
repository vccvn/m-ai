<?php

namespace App\Console\Commands;

use App\Repositories\Html\AreaRepository;
use Illuminate\Console\Command;

class CreateDefaultArea extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:area';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tạo area mặc định';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(AreaRepository $areaRepository)
    {
        $areaRepository->createDefaultArea();
        return Command::SUCCESS;
    }
}
