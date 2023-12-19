<?php

namespace App\Http\Controllers\Apis\Payments;

use App\Http\Controllers\Apis\ApiController;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\Payments\MethodRepository;

class MethodController extends ApiController
{
    protected $module = 'methods';

    protected $moduleName = 'Method';

    protected $flashMode = true;

    /**
     * repository chinh
     *
     * @var MethodRepository
     */
    public $repository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(MethodRepository $repository)
    {
        $this->repository = $repository;
        $this->init();
    }

    public function getActiveMethods(Request $request){
        extract(self::$vars);
        $status = true;

        $data = $this->repository->getActionedMethodList();
        // dd($results);
        return $this->json(compact(...self::$outVars));
    }
}
