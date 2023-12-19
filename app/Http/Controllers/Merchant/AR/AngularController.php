<?php

namespace App\Http\Controllers\Merchant\AR;

use App\Http\Controllers\Merchant\MerchantController;

use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\AR\ModelRepository;

class AngularController extends MerchantController
{
    protected $module = '3d.models';

    protected $moduleName = 'AR Angular';

    protected $flashMode = true;

    /**
     * repository chinh
     *
     * @var ModelRepository
     */
    public $repository;
    
    /**
     * @var array $supportExtensions
     */
    protected $supportExtensions = [];
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ModelRepository $repository)
    {
        $this->repository = $repository;
        $this->init();
        $this->supportExtensions = get_3d_support_extensions();
    }

    public function getAngularEditemPage(Request $request)
    {
        if($request->secret_id && $model = $this->repository->mode('mask')->detail(['secret_id' => $request->secret_id])){
            return $this->viewModule('angular-editor', ['item'=>$model]);
        }
        $this->showError($request, 404);
    }


    // public function getItemCategories(Request $request)
    // {
    //     // extract($this->apiDefaultData);
    //     $categories = $this->categoryRepository->mode('mask')->getResults($request, [
    //         '@withItems' => 6
    //     ]);
    //     return $categories;
    // }
}
