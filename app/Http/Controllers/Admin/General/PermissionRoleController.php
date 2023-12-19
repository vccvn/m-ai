<?php

namespace App\Http\Controllers\Admin\General;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminController;

use App\Repositories\Permissions\RoleRepository;

use App\Repositories\Permissions\UserRoleRepository;
use App\Repositories\Users\UserRepository;
use App\Services\Permissions\PermissionService;
use Gomee\Helpers\Arr;
use Illuminate\Http\JsonResponse;

/**
 * permission controller
 * @property UserRepository $userRepository
 * @method JsonResponse getUserTags(Request $request)
 */
class PermissionRoleController extends AdminController
{
    protected $module = 'permissions.roles';

    protected $moduleName = 'Quyền truy cập';

    protected $data = [];


    /**
     * permission service
     *
     * @var PermissionService
     */
    protected $service = null;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(RoleRepository $RoleRepository, UserRoleRepository $UserRoleRepository, PermissionService $permissionService, UserRepository $userRepository)
    {
        $this->repository = $RoleRepository;
        $this->userRoles = $UserRoleRepository;
        $this->service = $permissionService;
        $this->userRepository = $userRepository;
        $this->init();
        admin_breadcrumbs([
            [
                'text' => 'Thêm quyền',
                'url' => route($this->routeNamePrefix.'permissions.roles.create')
            ]
        ]);
    }

    public function afterSave($request, $result, $data)
    {
        $this->userRoles->updateUserRole($result->id, is_array($data->users)?$data->users:[]);
        $r = $this->service->updateModuleRoleMatrix($result->id, $data->modules);
        // dd($r);
    }

    /**
     * cap nhat thong tin role user
     */
    public function saveUserRole(Request $request)
    {
        extract($this->apiDefaultData);

        $validator = $this->userRoles->validator($request);
        if(!$validator->success()){
            $message = "Đã có lỗi xảy ra. Vui lòng kiểm tra lại!";
            $errors = $validator->errors();
        }else{
            $this->userRoles->updateUserRole($request->role_id, is_array($request->users)?$request->users:[]);
            $status = true;
            $message = "Cập nhật thành công!";
        }
        return $this->json(compact(...$this->apiSystemVars));
    }


    /**
     * tim kiếm thông tin người dùng
     * @param Request $request
     * @return json
     */
    public function getUserTags(Request $request)
    {
        extract($this->apiDefaultData);

        if($options = $this->userRepository->getUserTagData($request, ['@limit'=>10])){
            $data = $options;
            $status = true;
        }else{
            $message = 'Không có kết quả phù hợp';
        }

        return $this->json(compact(...$this->apiSystemVars));
    }
}
