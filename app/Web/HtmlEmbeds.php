<?php
namespace App\Web;

use Gomee\Files\Filemanager;
use Gomee\Helpers\Arr;
use App\Engines\JsonData;

class HtmlEmbeds extends Arr{
    public function getCode($area = null)
    {
        $embeds = $area?$this->get($area):$this->all();
        $embedstr = '';
        if($embeds){
            if(is_array($embeds)){
                foreach ($embeds as $embed) {
                    if(is_array($embed)){
                        foreach ($embed as $code) {
                            $embedstr.=$code;
                        }
                    }else{
                        $embedstr.=$embed;
                    }
                }
            }else{
                $embedstr .= $embeds;
            }
        }
        return $embedstr;
    }
}