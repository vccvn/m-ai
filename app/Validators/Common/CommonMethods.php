<?php
namespace App\Validators\Common;
trait CommonMethods{
    public function addCheckColorRule()
    {
        $this->addRule('check_color', function($attr, $value){
            if(!strlen($value)) return true;
            if(preg_match('/^\#[A-f0-9]{3,6}$/i', $value)) return true;
            return false;
        });

        
    }

    public function addCheckMoneyRule()
    {
        $this->addRule('check_money', function($attr, $value){
            if(!strlen($value)) return true;
            return strlen((int) $value) < 11;
        });

        
    }
}