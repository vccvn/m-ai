<?php

namespace App\Repositories\Options;

use Illuminate\Support\Str;
use Gomee\Helpers\Arr;
use Gomee\Files\Filemanager;

class WebDataRepository extends OptionRepository
{
    /**
     * cap nhat du lieu
     *
     * @param string|array $key_path_or_condition
     * @param mixed $data
     * @return bool
     */
    public function updateData($key_path_or_condition = null, $data = null)
    {
        if ($key_path_or_condition) {
            if (is_string($key_path_or_condition) && ($t = count($a = array_filter(explode('.', str_replace(' ', '', $key_path_or_condition)), fn ($v) => strlen($v) > 0))) > 1) {
                if ($t == 2) {
                    if (!is_array($data)) return false;
                    return $this->updateOptionData(['option' => $a[0], 'group' => $a[1]], $data);
                }
                return $this->updateOptionData(['option' => $a[0], 'group' => $a[1]], [$a[2] => $data]);
            }
            elseif(is_array($key_path_or_condition)){
                return $this->updateOptionData($key_path_or_condition, $data);
            }
            return false;
        }
    }
    /**
     * cap nhat du lieu
     *
     * @param string|array $key_path_or_condition
     * @param mixed $data
     * @return bool
     */
    public function updateDataRef($ref, $ref_id, $key_path_or_condition = null, $data = null)
    {
        if ($key_path_or_condition) {
            if (is_string($key_path_or_condition) && ($t = count($a = array_filter(explode('.', str_replace(' ', '', $key_path_or_condition)), fn ($v) => strlen($v) > 0))) > 1) {
                if ($t == 2) {
                    if (!is_array($data)) return false;
                    return $this->updateOptionData(['option' => $a[0], 'group' => $a[1], 'ref' => $ref, 'ref_id' => $ref_id], $data);
                }
                return $this->updateOptionData(['option' => $a[0], 'group' => $a[1], 'ref' => $ref, 'ref_id' => $ref_id], [$a[2] => $data]);
            }
            elseif(is_array($key_path_or_condition)){
                $c = array_merge(['ref' => $ref, 'ref_id' => $ref_id], $key_path_or_condition);
                return $this->updateOptionData($c, $data);
            }
            return false;
        }
    }

    /**
     * lấy thông tin data
     * @param array|string $key_path_or_condition key gồm option_name.group.name
     * @param boolean $parse_values_by_type biến đổi value theo kiểu
     * @param boolean $convert_to_arr_obj biến đổi mảng trả về thành object accessable
     * @return array|Arr|string|integer|float
     */
    public function getGroupData($key_path_or_condition, $parse_values_by_type = false, $convert_to_arr_obj = false)
    {
        $args = [];
        $name = null;
        if ($key_path_or_condition) {
            if (is_string($key_path_or_condition) && ($t = count($a = array_filter(explode('.', str_replace(' ', '', $key_path_or_condition)), fn ($v) => strlen($v) > 0))) > 1) {
                $args = ['option' => $a[0], 'group' => $a[1]];
            }
            elseif(is_array($key_path_or_condition)){
                $args = $key_path_or_condition;
            }
            if($t == 3){
                $name=$a[2];
            }
        }

        $params = new Arr($args);
        if(!count($optionGroup = $this->getGroupParams($params))) return $name?null:[];
        extract($optionGroup);
        $params->merge(['group_id' => $group->id, '@order_by' => ['priority' => 'ASC']]);

        if($name){
            $params->name = $name;
            if($d = $this->dataRepository->first($params->all())){
                return $d->value;
            }
            return null;
        }else{
            $data = [];
            $listData = $this->dataRepository->get($params->all());
            if(count($listData)){
                foreach ($listData as $oData) {
                    // $d = new Arr($oData->toArray());
                    // $d->remove('props', 'value', 'id');
                    // if(is_array($oData->props)){
                    //     $d->merge($oData->props);
                    // }
                    // $d->remove('validate', 'rules', 'messages');
                    // $inputs[$oData->name] = $d->all();
                    $data[$oData->name] = $oData->value;
                }
            }
            return $data?($convert_to_arr_obj?(new Arr($data)):$data):[];
        }



    }


}
