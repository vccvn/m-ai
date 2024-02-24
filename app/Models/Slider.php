<?php
namespace App\Models;
use Gomee\Models\Model;

/**
 * Slider class
 *
 * @property string $id Slider id
 * @property string $name Tên slider
 * @property string $slug Slider Item
 * @property string $description Mô tả
 * @property integer $priority Độ ưu tiên
 * @property integer $crop Crop ảnh
 * @property integer $width Chiều rộng ảnh
 * @property integer $height Chiều cao
 * @property integer $status Trạng thái
 * @property integer $trashed_status Trạng thái xoá
 * @property SliderItems[] $items
 * @property SliderItems[] $sliderItems
 */
class Slider extends Model
{
    public $table = 'sliders';
    public $fillable = ['name', 'slug', 'description', 'priority', 'crop', 'width', 'height', 'status', 'trashed_status'];





    /**
     * lấy danh sách item thuộc slider
     *
     * @return SliderItem
     */
    public function sliderItems()
    {
        return $this->hasMany(SliderItem::class, 'slider_id', 'id');
    }

    public function items()
    {
        return $this->sliderItems()->orderBy('priority', 'ASC');
    }


    /**
     * lay du lieu form
     * @return array
     */
    public function toFormData()
    {
        $data = $this->toArray();

        return $data;
    }

    public function getSizeText()
    {
        return $this->crop ? $this->width . 'x' . $this->height : 'auto';
    }


    public function beforeDelete()
    {
        if(count($this->sliderItems)){
            foreach ($this->sliderItems as $items) {
                $items->delete();
            }
        }
    }

}
