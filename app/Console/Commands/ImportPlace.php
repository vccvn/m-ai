<?php

namespace App\Console\Commands;

use App\Excels\ImportPlaceData;
use App\Excels\PlaceData;
use App\Repositories\Locations\PlaceRepository;
use App\Repositories\Locations\RegionRepository;
use App\Services\Awards\AwardService;
use Gomee\Files\Filemanager;
use Illuminate\Console\Command;

class ImportPlace extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:place';

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
    public function handle(PlaceRepository $placeRepository, RegionRepository $regionRepository)
    {

        $excel = new ImportPlaceData(storage_path('excels/templates/place-export.xlsx'));
        $rs = $excel->getSheetData();
        $d = [];
        $regions = [];
        foreach ($rs as $p) {

            $regionSlug = str_slug($p['region_name']);
            if (!array_key_exists($regionSlug, $regions)) {
                if (!($region = $regionRepository->select('id')->first(['slug' => $regionSlug])))
                    continue;
                $regions[$regionSlug] = $region->id;
            }

            $p['region_id'] = $regions[$regionSlug];

            $p['priority'] = 10;
            $a =  $placeRepository->createDataIfNotExists($p);
            echo "import: " . $a->name . ' - ' . $p['region_name'] . "\n";
        }

        return Command::SUCCESS;




        $excel = new PlaceData(storage_path('excels/templates/place.xlsx'));
        $rs = $excel->getSheetData();
        $d = [];
        foreach ($rs as $p) {
            $p['priority'] = 10;
            $a =  $placeRepository->createDataIfNotExists($p);
            echo "import: " . $a->name . ' - ' . $a->region_id . "\n";
        }
        return Command::SUCCESS;
    }
}
