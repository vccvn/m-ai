<?php

namespace App\Masks\Products;

use App\Models\ProductCollection;
use Gomee\Masks\Mask;

class CollectionMask extends Mask
{

    // xem thêm ExampleMask
    /**
     * thêm các thiết lập của bạn
     * ví dụ thêm danh sách cho phép truy cập vào thuộc tính hay gọi phương thức trong model
     * hoặc map vs các quan hệ dữ liệu
     *
     * @return void
     */
    // protected function init(){
    //     # code...
    // }

    /**
     * lấy data từ model sang mask
     * @param ProductCollection $collection Tham số không bắt buộc phải khai báo.
     * Xem thêm ExampleMask
     */
    // public function toMask()
    // {
    //     $data = $this->getAttrData();
    //     // thêm data tại đây.
    //     // Xem thêm ExampleMask
    //     return $data;

    // }

    /**
     * sẽ được gọi sau khi thiết lập xong
     *
     * @return void
     */
    protected function onLoaded()
    {
        $labels = $this->getLabelIDs();
        $cates = $this->getCateIDs();
        $tags = $this->getTagIDs();
        $this->image = $this->getFeaturedImage();
        $getProductParamData = [
            '@sorttype' => $this->sorttype
        ];
        $urlParams = [];

        if ($labels) {
            $getProductParamData['@matchAllLabel'] = $labels;
            $urlParams['match_labels'] = implode(',', $labels);
            $this->hasLabel = true;
        }
        if ($cates) {
            $getProductParamData['@hasAnyCategory'] = $cates;
            $urlParams['in_any_category'] = implode(',', $cates);
            $this->hasCategories = true;
        }
        if ($tags) {
            $getProductParamData['@matchAllTag'] = $cates;
            $urlParams['match_tags'] = implode(',', $tags);
            $this->hasTags = true;
        }

        $urlParams['sorttype'] = $this->sorttype;

        $this->getProductParamData = $getProductParamData;

        $this->urlParams = $urlParams;

    }

    public function getProductParams()
    {
        return $this->getProductParamData;
    }
    public function getUrlParams()
    {
        return $this->urlParams;
    }


    public function getProductListUrl($args = [])
    {
        return route('web.products', [
            'collection' => $this->id
        ]);
    }

    // khai báo thêm các hàm khác bên dưới nếu cần
}
