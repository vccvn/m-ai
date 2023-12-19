<?php

namespace App\Http\Controllers\Admin\Ecommerce;

use App\Http\Controllers\Admin\AdminController;

use Illuminate\Http\Request;
use Gomee\Helpers\Arr;
use App\Repositories\Products\ReviewRepository;

class ProductReviewController extends AdminController
{
    protected $module = 'products.reviews';

    protected $moduleName = 'Đánh giá sàn phẩm';

    protected $flashMode = true;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ReviewRepository $reviewRepository)
    {
        $this->repository = $reviewRepository;
        $this->init();
        $this->activeMenu('products');
        $this->addHeaderButtons('create');
    }

    
    public function start()
    {
        // $this->activeMenu($this->module.'.list');
        add_js_data('review_urls', [
            
            'change_approve_url' => $this->getModuleRoute('change-approve')
        
        ]);
    }

    public function changeApprove(Request $request)
    {
        extract($this->apiDefaultData);
        $approved = $request->approved?1:0;
        $d = ['approved' => $approved];
        if($approved) $d['approved_id'] = $request->user()->id;
        // return $d;
        if($request->id && $detail = $this->repository->update($request->id, $d)){
            $data = $detail;
            $status = true;
        }
        else{
            $message = 'Không tìm thấy comment';
        }
        return $this->json(compact(...$this->apiSystemVars));
    }

}
