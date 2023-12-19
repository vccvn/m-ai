<?php

namespace App\Http\Controllers\Admin\Location;

use App\Http\Controllers\Admin\AdminController;

use Illuminate\Http\Request;
use Gomee\Helpers\Arr;
use App\Repositories\Locations\RegionRepository;
use App\Repositories\Locations\DistrictRepository;
use App\Repositories\Locations\WardRepository;


class LocationController extends AdminController
{
    protected $module = 'location';

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

        if($options = $this->repository->getDataOptions([], 'Chọn Tỉnh / Thành phố')){
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

        if($options = $this->districtRepository->getDataOptions(['region_id' => $request->region_id?$request->region_id:'-1'], 'Chọn Quận / Huyện')){
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

        if($options = $this->wardRepository->getDataOptions(['district_id' => $request->district_id?$request->district_id:'-1'], 'Chọn Xã / Phường')){
            $data = $options;
            $status = true;
        }else{
            $message = 'Không có kết quả phù hợp';
        }

        return $this->json(compact(...$this->apiSystemVars));
    }


    public function getRegionData(Request $request)
    {
        extract($this->apiDefaultData);

       $status = true;
       $data = $this->repository->getRegionData();

        return $this->json(compact(...$this->apiSystemVars));
    }
}
