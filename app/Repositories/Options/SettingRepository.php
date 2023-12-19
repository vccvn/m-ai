<?php

namespace App\Repositories\Options;

use App\Validators\Options\SettingValidator;
use Illuminate\Support\Str;
use Gomee\Helpers\Arr;
use Gomee\Files\Filemanager;

class SettingRepository extends OptionRepository
{
    protected $validatorClass = SettingValidator::class;
    /**
     * tao moi setting
     */
    public function createNewData()
    {
        $this->activeDataGroup();
        $this->filemanager = new Filemanager(base_path('json/data/web'));


        $data = [
            'title' => 'Thiết lập',
            'slug' => 'settings'
        ];

        $this->createDataIfNotExists($data, function($option) {
            $this->groupRepository->createDataIfNotExists([
                'slug' => 'system',
                'label' => 'Cài đặt hệ thống',
                'option_id' => $option->id
            ], function($group){
                if($system = $this->filemanager->json('system')){
                    $this->dataRepository->createListData($group->id, $system['data'], false);
                }
            });
            $this->groupRepository->createDataIfNotExists([
                'slug' => 'mailer',
                'label' => 'Thiết lập Email',
                'option_id' => $option->id
            ], function($group){
                if($siteinfo = $this->filemanager->json('mailer')){
                    $this->dataRepository->createListData($group->id, $siteinfo['data'], false);
                }
            });
            $this->groupRepository->createDataIfNotExists([
                'slug' => 'siteinfo',
                'label' => 'Thông tin website',
                'option_id' => $option->id
            ], function($group){
                if($siteinfo = $this->filemanager->json('siteinfo')){
                    $this->dataRepository->createListData($group->id, $siteinfo['data'], false);
                }
            });

            $this->groupRepository->createDataIfNotExists([
                'slug' => 'discounts',
                'label' => 'Thiết lập chiết khấu',
                'option_id' => $option->id
            ], function($group){
                if($siteinfo = $this->filemanager->json('discounts')){
                    $this->dataRepository->createListData($group->id, $siteinfo['data'], false);
                }
            });
            $this->groupRepository->createDataIfNotExists([
                'slug' => 'posts',
                'label' => 'Thiết lập tin bài',
                'option_id' => $option->id
            ], function($group){
                if($siteinfo = $this->filemanager->json('posts')){
                    $this->dataRepository->createListData($group->id, $siteinfo['data'], false);
                }
            });
            $this->groupRepository->createDataIfNotExists([
                'slug' => 'products',
                'label' => 'Thiết lập trang Sản phẩm',
                'option_id' => $option->id
            ], function($group){
                if($siteinfo = $this->filemanager->json('products')){
                    $this->dataRepository->createListData($group->id, $siteinfo['data'], false);
                }
            });

            $this->groupRepository->createDataIfNotExists([
                'slug' => 'projects',
                'label' => 'Thiết lập trang Dự án',
                'option_id' => $option->id
            ], function($group){
                if($siteinfo = $this->filemanager->json('projects')){
                    $this->dataRepository->createListData($group->id, $siteinfo['data'], false);
                }
            });

            $this->groupRepository->createDataIfNotExists([
                'slug' => 'ecommerce',
                'label' => 'Thiết lập cửa hàng',
                'option_id' => $option->id
            ], function($group){
                if($siteinfo = $this->filemanager->json('ecommerce')){
                    $this->dataRepository->createListData($group->id, $siteinfo['data'], false);
                }
            });

            $this->groupRepository->createDataIfNotExists([
                'slug' => 'jssdk',
                'label' => 'Javascript SDK',
                'option_id' => $option->id
            ], function($group){
                if($siteinfo = $this->filemanager->json('jssdk')){
                    $this->dataRepository->createListData($group->id, $siteinfo['data'], false);
                }
            });

            $this->groupRepository->createDataIfNotExists([
                'slug' => 'favicons',
                'label' => 'Biểu tượng Website',
                'option_id' => $option->id
            ], function($group){
                if($siteinfo = $this->filemanager->json('favicons')){
                    $this->dataRepository->createListData($group->id, $siteinfo['data'], false);
                }
            });
            $this->groupRepository->createDataIfNotExists([
                'slug' => 'pwa',
                'label' => 'Thiết lập PWA',
                'option_id' => $option->id
            ], function($group){
                if($siteinfo = $this->filemanager->json('pwa')){
                    $this->dataRepository->createListData($group->id, $siteinfo['data'], false);
                }
            });

            $this->groupRepository->createDataIfNotExists([
                'slug' => 'contents',
                'label' => 'Thiết lập nội dung',
                'option_id' => $option->id
            ], function($group){
                if($siteinfo = $this->filemanager->json('contents')){
                    $this->dataRepository->createListData($group->id, $siteinfo['data'], false);
                }
            });


            $this->groupRepository->createDataIfNotExists([
                'slug' => 'default',
                'label' => 'Thông số mặc định',
                'option_id' => $option->id
            ], function($group) {
                if($siteinfo = $this->filemanager->json('default')){
                    $this->dataRepository->createListData($group->id, $siteinfo['data'], false);
                }
            });

            // $this->groupRepository->createDataIfNotExists([
            //     'slug' => 'trekka',
            //     'label' => 'Trekka',
            //     'option_id' => $option->id
            // ], function($group){
            //     if($siteinfo = $this->filemanager->json('trekka')){
            //         $this->dataRepository->createListData($group->id, $siteinfo['data'], false);
            //     }
            // });

        });

        $urlOptions = [
            'title' => 'Thiết lập URL',
            'slug' => 'urlsettings'
        ];

        $this->createDataIfNotExists($urlOptions, function($option){
            $groups = $this->filemanager->json('urls');
            if($groups){
                foreach ($groups as $key => $g) {
                    $this->groupRepository->createDataIfNotExists([
                        'slug' => $key,
                        'label' => $g['title'],
                        'option_id' => $option->id
                    ], function($group) use($g){
                        if($g['inputs']){
                            $this->dataRepository->createListData($group->id, $g['inputs'], false);
                        }
                    });
                }
            }

        });


        $webOptions = [
            'title' => 'Web',
            'slug' => 'web'
        ];

        $this->createDataIfNotExists($webOptions, function($option){
            $groups = $this->filemanager->json('webconfig');
            if($groups){
                foreach ($groups as $key => $g) {
                    $this->groupRepository->createDataIfNotExists([
                        'slug' => $key,
                        'label' => $g['title'],
                        'option_id' => $option->id
                    ], function($group) use($g){
                        if($g['inputs']){
                            $this->dataRepository->createListData($group->id, $g['inputs'], false);
                        }
                    });
                }
            }

        });

    }



