<?php

namespace App\Http\Controllers\Admin\General;

use App\DTOs\Users\UserDTO;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminController;
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
class UserController extends AdminController
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

    public function getCIPendingStatus(Request $request)
    {
        $this->repository->enableCIQuery();
        return $this->getList($request);
    }


    public function beforeGetListView(Request $request, $data)
    {
        add_js_data('users', [
            'urls' => [
                'changeStatus' => route('admin.users.change-status')

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
        // $trust_score = 0;
        // if ($data->is_verified_phone)
        //     $trust_score += User::TRUST_PHONE_SCORE;
        // if ($data->is_verified_email)
        //     $trust_score += User::TRUST_EMAIL_SCORE;
        // if ($data->is_verified_identity)
        //     $trust_score += User::TRUST_EKYC_SCORE;
        // if ($this->user && $this->user->hasPayment)
        //     $trust_score += User::TRUST_PAY_SCORE;
        // $data->trust_score = $trust_score;

        // if ($this->user) {
        //     if ($this->user->type != $data->type && in_array($data->type, [User::MERCHANT, User::AGENT_LV1, User::AGENT_LV2])) {
        //         $data->agent_expired_at = Carbon::now()->addMonths(discount_setting($data->type.'_contract_renew', 1))->toDateTimeString('millisecond');
        //     }
        // }
        // elseif (in_array($data->type, [User::MERCHANT, User::AGENT_LV1, User::AGENT_LV2])) {
        //         $data->agent_expired_at = Carbon::now()->addMonths(discount_setting($data->type.'_contract_renew', 1))->toDateTimeString('millisecond');
        // }

        // $data->account_data = $data->copy([
        //     "bank_name",
        //     "bank_account_name",
        //     "bank_account_number"
        // ]);
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
        if (!$request->uuid ?? $request->id || !($user = $this->repository->find($request->uuid ?? $request->id))) {
            $message = 'Người dùng không tồn tãi';
        } elseif (!in_array($request->status, User::STATUS_LIST)) {
            $message = 'Trạng thái không hợp lệ';
        } elseif (!($userUpdate = $this->repository->update($user->id, ['status' => $request->status]))) {
            $message = 'Không thể update user';
        } else {

            $status = true;
            $data = $userUpdate;

            if ($request->status == -1 && $user->status != -1) {
                Mailer::to($user->email, $user->full_name)
                    ->subject('Tài khoản của bạn đang bị tạm khóa')
                    ->body('mails.block-account')
                    ->data([
                        'name' => $user->full_name
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

}
