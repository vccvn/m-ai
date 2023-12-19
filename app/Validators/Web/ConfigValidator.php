<?php

namespace App\Validators\Web;

use Gomee\Validators\Validator;

class ConfigValidator extends Validator
{
    protected $ownerID = 0;
    protected $userID = 0;

    public function extends()
    {
        // $this->repository->reset(true)->removeDefaultConditions();
        $this->userID = $this->user()->id;
        $this->addRule('check_alias', function ($attr, $value) {
            if ($value) return true;
            if (!$this->domain) {
                if ($web = $this->repository->first(['alias_domain' => $value])) {
                    if ($this->ownerID == $web->owner_id) return true;
                    if ($this->userID == $web->owner_id) return true;
                    return false;
                }
            } elseif ($web = $this->repository->matchIn(['alias_domain' => $value, 'domain' => $this->domain])) {
                if ($this->ownerID == $web->owner_id) return true;
                if ($this->userID == $web->owner_id) return true;
                return false;
            }

            return true;
        });

        $this->addRule('check_domain', function ($attr, $value) {
            if ($value) return true;
            if (!$this->alias_domain) {
                if ($web = $this->repository->first(['domain' => $value])) {
                    if ($this->ownerID == $web->owner_id) return true;
                    if ($this->userID == $web->owner_id) return true;
                    return false;
                }
            } elseif ($web = $this->repository->matchIn(['domain' => $value, 'alias_domain' => $this->alias_domain])) {
                if ($this->ownerID == $web->owner_id) return true;
                if ($this->userID == $web->owner_id) return true;
                return false;
            }

            return true;
        });


        $this->addRule('check_web_type', function ($attr, $value) {
            if ($list = get_system_config('web_type_list')) {
                return isset($list[$value]);
            }
            return false;
        });
    }

    /**
     * ham lay rang buoc du lieu
     */
    public function rules()
    {

        return [

            'alias_domain' => 'check_alias',
            'domain' => 'check_domain',
            'ssl' => 'check_boolean',
            'web_type' => 'check_web_type',
            'package_type' => 'in_list:demo,pro,max'

        ];
    }

    /**
     * các thông báo
     */
    public function messages()
    {
        return [
            'web_type.required'                    => 'Bạn chưa chọn gói dịch vụ',
            'web_type.check_web_type'              => 'Gói dịch vụ không hợp lệ',
            'subdomain.required'                   => 'Bạn chưa nhập tên miền',
            'subdomain.check_subdomain'            => 'Tên miền này đã được sử dụng',
            'domain.required'                      => 'Bạn chưa nhập tên miền',
            'domain.check_domain'                  => 'Tên miền hỗ trợ không hợp lệ',
            'alias_domain.required'                => 'Bạn chưa nhập tên miền hoặc tên miền alias',
            'alias_domain.check_alias'             => 'Alias Domain đã được sử dụng',
        ];
    }
}
