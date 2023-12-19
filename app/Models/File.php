<?php

namespace App\Models;
use Gomee\Models\Model;
/**
 * File class
 *
 * @property string $upload_by
 * @property string $privacy
 * @property string $ref
 * @property string $ref_id
 * @property string $folder_id
 * @property string $date_path
 * @property string $driver
 * @property string $filename
 * @property string $original_filename
 * @property string $filetype
 * @property string $mime
 * @property double $size
 * @property string $extension
 * @property string $title
 * @property string $description
 */
class File extends Model
{
    public $table = 'files';
    public $fillable = ['upload_by', 'privacy', 'ref', 'ref_id', 'folder_id', 'date_path', 'driver', 'filename', 'original_filename', 'filetype', 'mime', 'size', 'extension', 'title', 'description', 'sid'];


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
            $path = get_content_path( 'files' ) . '/';
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

    /**
     * xoa avatar
     */
    public function deleteFile()
    {
        $path1 = $this->getSecretPath() . '/files/';
        if($this->date_path){
            $path1.=$this->date_path.'/';
        }

        if($this->filename && $this->filename != 'default.png' && file_exists($path = public_path($path1.$this->filename))){
            unlink($path);
            if(file_exists($path2 = public_path($path1.'120x120/'.$this->filename))){
                unlink($path2);
            }
        }
    }


    /**
     * lay du lieu form
     * @return array
     */
    public function toFormData()
    {
        $data = $this->toArray();

        $data['url'] = $this->getUrl();
        $data['thumbnail'] = $this->getThumbnail();
        $data['source'] = $data['thumbnail'];
        $data['file'] = $this->original_filename;
        $size_unit = "KB";
        if($this->size>=1024){
            $data['size'] = round($this->size*10/1024)/10;
            $size_unit = 'MB';
            if($data['size']>=1024){
                $data['size'] = round($data['size']*10/1024)/10;
                $size_unit = 'GB';
                if($data['size']>=1024){
                    $data['size'] = round($data['size']*10/1024)/10;
                    $size_unit = 'TB';

                }
            }
        }
        $data['size_unit'] = $size_unit;


        return $data;
    }


    /**
     * tính kích thước
     */
    public function sizeinfo()
    {

        $size_unit = "KB";
        $size = $this->size;
        if($size>=1024){
            $size = round($size*10/1024)/10;
            $size_unit = 'MB';
            if($size>=1024){
                $size = round($size*10/1024)/10;
                $size_unit = 'GB';
                if($size>=1024){
                    $size = round($size*10/1024)/10;
                    $size_unit = 'TB';
                }
            }
        }
        return compact('size', 'size_unit');
    }

    public function getSizeText()
    {
        $s = $this->sizeinfo();
        return $s['size'].$s['size_unit'];
    }
     /**
     * ham xóa file cũ
     * @param int $id
     *
     * @return boolean
     */
    public function deleteAttachFile()
    {
        return $this->deleteFile();
    }

    /**
     * lấy tên file đính kèm cũ
     */
    public function getAttachFilename()
    {
        return $this->filename;
    }

    /**
     * xóa dữ liệu
     */
    public function beforeDelete()
    {
        $this->deleteFile();
    }


}
