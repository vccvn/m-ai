<?php

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Admin\AdminController;
use App\Models\User;
use Illuminate\Http\Request;
use Gomee\Helpers\Arr;


use App\Repositories\Web\SettingRepository;
use App\Repositories\Options\SettingRepository as OptionSettings;
// use App\Repositories\Html\AreaRepository;

use App\Repositories\Users\OwnerRepository;

class InstallController extends AdminController
{
    protected $module = 'install';

    protected $moduleName = 'Install';

    protected $flashMode = true;

    /**
     * repository chinh
     *
     * @var OwnerRepository
     */
    public $repository;



    public $is_created = false;

    /**
     * Undocumented variable
     *
     * @var OptionSettings
     */
    public $OptionSettings;
    /**
     * Undocumented variable
     *
     * @var OptionSettings
     */
    public $optionRepository;
    // /**
    //  * Undocumented variable
    //  *
    //  * @var AreaRepository
    //  */
    // public $htmlAreaRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        OwnerRepository $repository,
        OptionSettings $OptionSettings
        )
    {
        $this->repository = $repository;
        $this->repository->setValidatorClass('Users\AdminValidator');
        $this->repository->enableManagerQuery();
        $this->optionRepository = $OptionSettings;
        $this->repository
            ->setController($this)
            ->setRepositories(
                $OptionSettings
            );
        $this->init();
    }

    public function getInstallForm(Request $request)
    {
        return $this->viewModule('index');
    }



    /**
     * lấy mảng thông tin khởi tạo
     *
     * @param array $data
     * @return array
     */
    public function getDataDefault($data = [])
    {
        $data['username'] = $this->repository->getUsernameByEmail($data['email']);
        $data = array_merge($data, [
            "type" => "admin",
            "status" => 1,

            "name" => $data['username']
        ]);
        return $data;
    }

    /**
     * tạo user
     *
     * @param array $data
     * @return User|null
     */
    public function createUser($data)
    {
        $user = $this->repository->create($data);
        if ($user) {
            $this->optionRepository->createNewData();
        }
        return $user;
    }

    /**
     * đăng nhập
     * @param Request
     * @return json
     */
    public function install(Request $request)
    {
        extract($this->apiDefaultData);
        $validator = $this->repository->validator($request, 'Users/Admin');
        if (!$validator->success()) {
            $message = "Thông tin không hợp lệ!";
            $errors = $validator->errors();
        } elseif (!($inputData = $this->getDataDefault($validator->inputs())) || !($user = $this->createUser($inputData))) {
            $message = "Không thể tạo thông tin user";
        } elseif (!$this->repository->createOwnerData($user, $inputData, true)) {
            $message = 'Lỗi không xác định';
        } else {
            $data = ['redirect' => route('home')];
            $status = true;
            $message = 'Thiết lập thành công';
        }
        return $this->json(compact(...$this->apiSystemVars));
    }
}
