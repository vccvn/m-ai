<?php

namespace App\Repositories\Promos;

use App\Models\Promo;
use App\Repositories\Users\UserDiscountRepository;
use App\Validators\Promos\PromoValidator;
use Gomee\Helpers\Arr;
use Gomee\Repositories\BaseRepository;
use Illuminate\Http\Request;
use JetBrains\PhpStorm\NoReturn;

class PromoRepository extends BaseRepository
{
    /**
     * class chứ các phương thức để validate dử liệu
     * @var string $validatorClass 
     */
    protected $validatorClass = PromoValidator::class;


    /**
     * tên class mặt nạ. Thược có tiền tố [tên thư mục] + \ vá hậu tố Mask
     *
     * @var string
     */
    protected $maskClass = 'Promos\PromoMask';

    /**
     * tên collection mặt nạ
     *
     * @var string
     */
    protected $maskCollectionClass = 'Promos\PromoCollection';

    /**
     * Undocumented variable
     *
     * @var UserDiscountRepository
     */
    protected $userDiscountRepository;


    public $actionStatus = false;
    public $actionMessage = "Thao tác thành công";

    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\Promo::class;
    }

    public function init()
    {
        $this->userDiscountRepository = app(UserDiscountRepository::class);
    }

    /**
     * lấy option còn hiệu lực
     *
     * @param array $args
     * @return array
     */
    public function getPromoAvailableOptions($args = [])
    {
        $this->whereDate('finished_at', '>=', date('Y-m-d'));
        $this->withCount('productRefs');
        $data = ['Chọn một chương trình khuyến mãi'];
        if (count($promos = $this->notTrashed()->get($args))) {
            foreach ($promos as $promo) {
                $data[$promo->id] = htmlentities($promo->code . ' - ' . $promo->name . ' (' . $promo->productRefs_count . ' sản phẩm)');
            }
        }
        return $data;
    }

    /**
     * cập nhật danh sách user dc nhận promo
     * @param int $promo_id
     * @param array $user_id_list
     * @return void
     */
    public function updatePromoUsers(int $promo_id, array $user_id_list = [])
    {
        $ingore = [];
        if (count($users = $this->getBy('role_id', $promo_id))) {
            foreach ($users as $user) {
                // nếu role nằm trong số id them thì bỏ qua
                if (in_array($user->user_id, $user_id_list)) $ingore[] = $user->user_id;
                // nếu ko thì xóa
                else $user->delete();
            }
        }
        if (count($user_id_list)) {
            foreach ($user_id_list as $user_id) {
                if (!in_array($user_id, $ingore)) {
                    // nếu ko nằm trong danh sách bỏ qua thì ta thêm mới
                    $this->save(compact('user_id', 'role_id'));
                }
            }
        }
    }



    /**
     * kiểm tra promo
     *
     * @param mixed $couponCode
     * @param integer $user_id
     * @return Promo
     */
    public function checkPromo($coupon_code, $user_id = 0)
    {
        if (!$coupon_code || !($currentTime = time())) {
            $this->actionMessage = "Bạn chưa nhập mã giảm giá";
        } elseif (!($promo = $this->first(['code' => $coupon_code]))) {
            $this->actionMessage = "Mã giảm giá không tồn tại";
        } elseif ($promo->started_at && $currentTime < strtotime($promo->started_at)) {
            $this->actionMessage = "Mã giảm giá hiện chưa có hiệu lục";
        } elseif ($promo->finished_at && $currentTime > strtotime($promo->finished_at)) {
            $this->actionMessage = "Mã giảm giá này hiện đã hết hiệu lục";
        } elseif (!$promo->is_activated) {
            $this->actionMessage = "Mã giảm giá này không hiệu lục";
        } elseif ($promo->scope == 'product') {
            $this->actionMessage = "Mã giảm giá này không áp dụng cho đơn hàng";
        } elseif ($promo->scope == 'user' && (!$user_id || !($userDiscount = $this->userDiscountRepository->first(['user_id' => $user_id, 'discount_id' => $promo->id])))) {
            $this->actionMessage = "Mã giảm giá này không dành cho bạn";
        } elseif ($promo->scope == 'user' && ($userDiscount->usage >= $userDiscount->total)) {
            $this->actionMessage = "Bạn đã dùng hết lượt khuyến mãi của mả giảm giá này";
        } elseif ($promo->scope == 'order' && $promo->usage_total >= $promo->limited_total) {
            $this->actionMessage = "Mã khuyến mãi này đã được sử dụng hết số lượng";
        } else {
            $this->actionStatus = true;
            return $promo;
        }
        return false;
    }

    public function getUserAvailablePromos($user_id)
    {
        if (!$user_id) return $this->maskCollection([], 0);
        return $this->mode('mask')
            ->join('user_discounts', 'user_discounts.discount_id', '=', 'promos.id')
            ->whereColumn('user_discounts.usage', '<', 'user_discounts.total')
            ->where('user_discounts.user_id', $user_id)
            ->where('is_activated', Promo::ACTIVATED)
            ->where('promos.started_at', '<=', CURRENT_TIME_STRING)
            ->where('promos.finished_at', '>=', CURRENT_TIME_STRING)
            ->select('promos.*', 'user_discounts.usage', 'user_discounts.total')
            ->getData([]);
    }


    public function downPromoTotal($code, $user_id = null)
    {
        if (!$code) {
            $this->actionMessage = "Bạn chưa nhập mã giảm giá";
        } elseif (!($promo = $this->first(['code' => $code]))) {
            $this->actionMessage = "Mã giảm giá không tồn tại";
        } elseif ($promo->scope == 'user') {
            if ((!$user_id || !($userDiscount = $this->userDiscountRepository->first(['user_id' => $user_id, 'discount_id' => $promo->id])))) {
                $this->actionMessage = "Mã giảm giá này không dành cho bạn";
            } else {
                $userDiscount->usage++;
                $userDiscount->save();
                $promo->usage_total++;
                $promo->save();
                return $promo;
            }
        } elseif ($promo->scope == 'order') {
            $promo->usage_total++;
            $promo->save();
            return $promo;
        } else {
            $this->actionStatus = true;
        }
        return false;
    }

    /**
     * get Product option
     * @param Request $request
     * @param array $args
     * @return array
     */
    public function getPromoSelectOptions($request, array $args = [])
    {
        if ($request->ignore && is_array($request->ignore)) {
            $this->whereNotIn('promos.id', $request->ignore);
        }
        $data = [];
        if ($list = $this->notTrashed()->getFilter($request, $args)) {
            foreach ($list as $item) {
                $data[$item->id] = htmlentities("$item->code - $item->name");
            }
        }
        return $data;
    }

    /**
     * get Product option
     * @param Request $request
     * @param array $args
     * @return array
     */
    public function getPromoOptions(array $args = [])
    {
        $data = [];
        if ($list = $this->notTrashed()->get($args)) {
            foreach ($list as $item) {
                $data[$item->id] = htmlentities("$item->code - $item->name");
            }
        }
        return $data;
    }

    public function beforeFilter($request)
    {
        // nếu có date range và date range hợp lệ thì sẽ thêm vào query
        if ($request->daterange && $date = get_date_range($request->daterange)) {
            $from = $date['from'];
            $to = $date['to'];
            $this->where(function ($query) use ($from, $to) {
                $query->where(function ($query) use ($from, $to) {
                    $query->whereDate('promos.finished_at', '>=', "$from[year]-$from[month]-$from[day]")
                        ->whereDate('promos.finished_at', '<=', "$to[year]-$to[month]-$to[day]");
                })
                    ->orWhere(function ($query) use ($from, $to) {
                        $query->whereDate('promos.started_at', '>=', "$from[year]-$from[month]-$from[day]")
                            ->whereDate('promos.started_at', '<=', "$to[year]-$to[month]-$to[day]");
                    });
            });
        }
    }
}
