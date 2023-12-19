<?php

namespace App\Repositories\Payments;

use App\Masks\Payments\MethodCollection;
use App\Masks\Payments\MethodMask;
use App\Models\PaymentMethod;
use Gomee\Helpers\Arr;
use Gomee\Repositories\BaseRepository;
/**
 * @method PaymentMethodCollection<PaymentMethodMask>|PaymentMethod[] filter(Request $request, array $args = []) lấy danh sách PaymentMethod được gán Mask
 * @method PaymentMethodCollection<PaymentMethodMask>|PaymentMethod[] getFilter(Request $request, array $args = []) lấy danh sách PaymentMethod được gán Mask
 * @method PaymentMethodCollection<PaymentMethodMask>|PaymentMethod[] getResults(Request $request, array $args = []) lấy danh sách PaymentMethod được gán Mask
 * @method PaymentMethodCollection<PaymentMethodMask>|PaymentMethod[] getData(array $args = []) lấy danh sách PaymentMethod được gán Mask
 * @method PaymentMethodCollection<PaymentMethodMask>|PaymentMethod[] get(array $args = []) lấy danh sách PaymentMethod
 * @method PaymentMethodCollection<PaymentMethodMask>|PaymentMethod[] getBy(string $column, mixed $value) lấy danh sách PaymentMethod
 * @method PaymentMethodMask|PaymentMethod getDetail(array $args = []) lấy PaymentMethod được gán Mask
 * @method PaymentMethodMask|PaymentMethod detail(array $args = []) lấy PaymentMethod được gán Mask
 * @method PaymentMethodMask|PaymentMethod find(integer $id) lấy PaymentMethod
 * @method PaymentMethodMask|PaymentMethod findBy(string $column, mixed $value) lấy PaymentMethod
 * @method PaymentMethodMask|PaymentMethod first(string $column, mixed $value) lấy PaymentMethod
 * @method PaymentMethod create(array $data = []) Thêm bản ghi
 * @method PaymentMethod update(integer $id, array $data = []) Cập nhật
 *
 * @property PaymentMethod $_model Model
 */
class MethodRepository extends BaseRepository
{
    /**
     * class chứ các phương thức để validate dử liệu
     * @var string $validatorClass
     */
    protected $validatorClass = 'Payments\MethodValidator';

    /**
     * tên class mặt nạ. Thường có tiền tố [tên thư mục] + \ vá hậu tố Mask
     *
     * @var string
     */
    protected $maskClass = MethodMask::class;

    /**
     * tên collection mặt nạ
     *
     * @var string
     */
    protected $maskCollectionClass = MethodCollection::class;

    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\PaymentMethod::class;
    }

    public function init()
    {
        $this->defaultSortBy = [
            'payment_methods.priority' => 'ASC'
        ];
    }

    /**
     * lay cac phuong thuc duoc kich hoat
     *
     * @param array $args
     * @return array|\App\Models\PaymentMethod[]
     */
    public function getActivatedMethods($args = [])
    {
        $a = array_merge(['status' => PaymentMethod::ACTIVE], $args);
        return $this->addDefaultParam('trashed_status', 0)->orderBy('priority', 'ASC')->getData($a);
    }

    public function getActiveOptions()
    {
        $data = [];
        if(count($list = $this->getActivatedMethods())){
            foreach ($list as $index => $method) {
                $data[$method->id] = htmlentities($method->name);
            }
        }
        return $data;
    }


    /**
     * lấy các phương thức kèm chi tiết
     *
     * @param array $args
     * @return array|\Crazy\Helpers\Arr[]|\App\Models\PaymentMethod[]
     */
    public function getActionedMethodDetails($args = [])
    {
        $arr = [];
        if(count($mths = $this->getActivatedMethods($args)) && $methods = get_payment_config('methods')){

            foreach ($mths as $i => $method) {
                if(isset($methods[$method->method])){
                    $m = $methods[$method->method];

                    $title = $method->name?$method->name:(isset($m['name'])?$m['name']:$method->method);
                    $d = new Arr($method->toArray());
                    $d->title = $title;
                    $d->name = $title;
                    $d->value = $method->method;
                    $d->id = $method->id;
                    $detail = [];
                    if($method->config && is_array($method->config)){
                        foreach ($method->config as $key => $value) {
                            $detail[] = new Arr([
                                'label' => isset($m[$key])?$m[$key]['label']:$key,
                                'name' => $key,
                                'value' => $value,

                            ]);
                        }
                    }
                    $d->detail = $detail;
                    $cd = [];
                    if(array_key_exists('data', $m)){
                        if($m['data'] && is_countable($m['data']) && count($m['data'])){
                            foreach ($m['data'] as $key => $value) {
                                $cd[$key] = new Arr($value);
                            }
                        }
                    }
                    if(array_key_exists('icon', $m)){
                        $d->icon = asset('static/payments/portal/'.$m['icon']);
                    }
                    $d->configData = new Arr($cd);
                    $d->defaultValues = array_key_exists('default_values', $m)?$m['default_values'] : [];
                    $arr[$d->id] = $d;

                }
            }

            return new Arr($arr);
        }
        return $arr;
    }

    /**
     * lấy các phương thức kèm chi tiết
     *
     * @param array $args
     * @return array|\Crazy\Helpers\Arr[]|\App\Models\PaymentMethod[]
     */
    public function getActionedMethodList($args = [])
    {
        $arr = [];
        if(count($mths = $this->getActivatedMethods($args)) && $methods = get_payment_config('methods')){

            foreach ($mths as $i => $method) {
                if(isset($methods[$method->method])){
                    $m = $methods[$method->method];

                    $title = $method->name?$method->name:(isset($m['name'])?$m['name']:$method->method);
                    $d = new Arr();
                    $d->title = $title;
                    $d->name = $title;
                    $d->method = $method->method;
                    $d->id = $method->id;
                    if(array_key_exists('icon', $m)){
                        $d->icon = asset('static/payments/portal/'.$m['icon']);
                    }
                    $arr[] = $d;

                }
            }

        }
        return $arr;
    }
}
