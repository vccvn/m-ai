<?php

namespace App\Console\Commands;

use App\Repositories\Locations\CountryRepository;
use App\Repositories\Locations\RegionRepository;
use Illuminate\Console\Command;

class ImportLocationData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:location {country?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Nhập dữ liệu địa điểm';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(CountryRepository $countryRepository, RegionRepository $regionRepository)
    {
        $country = $this->argument('country');
        if($country == 'vietnam'){
            $data = json_decode(file_get_contents(base_path('json/data/location/vietnam.json')), true);
            if($data){
                $countryRepository->createCountry($data, 10);
            }
        }
        return Command::SUCCESS;
    }
}
