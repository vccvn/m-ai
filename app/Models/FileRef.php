<?php

namespace App\Models;
use Gomee\Models\Model;
class FileRef extends Model
{
    public $table = 'file_refs';
    public $fillable = ['file_id', 'ref_id', 'ref'];





    public function file()
    {
        return $this->belongsTo(Fiie::class, 'file_id', 'id');
    }

    /**
     * get avatar url
     * @param boolean $urlencode mã hóa url
     * @return string
     */
    public function getUrl($urlencode=false)
    {
        if($this->filename){
            $filename = $this->filename;
        }else{
            $filename = 'default.png';
        }
        $path = get_content_path('/files/');
        if($filename != 'default.png' && $this->date_path){
            $path.=$this->date_path.'/';
        } else if($filename == 'default.png'){
            $path = 'static/images/';
        }
        $url = asset($path.$filename);
        if($urlencode) return urlencode($url);
        return $url;
    }

    /**
     * get avatar url
     * @param boolean $urlencode mã hóa url
     * @return string
     */
    public function getThumbnail()
    {
        if($this->filetype == 'video') return asset('static/images/video.png');
        if($this->filename){
            $filename = $this->filename;
            $path = $this->getSecretPath() . '/files/';
            if($this->date_path){
                $path.=$this->date_path.'/';
            }

        }else{
            $path = 'static/images/';
            $filename = 'default.png';
        }

        if(file_exists($path2 = public_path($path.'120x120/'.$filename))){
            $url = asset($path.'120x120/'.$filename);
        }else{
            $url = asset($path.$filename);
        }


        return $url;
    }
}
