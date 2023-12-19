<?php

namespace App\Http\Controllers\Merchant\General;

use App\Http\Controllers\Merchant\MerchantController;
use Illuminate\Http\Request;

use App\Repositories\Options\SettingRepository;
use App\Repositories\Options\GroupRepository;
use App\Repositories\Options\DataRepository;
use Gomee\Helpers\Arr;

/**
 * @property GroupRepository $groupRepository
 * @property DataRepository $dataRepository
 */
class SettingController extends MerchantController
{
    protected $module = 'settings';

    protected $moduleName = 'Thiết lập';

    protected $flashMode = true;
    protected $groupRepository = true;
    protected $dataRepository = true;
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(GroupRepository $groupRepository, DataRepository $dataRepository)
    {
        $this->repository = $groupRepository;
        $this->groupRepository = $groupRepository;
        $this->dataRepository = $dataRepository;
        $this->init();
    }

    
    public function addViewGroupData($group)
    {
        $this->activeMenu($this->module.'.'.$group);
        // $this->activeMenu($this->module.'.list');
        $params = compact('group');
        add_js_data('setting_data', [
            'urls' => [
                'delete' => $this->getModuleRoute('item.delete', $params),
                'detail' => $this->getModuleRoute('item.detail', $params)
            ],
        ]);
        
    }


    /**
     * Hiển thị form option
     * @param Request $request
     * @param string $group
     * @return View
     */
    public function getSettingForm(Request $request, $group = null)
    {
        if(!($fdata = $this->groupRepository->getOptionFormData(['slug' => $group, 'merchant_id' => $request->user()->id]))) return $this->showError($request, 404);
        $this->submitUrl = $this->getModuleRoute('group.save',['group'=>$group]);
        $this->btnSubmitEext = 'Lưu';
        $this->addViewGroupData($group);
        if(count($fdata['inputs'])){
            foreach ($fdata['inputs'] as $key => $input) {
                if(isset($input['type'])){
                    if($input['type'] == 'maillist'){
                        $input['type'] = 'textarea';
                    }
                }
                $fdata['inputs'][$key] = $input;
            }
        }
        return $this->getForm($request, [
            'type' => 'free',
            'input_type' => 'list',
            'inputs' => $fdata['inputs'],
            'form_config' => [
                'title' => $fdata['group_label'],
                'lock_style' => true
            ]
        ],$fdata['data']);
    }

    /**
     * lằng nghe sự kiện của hàm handle khi đã hoàn thành
     * @param Request $request
     * @param Arr $data dữ liệu đã được validate
     *
     * @return void
     */
    public function done(Request $request, Arr $data)
    {
        $user_id = get_web_data('__merchant__id__');
        if(!$user_id){
            $user_id = $request->user()->id;
        }
        // lấy danh sách data có trong group với loại input là file
        $fileList = $this->groupRepository->getGroupItems(['type' => 'file', 'slug' => $request->route('group'), 'merchant_if' => $user_id]);
        if(count($fileList)){
            // dd($fileList);
            foreach($fileList as $item){
                $this->uploadAttachFile($request, $data, $item->name, get_content_path('settings/'.$user_id.'/'.$request->route('group')));
            }
        }
        // cập nhật danh sách
        $this->groupRepository->updateGroupData($request->route('group'), $data->all(), ['merchant_id' => $request->user()->id]);
        
    }


    public function updateSystemData(Request $request)
    {
        $user_id = get_web_data('__merchant__id__');
        if(!$user_id){
            $user_id = $request->user()->id;
        }

        $this->groupRepository->createNewData($user_id);
        return $this->groupRepository->get();
    }

}
