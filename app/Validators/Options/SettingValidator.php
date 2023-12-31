<?php

namespace App\Validators\Options;

use Gomee\Helpers\Arr;
use Gomee\Validators\Validator as BaseValidator;

class SettingValidator extends BaseValidator
{
    protected $customRules = [];
    protected $customMessages = [];
    /**
     * thêm các rule
     */
    public function extends()
    {
        $this->addRule('maillist', function($attrm, $value){
            if(!$value) return true;
            $m = array_filter(array_map('trim', explode(',', $value)), function($email){
                return strlen($email)>0;
            });
            $to = array_filter($m, function($email){
                return filter_var($email, FILTER_VALIDATE_EMAIL);
            });
            return count($m) == count($to);
        });
        if(count($items = $this->repository->getSettingItems($this->route('group')))){
            $rules = [];
            $messages = [];
            foreach ($items as $item) {
                $item = new Arr($item->toArray());
                $name = $item->name;
                $label = $item->label;
                $rule = [];
                // kiểm tra type
                if($item->type == 'checklist'){
                    $name.= '.*';
                }

                if(in_array('number', [$item->type, $item->value_type])){
                    $messages[$name.'.check_number'] = $label.' phải là số';
                    $rule[] = 'check_number';

                    if($item->has("min") && is_numeric($item->min)){
                        $rule[] = "min:$item->min";
                    }
                    if($item->has("max") && is_numeric($item->max)){
                        $rule[] = "max:$item->max";
                    }

                }elseif($item->value_type == 'boolean'){
                    $rule[] = 'check_boolean';
                }elseif($item->type == 'email'){
                    $messages[$name.'.email'] = $label.' không hợp lệ';
                    $rule[] = 'email';
                }elseif($item->type == 'textarea'){
                    $messages[$name.'.max'] = $label.' Vượt quá ký tự';
                    $rule[] = 'max:50000';
                }elseif($item->type == 'ckeditor'){
                    $messages[$name.'.max'] = $label.' Vượt quá ký tự';
                    $rule[] = 'max:500000';
                }elseif($item->type == 'maillist'){
                    $messages[$name.'.maillist'] = $label.' không hợp lệ';
                    $rule[] = 'maillist';
                }elseif($item->type == 'tel'){
                    $messages[$name.'.phone_number'] = $label.' không hợp lệ';
                    $rule[] = 'phone_number';
                }elseif($item->type == 'file'){
                    $messages[$name.'.mimes'] = $label.' không hợp lệ';
                    $rule[] = 'mimes:jpg,jpeg,png,gif,ico,svg,doc,docs,pdf';
                    $messages[$name.'_data.base64_file'] = $label.' không hợp lệ';
                    $rules[$name.'_data'] = 'base64_file:image';
                }

                // kiểm tra trong thuộc tính có kèm validate ko
                if(is_array($item->props)){
                    // nếu có rules
                    if(isset($item->props['rules'])){
                        $vdata1 = $this->getValidateData(
                            is_string($item->props['rules'])?$item->props['rules']:'',
                            is_array($item->props['messages'])?$item->props['messages']:[],
                            $name,
                            $label
                        );
                        if($vdata1['rules']) $rule = array_merge($rule, $vdata1['rules']);
                        if($vdata1['messages']) $messages = array_merge($messages, $vdata1['messages']);
                    }
                    // nếu chứa mảng validate
                    elseif(isset($item->props['validate'])){
                        $validate = $item->props['validate'];
                        if(is_array($validate)){

                            $vdata2 = $this->getValidateData(
                                $validate['rules'],
                                is_array($validate['messages'])?$validate['messages']:[],
                                $name,
                                $label
                            );
                        }
                        else{
                            $props = is_array($p = $item->props)?$p:[];
                            $mess=  $props['messages']??[];
                            $vdata2 = $this->getValidateData(
                                $validate,
                                is_array($mess)?$mess:[],
                                $name,
                                $label
                            );
                        }

                        if($vdata2['rules']) $rule = array_merge($rule, $vdata2['rules']);
                        if($vdata2['messages']) $messages = array_merge($messages, $vdata2['messages']);

                    }
                }
                if(!$rule) $rule[] = 'mixed|max:180';
                $rules[$name] = implode('|', $rule);
            }
            $this->customRules = $rules;
            $this->customMessages = $messages;
        }
    }

    public function getValidateData($rules = null, $messages = [], $name = null, $label = null)
    {
        $ruleList = [];
        $messageList = [];
        if(is_array($rules)){
            $rs = $rules;
        }elseif($r1 = array_map(function($value){return trim($value);}, explode('|', $rules))){
            $rs = $r1;
        }
        else{
            $rs = [];
        }
        if(count($rs)){
            $ruleList = $rs;
            foreach ($rs as $value) {
                $t = explode(':', $value);
                if(!($r = trim($t[0]))){
                    $r = $value;
                }
                if(isset($messages[$r])){
                    $messageList[$name.'.'.$r] = $messages[$r];
                }else{
                    $messageList[$name.'.'.$r] = $label. ' không hợp lệ';
                }
            }
        }
        return [
            'rules' => $ruleList,
            'messages' => $messageList
        ];
    }
    /**
     * ham lay rang buoc du lieu
     */
    public function rules()
    {
        return $this->customRules;
        // return $this->parseRules($rules);
    }

    public function messages()
    {
        return $this->customMessages;
    }
}
