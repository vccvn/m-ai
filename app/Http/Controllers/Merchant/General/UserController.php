<?php

namespace App\Http\Controllers\Merchant\General;

use App\DTOs\Users\UserDTO;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Merchant\MerchantController;
use App\Models\User;
use App\Repositories\Accounts\AgentRepository;
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
    public function __construct(UserRepository $UserRepository, UserService $service, protected AgentRepository $agentRepository)
    {
        $this->repository = $UserRepository;
        $this->service = $service;
        $this->init();
    }


    public function beforeGetListData(Request $request)
    {
        $user = $request->user();
        $vendor_id = get_web_data('merchant_id',  $user->id);
        // $data->vendor_id = get_web_data('merchant_id',  $vendor_id);
        $this->repository->where('users.agent_id', $vendor_id);
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

    public function beforeCreate($request, $data)
    {
        $user = $request->user();
        $data->ref_code = $user->affiliate_code;
        $data->agent_id = $user->id;
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
}
