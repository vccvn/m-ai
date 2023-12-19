<?php

namespace App\Http\Controllers\Admin\Requires;

use App\Http\Controllers\Admin\AdminController;
use App\Models\ConnectRequire;
use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\Requires\ConnectRequireRepository;

class RequireController extends AdminController
{
    protected $module = 'requires';

    protected $moduleName = 'Yêu cầu kết nối';

    protected $flashMode = true;

    /**
     * repository chinh
     *
     * @var ConnectRequireRepository
     */
    public $repository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ConnectRequireRepository $repository)
    {
        $this->repository = $repository;
        $this->init();
    }
    public function beforeGetListView(Request $request, $data)
    {
        add_js_data('requires', [
            'urls' => [
                'changeStatus' => route('admin.requires.change-status')

            ]
        ]);
    }
    public function changeStatus(Request $request)
    {
        extract($this->apiDefaultData);
        if (!($request->id??$request->id) || !($user = $this->repository->first(['id' => $request->id??$request->id]))) {
            $message = 'Người dùng không tồn tại';
        } elseif (!in_array($request->status, ConnectRequire::ALL_STATUS)) {
            $message = 'Trạng thái không hợp lệ';
        } elseif (!($userUpdate = $this->repository->update($user->id, ['status' => $request->status]))) {
            $message = 'Không thể update yêu cầu';
        } else {
            $status = true;
            $data = $userUpdate;

        }
        return $this->json(compact(...$this->apiSystemVars));
    }

}
