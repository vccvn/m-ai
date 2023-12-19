<?php
namespace App\Masks\Files;

use App\Models\File;
use Gomee\Masks\Mask;

class GalleryMask extends Mask
{
    protected function init(){
        $this->allow(['getUrl', 'getThumbnail']);
    }

    public function onLoaded()
    {
        if(!$this->id) $this->id = $this->id;

        $this->url = $this->getUrl();
        $this->thumbnail = $this->getThumbnail();

        $this->source = $this->url;
    }

}
