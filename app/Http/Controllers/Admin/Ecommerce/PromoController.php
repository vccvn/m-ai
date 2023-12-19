<?php

namespace App\Http\Controllers\Admin\Ecommerce;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminController;
use App\Models\Promo;
use App\Repositories\Promos\PromoRepository;

use Gomee\Helpers\Arr;
use App\Repositories\Products\ProductRefRepository;
use App\Repositories\Users\UserDiscountRepository;

class PromoController extends AdminController
{
    protected $module = 'promos';

    protected $moduleName = 'Khuyến mãi';

    protected $flashMode = true;

    /**
     * Undocumented variable
     *
     * @var PromoRepository
     */
    public $repository;
    /**
     * Undocumented variable
     *
     * @var ProductRefRepository
     */
    protected $productRefRepository;
    /**
     * Undocumented variable
     *
     * @var UserDiscountRepository
     */
    protected $userDiscountRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(PromoRepository $promoRepository, ProductRefRepository $productRefRepository, UserDiscountRepository $userDiscountRepository)
    {
        $this->repository = $promoRepository;
        $this->productRefRepository = $productRefRepository;
        $this->userDiscountRepository = $userDiscountRepository;
        $this->init();

        // add data
        add_js_data('promo_data', 'types', Promo::getTypeMapKeys());
    }

    

    /**
     * can thiệp xử lý trước khi lưu
     * @param Request $request
     * @param Promo $promo
     * @param Arr $data
     * 
     * @return void
     */
    public function beforeSave(Request $request, Arr $data)
    {
        $times = $data->times;
        $data->merge([
            'started_at' => $times['from']??date('Y-M-d H:i:s'),
            'finished_at' => $times['to']??date('Y-M-d H:i:s', time()+3600*24*2),
            
        ]);
    }


    /**
     * lưu các dữ liệu liên quan
     * @param Illuminate\Http\Request $request
     * @param App\Models\Promo $promo
     * @param Crazy\Helpers\Arr $data
     * 
     * @return void
     */
    public function afterSave(Request $request, $promo, Arr $data)
    {
        $this->productRefRepository->updateProductRef('promo', $promo->id, $data->products??[]);
        if($data->scope == 'user'){
            $this->userDiscountRepository->updateUserDiscounts($promo->id, $data->quantity_per_user, $data->user_list);
        }
    }


    /**
     * tim kiếm thông tin sản phẩm
     * @param Request $request
     * @return json
     */
    public function getPromoSelectOptions(Request $request)
    {
        extract($this->apiDefaultData);

        if($options = $this->repository->getPromoSelectOptions($request, ['@limit'=>10])){
            $data = $options;
            $status = true;
        }else{
            $message = 'Không có kết quả phù hợp';
        }

        return $this->json(compact(...$this->apiSystemVars));
    }

 
}
