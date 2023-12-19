<?php

namespace App\Repositories\Categories;

use Gomee\Repositories\BaseRepository;

class CategoryRefRepository extends BaseRepository
{

    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\CategoryRef::class;
    }


    /**
     * lấy các category của một ref nào đó
     * @param string $ref
     * @param int $ref_id
     * @return array
     */
    public function getCategoryRefs($ref = 'link', $ref_id = 0)
    {
        $data = [];
        if($ref_id && $categories = $this->get(compact('ref', 'ref_id'))){
            foreach ($categories as $category) {
                $data[] = $category->category_id;
            }
        }
        return $data;
    }

    /**
     * cập nhật danh sách category
     * @param string $ref
     * @param int $ref_id
     * @param array $category_id_list
     * @return void
     */
    public function updateCategoryRef(string $ref = 'link', int $ref_id, array $category_id_list = [])
    {

        $ignore = [];
        $addedData = [];
        if(count($categoris = $this->get(compact('ref', 'ref_id')))){
            foreach ($categoris as $category) {
                // nếu category nằm trong số id them thì bỏ qua
                if(!in_array($category->category_id, $category_id_list)) $category->delete();
                // nếu ko thì xóa
                else $ignore[] = $category->category_id;
            }
        }
        if(count($category_id_list)){
            foreach ($category_id_list as $category_id) {
                if($category_id &&!in_array($category_id, $ignore)){
                    // nếu ko nằm trong danh sách bỏ qua thì ta thêm mới
                    $addedData[] = $this->save(compact('ref','ref_id', 'category_id'));
                }
            }
        }
        return $addedData;
    }

}
