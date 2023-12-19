<?php

namespace App\Console\Commands;

use App\Excels\ExportPlaceData;
use App\Excels\PlaceData;
use App\Repositories\Locations\PlaceRepository;
use App\Services\Awards\AwardService;
use Gomee\Files\Filemanager;
use Illuminate\Console\Command;

class ExportPlace extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:place';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Place';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(PlaceRepository $placeRepository)
    {
        $excel = new ExportPlaceData(storage_path('excels/templates/place-export.xlsx'));
        $res = $placeRepository->join('regions', 'regions.id', '=', 'places.region_id')->select('regions.name as region', 'places.name')->get()->toArray();
        print_r($res);
        $excel->setSheetData(0, $res);
        $excel->save();
        return Command::SUCCESS;
    }
}
