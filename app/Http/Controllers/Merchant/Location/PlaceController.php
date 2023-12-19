<?php

namespace App\Http\Controllers\Merchant\Location;

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Merchant\MerchantController;
use App\Models\User;
use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\Locations\PlaceRepository;

class PlaceController extends MerchantController
{
    protected $module = 'location.places';

    protected $moduleName = 'Place';

    protected $flashMode = true;

    /**
     * repository chinh
     *
     * @var PlaceRepository
     */
    public $repository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(PlaceRepository $repository)
    {
        $this->repository = $repository;
        $this->init();
    }


    // public function beforeGetCrudForm($request, $config, $inputs, $data, $attributes)
    // {
    //     dd($inputs, $data, $attributes);
    // }

    public function beforeSave($request, $data, $old = null)
    {

        $owner_id = get_web_data('merchant_id');
        $data->owner_id = $owner_id;

    }

    public function beforeGetListView($request, $data)
    {
        $owner_id = get_web_data('merchant_id');
        if(($results = $data->results) && is_countable($results) && count($results) > 0) {
            foreach($results as $place){
                if($place->owner_id == $owner_id) {
                    $place->can_update_place = true;
                    // dd($place);
                    // test
                }
            }
        }
        // die(json_encode($results, JSON_PRETTY_PRINT));
    }

    /**
     * tim kiếm thông tin người dùng
     * @param Request $request
     * @return json
     */
    public function getPlacetOptions(Request $request)
    {
        extract($this->apiDefaultData);

        if ($options = $this->repository->getPlaceSelectOptions($request, ['@limit' => 10])) {
            $data = $options;
            $status = true;
        } else {
            $message = 'Không có kết quả phù hợp';
        }

        return $this->json(compact(...$this->apiSystemVars));
    }
    /**
     * tim kiếm thông tin người dùng
     * @param Request $request
     * @return json
     */
    public function createPlace(Request $request)
    {
        extract($this->apiDefaultData);
        $validator = $this->repository->validator($request, PlaceValidator::class);
        if(!$validator->success()){
            $message = 'Thông tin gửi lên không hợp lệ';
            $errors = $validator->errors();
        }
        elseif(!($created = $this->repository->create(array_merge($validator->inputs() , ['owner_id' => get_web_data('merchant_id')])))) {
            $message = 'Không thể tạo địa điểm mới';
        }else{
            $status = true;
            $data = $this->repository->getSelectOptions(['id' => $created->id]);
        }

        return $this->json(compact(...$this->apiSystemVars));
    }
}
