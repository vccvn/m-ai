<?php

namespace App\Repositories\Users;

use AB0;
use Gomee\Engines\DCryptEngine;
use App\Http\Controllers\Web\WebController;
use App\Models\User;
use Gomee\Helpers\Arr;
use Illuminate\Http\Request;


use App\Repositories\Dynamics\DynamicRepository;
use App\Repositories\Options\SettingRepository as OptionSetting;
use App\Repositories\Html\AreaRepository;
use App\Repositories\Metadatas\MetadataRepository;
use App\Repositories\Themes\ThemeRepository;
use Gomee\Apis\Api;
use Gomee\Database\MyAdmin;
use Illuminate\Support\Facades\Config;

class OwnerRepository extends UserRepository
{

    /**
     * class chứ các phương thức để validate dử liệu
     * @var string $validatorClass
     */
    protected $validatorClass = 'App\Validators\Users\OwnerValidator';

    /**
     * tên class mặt nạ. Thược có tiền tố [tên thư mục] + \ vá hậu tố Mask
     *
     * @var string
     */
    protected $maskClass = 'Users\OwnerMask';

    /**
     * tên collection mặt nạ
     *
     * @var string
     */
    protected $maskCollectionClass = 'Users\OwnerCollection';

    protected $columns = [
        'id' => 'users.id',
        'name' => 'users.name',
        'email' => 'users.email',
        'username' => 'users.username',
        'phone_number' => 'users.phone_number',
        'type' => 'users.type',
        'status' => 'users.status',
        'trashed_status' => 'users.trashed_status',


    ];

    /**
     * option
     *
     * @var OptionSetting
     */
    public $optionSetting = null;
    /**
     * html
     *
     * @var AreaRepository
     */
    public $htmlAreaRepository = null;
    /**
     * dynamic
     *
     * @var DynamicRepository
     */
    public $dynamicRepository = null;

    /**
     * Controller
     * @var ClientController
     */
    public $controller = null;

    public function init()
    {
        $this->defaultSortBy = [
            'created_at' => 'DESC'
        ];
    }


    /**
     * set cac repositories khac
     *
     * @param OptionSetting $OptionSetting
     * @param AreaRepository $htmlAreaRepository
     * @return $this
     */
    public function setRepositories(
        OptionSetting $OptionSetting
        // ,
        // AreaRepository $htmlAreaRepository
    ) {
        $this->optionSetting = $OptionSetting;
        // $this->htmlAreaRepository = $htmlAreaRepository;
        return $this;
    }

    public function setController($controller)
    {
        $this->controller = $controller;
        return $this;
    }

    /**
     * get staffs by info
     * @param string
     */
    public function findLogin($info)
    {
        return $this->where(function ($query) use ($info) {
            $query->where('phone_number', $info)
                ->orWhere('username', $info)
                ->orWhere('email', $info);
        })->first([
            'status' => 1,
            // 'owner_id' => 0
        ]);
    }


    /**
     * thiết lập truy vấn để sử dụng trong trang quản trị
     * @return object instance
     */
    public function fullInfo()
    {
        $this
        // ->setJoinable([
        //     ['leftJoin', 'profiles', 'profiles.profile_id', '=', 'users.id'],
        //     // ['leftJoin', 'web_settings', 'web_settings.owner_id', '=', 'users.id']
        // ])
            ->setSelectable(array_merge($this->columns, ['created_at' => 'users.created_at', 'avatar' => 'users.avatar', 'secret_id' => 'users.secret_id']));
        // dd($this);
        return $this;
    }


    /**
     * thiết lập truy vấn để sử dụng trong trang quản trị
     * @return object instance
     */
    public function enableManagerQuery()
    {
        $this->fullInfo();
        $cc = [
            'id' => 'users.id',
            'name' => 'users.name',
            'email' => 'users.email',
            'username' => 'users.username',
            'phone_number' => 'users.phone_number',
            'name' => 'profiles.name',

        ];
        $this->setSearchable($cc)
            ->setWhereable($cc)
            ->setSortable(array_merge($this->columns, ['created_at' => 'users.created_at', 'avatar' => 'users.avatar']));
        // dd($this);
        // $this->addDefaultParam('owner_id', 'owner_id', '=', 0);
        return $this;
    }

    /**
     * lấy thông tin chủ web
     * @return \App\Models\User
     */
    public function getOwner()
    {
        return ($id = $this->getOwnerID()) ? $this->clear()->fullInfo()->with(['userWebSetting', 'profile'])->getDetail(['id' => $id]) : null;
    }


    /**
     * tạo dữ liệu chủ web
     *
     * @param User $user
     * @param array $data
     * @param boolean $is_created
     * @return boolean
     */
    public function createOwnerData(User $user, $data = null, $is_created = false)
    {
        // // luu thong tin pro file
        // $this->profiles->saveProfile($user->id, $data, false);

        // // luu thong tin thiet lap
        // $this->webSettings->saveOwnerSetting($user->id, $data);

        // $this->optionRepository->createNewData($user->id);


        // active theme mac dinh tranh bi loi
        // ThemeRepository::addOwnerID($user->id);
        // $this->themeRepository->setOwnerID();
        $themeRepository = new ThemeRepository();
        $themeRepository->activeDefault($user->id);
        // thoing tin khoi tao host
        $data['alias_comment'] = isset($data['alias_domain']) && $data['alias_domain'] ? '' : '#';
        $data['domain'] = isset($data['domain']) && $data['domain'] ? $data['domain'] : '';
        $data['alias_domain'] = isset($data['alias_domain']) && $data['alias_domain'] ? $data['alias_domain'] : '';
        $client_key = $user->client_key ? $user->client_key : substr(md5(substr(md5($user->id), 4, 20)), 4, 16);
        $secret_key = $user->secret_key ? $user->secret_key : substr(md5(substr(md5($user->id), 4, 20)), 4, 16);
        $data['secret'] = $client_key;


        $web_type = isset($data['web_type']) ? $data['web_type'] : 'default';


        return true;
    }

    public function deleteWebData(User $user)
    {
        $websetting = $user->userWebSetting;
        $api = new Api();
        if($websetting->web_type == 'wordpress'){
            $rs = $api->get(env('HOSTING_MANAGER_API') . '/wp/delete?secret_id=' . $user->client_key)->getBody()->getContents();
        } else if($websetting->web_type == 'vcchosting'){
            $rs = $api->get(env('HOSTING_MANAGER_API') . '/hosting/delete?secret_id=' . $user->secret_key)->getBody()->getContents();
        } else {

        }
    }
}
