<?php

namespace App\Http\Controllers\Web\Common;

use App\Http\Controllers\Web\WebController;

use App\Repositories\Locations\RegionRepository;
use App\Repositories\Locations\DistrictRepository;
use App\Repositories\Locations\WardRepository;

use Illuminate\Http\Request;


class LocationController extends WebController
{
    protected $module = 'locations';

    protected $moduleName = 'Địa điểm';

    protected $flashMode = true;

    /**
     * repository chinh
     *
     * @var RegionRepository
     */
    public $repository;

    /**
     * @var DistrictRepository
     *
     */
    public $districtRepository;

    /**
     * reposaitory quanr ly xa / phuong
     *
     * @var WardRepository
     */
    public $wardRepository;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(RegionRepository $repository, DistrictRepository $districtRepository, WardRepository $wardRepository)
    {
        $this->repository = $repository;
        $this->districtRepository = $districtRepository;
        $this->wardRepository = $wardRepository;
        $this->init();
    }

    public function getRegionOptions(Request $request)
    {
        extract($this->apiDefaultData);
        $args = $request->code?['countries.code' => $request->code]:[];
        if($options = $this->repository->join('countries', 'countries.id', '=', 'country_id')->select('regions.id', 'regions.name')->getDataOptions($args, 'Chọn Tỉnh / Thành phố', "id", "name")){
            $data = $options;
            $status = true;
        }else{
            $message = 'Không có kết quả phù hợp';
        }

        return $this->json(compact(...$this->apiSystemVars));
    }

    public function getDistrictOptions(Request $request)
    {
        extract($this->apiDefaultData);

        if($options = $this->districtRepository->getDataOptions(['region_id' => $request->region_id?$request->region_id:'-1'], 'Chọn Quận / Huyện', "id", "name")){
            $data = $options;
            $status = true;
        }else{
            $message = 'Không có kết quả phù hợp';
        }

        return $this->json(compact(...$this->apiSystemVars));
    }

    public function getWardOptions(Request $request)
    {
        extract($this->apiDefaultData);

        if($options = $this->wardRepository->getDataOptions(['district_id' => $request->district_id?$request->district_id:'-1'], 'Chọn Xã / Phường', "id", "name")){
            $data = $options;
            $status = true;
        }else{
            $message = 'Không có kết quả phù hợp';
        }

        return $this->json(compact(...$this->apiSystemVars));
    }
}
