<?php

namespace App\Engines;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ExcelFile extends Excel
{
    protected $config = [];
    
    protected $file = null;
    protected $temp = null;
    protected $opts = [];
    protected $mode = 'file';
    public function __construct($config = [])
    {
        $this->init();
        $this->setup($config);
        $this->autoLoad();
    }

    public function setup($config = [])
    {
        if(!$this->file && isset($config['filename'])) $this->file = $config['filename'];
        if(!$this->temp && isset($config['template'])) $this->temp = $config['template'];
        if(isset($config['options'])) $this->opts = $config['options'];
        return $this;
    }
    public function autoLoad()
    {
        if(!$this->file || !file_exists($this->file)){
            if($this->temp && file_exists($this->temp)){
                $this->load($this->temp, $this->opts);
            }else{
                $this->spreadsheet = new Spreadsheet();
                $this->setOptions($this->opts, true);
            }

            
        }else{
            $this->load($this->file, $this->opts);
            $this->filename = $this->file;
        }
        if($this->file){
            $this->filename = $this->file;
            $this->save();
        }
                    
        // die(json_encode($this));
    }

    public function resetByTemplate($option = null, $list = [])
    {
        if(file_exists($this->filename)) unlink($this->filename);
        if($this->temp && file_exists($this->temp)){
            $this->load($this->temp, $this->opts);
        }
        if($this->file){
            $this->filename = $this->file;
            $this->save();
        }

    }
}
