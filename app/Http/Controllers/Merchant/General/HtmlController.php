<?php

namespace App\Http\Controllers\Merchant\General;

use App\Http\Controllers\Merchant\MerchantController;

use Illuminate\Http\Request;
use Gomee\Helpers\Arr;


class HtmlController extends MerchantController
{
    protected $module = 'forms';

    protected $moduleName = 'Html';

    protected $flashMode = true;

    /**
     * repository chinh
     *
     * @var HtmlRepository
     */
    public $repository;
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->init();
    }

    public function getHtmlInput(Request $request)
    {
        extract($this->apiDefaultData);

        $input = $request->config??($request->input_config??$request->params);
        if(!is_array($input) || !array_key_exists('type', $input) || !$input['type']) $message = "cấu hình input không hợp lệ";
        else{
            $status = true;
            if($request->value) $input['value'] = $value;

            $html = $this->viewModule('input', ['input' => $input])->render();
            $modalList = get_admin_template_data('modals');
            $modals = [];

            if($modalList){
                foreach ($modalList as $key) {
                    $modals[$key] = $this->view('_templates.' . $key)->render();
                }
            }
            $js = get_js_src();
            $css = get_css_link();
            $data = compact('html', 'modals', 'js', 'css');
            $data['data'] = get_js_data();
        }

        return $this->json(compact(...$this->apiSystemVars));
    }
    public function getHtmlInputList(Request $request)
    {
        extract($this->apiDefaultData);

        $inputs = $request->inputs;
        $config = $request->config;
        $formData = $request->data;
        $attrs = $request->attrs;

        if(!is_array($inputs) ) $message = "cấu hình input không hợp lệ";
        else{

            $status = true;
            if(is_array($formData)) $formData = [];
            $html = $this->viewModule('simple-input-list', ['inputs' => $inputs, 'config' => $config, 'data' => $formData])->render();
            $modalList = get_admin_template_data('modals');
            $modals = [];

            if($modalList){
                foreach ($modalList as $key) {
                    $modals[$key] = $this->view('_templates.' . $key)->render();
                }
            }
            $js = get_js_src();
            $css = get_css_link();
            $data = compact('html', 'modals', 'js', 'css');
            $data['data'] = get_js_data();
        }

        return $this->json(compact(...$this->apiSystemVars));
    }

}
