<?php

namespace App\Http\Controllers\Admin\General;

use App\Http\Controllers\Admin\AdminController;

use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\Notices\NoticeRepository;

class NoticeController extends AdminController
{
    protected $module = 'notices';

    protected $moduleName = 'Notice';

    /**
     * route chuyển hướng sau khi lưu
     * @var string $redirectRoute
     */
    protected $redirectRoute = 'notices.list';

    protected $flashMode = true;

    /**
     * repository chinh
     *
     * @var NoticeRepository
     */
    public $repository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(NoticeRepository $repository)
    {
        $this->repository = $repository;
        $this->init();
    }

    /**
     * can thiep truoc khi sho form
     * @param Request $request
     * @param Arr $config
     * @param Arr $inputs
     * @param Arr $data
     * @param Arr $attrs ($form_attrs)
     * không dùng đến thì ko cần khai báo cho đỡ mệt
     * nhưng nếu dùng thì phải khai báo theo thứ tự
     * @return void
     */
    public function beforeGetCrudForm()
    {
        # gọi hàm them dường dẫn js
        add_js_src('static/manager/js/notice.js');
    }



    /**
     * can thiep truoc khi luu
     * @param Request $request
     * @param Arr $data
     *
     * @return void
     */
    public function BeforeSave(Request $request, Arr $data)
    {
        if(strtolower($data->type) != 'personal'){
            $data->to_id = 0;
        }
        if(strtolower($data->type) != 'group'){
            $data->to_group = 0;
        }
        if(!$request->id??$request->id){
            $data->created_by = $request->user()->id;
        }
    }

    /**
     * can thiep sau khi luu
     * @param Request $request
     * @param Model $result
     *
     * @return void
     */
    public function afterSave(Request $request, $result)
    {
        if(strtolower($result->type) == 'personal'){
            // $this->repository->incUserNoticeBadge($request->to_id, 1);
        }else {

        }
    }

    /**
     * lay thong bao tra ve json
     * @param Request $request
     */
    public function getUserNotices(Request $request)
    {
        $this->repository->prepareGetUserNotices($request);
        return $this->getAjaxData($request,['@orderBy'=>['created_at', 'desc']]);
    }


}
