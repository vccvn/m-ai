<?php

namespace App\Excels;

use App\Engines\Excel;
use Gomee\Helpers\Arr;

class ExportPlaceData extends Excel
{

    const IMPORT_SHEET_INDEX = 0;
    /**
     * cac tuoc tinh sheet user
     *
     * @var array
     */
    protected $importSheetOptions = [
        'title' => 'Nhập liệu từ file',
        'data_row_start' => 2,
        'columns' => [
            'region'                => 'Tỉnh thành',
            'name'                  => 'Tên địa điểm'
        ]
    ];

    protected $path = null;
    protected $_options = [];
    public function __construct($filename = null)
    {
        $this->init();
        $a = [
            static::IMPORT_SHEET_INDEX => $this->importSheetOptions
        ];
        ksort($a);
        $this->_options = $a;
        $this->setup($filename, $this->_options, true);
    }

    public function getIndexes()
    {
        return [
            'import' => static::IMPORT_SHEET_INDEX,
        ];
    }




    public function getPath()
    {
        return $this->path;
    }


    public function setDefaultDateTitleAll($date = null)
    {
        $indexes = $this->getIndexes();
        foreach ($indexes as $index) {
            $this->setDefaultDateTitle($index, $date);
        }
        return $this;
    }

    public function __call($method, $argments)
    {
        $indexes = $this->getIndexes();
        $mt = strtolower($method);
        foreach ($indexes as $key => $index) {
            if (count($mts = explode($key, $mt)) == 2) {
                if (in_array($mts[0], ['set', 'add'])) {
                    if (in_array($mts[1], ['datetitle', 'title'])) {
                        if ($mts[1] == 'title') {
                            $this->setSheetTitle($index, ...$argments);
                        } else {
                            $this->setDefaultDateTitle($index, ...$argments);
                        }
                    } elseif (in_array($mts[1], ['s', 'list', ''])) {
                        $this->addRows($index, ...$argments);
                    }

                    return $this;
                }
                break;
            }
        }
    }
}
