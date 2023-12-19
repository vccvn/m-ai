<?php

namespace App\Http\Controllers\Merchant\General;

use App\DTOs\Users\UserDTO;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Merchant\MerchantController;
use App\Models\User;
use Gomee\Helpers\Arr;
use App\Repositories\Users\UserRepository;
use App\Services\Encryptions\HashService;
use App\Services\Mailers\Mailer;
use App\Services\Users\UserService;
use Carbon\Carbon;

/**
 * user
 * @property UserRepository $repository
 * @property UserService $service
 * @property User $user
 */
class UserController extends MerchantController
{
    protected $module = 'users';

    protected $moduleName = 'Người dùng';

    protected $data = [];

    protected $user = null;

    protected $flashMode = true;

    protected $listType = null;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserRepository $UserRepository, UserService $service)
    {
        $this->repository = $UserRepository;
        $this->service = $service;
        $this->init();
    }


    public function beforeGetListData(Request $request)
    {
        $user = $request->user();
        $vendor_id = get_web_data('merchant_id',  $user->type == User::MERCHANT ? $user->id : $user->owner_id);
        // $data->vendor_id = get_web_data('merchant_id',  $vendor_id);
        $this->repository->where('users.owner_id', $vendor_id);
    }

    public function beforeGetListView(Request $request, $data)
    {
        add_js_data('users', [
            'urls' => [
                'changeStatus' => route('merchant.users.change-status')

            ]
        ]);
        add_js_data('user_custom_urls', [
            'reset2fa_url' => $this->getModuleRoute('reset2fa')
        ]);
    }

    public function beforeUpdate($request, $data, $model)
    {
        $this->user = $model;
    }

    /**
     * can thiệp trước khi luu
     * @param Request $request
     * @param UserDTO $data dũ liệu đã được validate
     * @return void
     */
    protected function beforeSave(Request $request, $data)
    {
        $this->uploadImageAttachFile($request, $data, 'avatar', get_content_path('users/avatar'));
        $trust_score = 0;
        if ($data->is_verified_phone)
            $trust_score += User::TRUST_PHONE_SCORE;
        if ($data->is_verified_email)
            $trust_score += User::TRUST_EMAIL_SCORE;
        if ($data->is_verified_identity)
            $trust_score += User::TRUST_EKYC_SCORE;
        if ($this->user && $this->user->hasPayment)
            $trust_score += User::TRUST_PAY_SCORE;
        $data->trust_score = $trust_score;

        if ($this->user) {
            if ($this->user->type != $data->type && in_array($data->type, [User::MERCHANT, User::AGENT_LV1, User::AGENT_LV2])) {
                $data->agent_expired_at = Carbon::now()->addMonths(discount_setting($data->type.'_contract_renew', 1))->toDateTimeString('millisecond');
            }
        }elseif (in_array($data->type, [User::MERCHANT, User::AGENT_LV1, User::AGENT_LV2])) {
                $data->agent_expired_at = Carbon::now()->addMonths(discount_setting($data->type.'_contract_renew', 1))->toDateTimeString('millisecond');
        }

        $data->account_data = $data->copy([
            "bank_name",
            "bank_account_name",
            "bank_account_number"
        ]);
    }

    /**
     * can thiệp sau khi luu
     * @param Request $request
     * @param Model $model dũ liệu đã được luu
     * @return void
     */
    protected function afterSave(Request $request, $model)
    {
        // luu thong tin pro file
        // $this->profiles->saveProfile($model->id, $this->data);

    }

    public function changeStatus(Request $request)
    {
        extract($this->apiDefaultData);
        if (!$request->id ?? $request->id || !($user = $this->repository->find($request->id ?? $request->id))) {
            $message = 'Người dùng không tồn tãi';
        } elseif (!in_array($request->status, User::STATUS_LIST)) {
            $message = 'Trạng thái không hợp lệ';
        } elseif (!($userUpdate = $this->repository->update($user->id, ['status' => $request->status]))) {
            $message = 'Không thể update user';
        } else {

            $status = true;
            $data = $userUpdate;

            if ($request->status == -1 && $user->status != -1) {
                Mailer::to($user->email, $user->name)
                    ->subject('Tài khoản của bạn đang bị tạm khóa')
                    ->body('mails.block-account')
                    ->data([
                        'name' => $user->name
                    ])
                    ->send();
            }
        }
        return $this->json(compact(...$this->apiSystemVars));
    }

    public function reset2fa(Request $request)
    {
        extract($this->apiDefaultData);

        if (!$request->id ?? $request->id || !($user = $this->repository->first(['id' => $request->id ?? $request->id]))) {
            $message = 'User không tồn tại';
        } else {
            $user->google2fa_secret = null;
            $user->save();
            $status = true;
        }

        return $this->json(compact(...$this->apiSystemVars));
    }

    /**
     * tim kiếm thông tin người dùng
     * @param Request $request
     * @return json
     */
    public function getUserSelectOptions(Request $request)
    {
        extract($this->apiDefaultData);

        if ($options = $this->repository->getUserSelectOptions($request, ['@limit' => 10])) {
            $data = $options;
            $status = true;
        } else {
            $message = 'Không có kết quả phù hợp';
        }

        return $this->json(compact(...$this->apiSystemVars));
    }
    public function getMerchantSelectOptions(Request $request)
    {
        extract($this->apiDefaultData);

        if ($options = $this->repository->where('users.type', User::MERCHANT)->getUserSelectOptions($request, ['@limit' => 10])) {
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
    public function getUserTagData(Request $request)
    {
        extract($this->apiDefaultData);

        if ($options = $this->repository->getUserTagData($request, ['@limit' => 10])) {
            $data = $options;
            $status = true;
        } else {
            $message = 'Không có kết quả phù hợp';
        }

        return $this->json(compact(...$this->apiSystemVars));
    }

    public function updateEncData(Request $request)
    {
        $data = [];
        if (count($users = $this->repository->get())) {
            foreach ($users as $i => $user) {
                // if ($user->ci_card_number && strlen($user->ci_card_number) < 20) {
                //     $user->ci_card_number = HashService::encrypt($user->ci_card_number);
                //     if ($user->ci_card_front_scan && $fsc = $this->service->encryptCIScan($user->ci_card_front_scan)) {
                //         $user->ci_card_front_scan = $fsc;
                //     }
                //     if ($user->ci_card_back_scan && $bsc = $this->service->encryptCIScan($user->ci_card_back_scan)) {
                //         $user->ci_card_back_scan = $bsc;
                //     }
                //     $user->save();
                //     echo "Đã mã hoá thông tin user $user->id \n";

                // }else
                if ($user->ci_card_number && strlen($user->ci_card_number) > 40) {
                    $ci = HashService::decrypt($user->ci_card_number);
                    $user->ci_card_number = HashService::decrypt($user->ci_card_number);
                    if (strlen($ci) > 40) {
                        $ci = HashService::decrypt($ci);
                        $user->ci_card_number = $ci;

                        $user->save();
                    }
                }
                // $data[] = [
                //     'name' => $user->name,
                //     'username' => $user->username,
                //     'ci_card_number' => $user->get,
                //     'ci_card_front_scan' => $user->ci_card_front_scan,
                //     'ci_card_back_scan' => $user->ci_card_back_scan,

                // ];
            }
        }
        return $data;
    }
}
