<?php

namespace App\Http\Controllers\Apis\Payments;

use App\Http\Controllers\Apis\ApiController;

use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\Payments\PackageRepository;

class PackageController extends ApiController
{
    protected $module = 'packages';

    protected $moduleName = 'Package';

    protected $flashMode = true;

    /**
     * repository chinh
     *
     * @var PackageRepository
     */
    public $repository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(PackageRepository $repository)
    {
        $this->repository = $repository;
        $this->init();
    }

    public function beforeSave(Request $request, Arr $data) {
        $data->currency = strtoupper($data->currency??"VND");
    }

    public function getPackages(Request $request) {
        extract(self::$vars);

        $status = true;
        $return = compact(...self::$outVars);

        return $this->json(array_merge($return, $this->repository->orderBy('price', 'ASC')->mode('mask')->getResults($request)->toArray()));
    }

}
