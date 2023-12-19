<?php

namespace App\Http\Controllers\Admin\General;

use App\DTOs\Users\UserDTO;
use App\Http\Controllers\Admin\AdminController;
use App\Models\User;
use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\Users\UserRepository;
use App\Validators\Users\AgentValidator;

class AgentController extends AdminController
{
    protected $module = 'users.agent';

    protected $moduleName = 'Agent';

    protected $flashMode = true;

    /**
     * repository chinh
     *
     * @var UserRepository
     */
    public $repository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
        $this->repository->setValidatorClass(AgentValidator::class);
        $this->activeMenu('users');
        $this->activeMenu('users.agent');
        $this->activeMenu('admin.users');
        $this->activeMenu('admin.users.agent');
        $this->init();
    }

    public function beforeGetListData($request)
    {
        $this->repository->whereIn('users.type', User::AGENT_LIST);
    }
    public function beforeGetListView(Request $request, $data)
    {
        add_js_data('users', [
            'urls' => [
                'changeStatus' => route('admin.users.change-status')

            ]
        ]);
    }

    /**
     * can thiệp trước khi luu
     * @param Request $request
     * @param UserDTO $data dũ liệu đã được validate
     * @return void
     */
    protected function beforeSave(Request $request, $data, $user = null)
    {
        if($data->reset_discount == 'reset' && $agent_discount = discount_setting($data->type))
            $data->agent_discount = $agent_discount;
    }
}
