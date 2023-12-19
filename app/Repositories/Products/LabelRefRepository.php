<?php

namespace App\Repositories\Products;

use Gomee\Repositories\BaseRepository;

class LabelRefRepository extends BaseRepository
{
    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\ProductLabelRef::class;
    }


    /**
     * cập nhật danh sách product
     * @param string $ref
     * @param int $ref_id
     * @param array $list
     * @return array
     */
    public function updateLabelRefs(string $ref = 'link', int $ref_id, array $label_id_list = [])
    {

        $ignore = [];
        $addedData = [];
        if(count($labels = $this->get(compact('ref', 'ref_id')))){
            foreach ($labels as $label) {
                // nếu label nằm trong số id them thì bỏ qua
                if(!in_array($label->label_id, $label_id_list)) $label->delete();
                // nếu ko thì xóa
                else $ignore[] = $label->label_id;
            }
        }
        if(count($label_id_list)){
            foreach ($label_id_list as $label_id) {
                if($label_id&&!in_array($label_id, $ignore)){
                    // nếu ko nằm trong danh sách bỏ qua thì ta thêm mới
                    $addedData[] = $this->save(compact('ref','ref_id', 'label_id'));
                }
            }
        }
        return $addedData;
    }
}