    /**
     * lấy thông tin input và data
     * @param string $group_slug
     *
     * @return array
     */
    public function getSettingFormData(string $group_slug)
    {
        return $this->getOptionFormData([
            'option' => 'settings',
            'ref_id' => 0,
            'group' => $group_slug
        ]);
    }


    /**
     * lấy thông tin input và data
     * @param string $group_slug
     * @param array $args
     * @return array
     */
    public function getSettingItems(string $group_slug, array $args = [])
    {
        return $this->getOptionItems(array_merge($args, [
            'option' => 'settings',
            'ref_id' => 0,
            'group' => $group_slug
        ]));

    }


    /**
     * lấy thông tin input và data
     * @param string $group_slug
     * @param string $name
     * @return array
     */
    public function getSettingItem(string $group_slug, string $name)
    {
        return $this->getOptionItem([
            'option' => 'settings',
            'ref_id' => 0,
            'group' => $group_slug,
            'name' => $name
        ]);

    }

    /**
     * lấy thông tin input và data
     * @param string $group_slug
     * @param array $data
     * @param array $args
     * @return array
     */
    public function updateSettingData(string $group_slug, array $data = [], array $args = [])
    {
        return $this->updateOptionData(array_merge($args, [
            'option' => 'settings',
            'ref_id' => 0,
            'group' => $group_slug
        ]), $data);
    }


    /**
     * lấy thông tin input và data
     * @param string $group_slug
     * @param array $args
     * @return OptionData
     */
    public function getSettingGroup(string $group_slug, array $args = [])
    {
        return $this->getOptionGroup(array_merge($args, [
            'option' => 'settings',
            'ref_id' => 0,
            'group' => $group_slug
        ]));

    }

}
