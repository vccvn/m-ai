<?php

namespace App\Models;
use Gomee\Models\Model;

class Subscribe extends Model
{
    public $table = 'subscribes';
    public $fillable = ['email', 'phone_number', 'name'];

    public $timestamps = false;
    /**
     * lay du lieu form
     * @return array
     */
    public function toFormData()
    {
        $data = $this->toArray();
        return $data;
    }
    public function getSubscribeInfo()
    {
        $a = [];
        if($this->name){
            $a[] = 'Hõ tên: '.$this->name;
        }
        if($this->email){
            $a[] = 'Email: '.$this->email;
        }
        if($this->phone_number){
            $a[] = 'SĐT: '.$this->phone_number;
        }
        return implode('<br>', $a);
    }
}
