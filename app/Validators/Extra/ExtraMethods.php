<?php
namespace App\Validators\Extra;
use App\Repositories\Locations\DistrictRepository;
use App\Repositories\Locations\RegionRepository;
use App\Repositories\Locations\WardRepository;

trait ExtraMethods{
    public function extraExtends()
    {
        $this->addRule('check_region', function($prop, $value){
            if(!$value) return true;
            return app(RegionRepository::class)->find($value) ? true : false;
        });
        $this->addRule('check_district', function($prop, $value, $parameters){
            if(!$value) return true;
            $region_id = ($p = $this->parseParameters($parameters))? ($p[0] && $this->{$p[0]} ?$this->{$p[0]} : $this->region_id ) : $this->region_id;
            return $region_id && app(DistrictRepository::class)->first(['id' => $value, 'region_id' => $region_id]) ? true : false;
        });
        $this->addRule('check_ward', function($prop, $value, $parameters){
            if(!$value) return true;
            $district_id = ($p = $this->parseParameters($parameters))? ($p[0] && $this->{$p[0]} ?$this->{$p[0]} : $this->district_id ) : $this->district_id;
            return $district_id && app(WardRepository::class)->first(['id' => $value, 'district_id' => $district_id]) ? true : false;
        });
    }
}