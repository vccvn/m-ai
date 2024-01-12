<?php

namespace App\Tools;

use App\Engines\Excel;
use Gomee\Helpers\Arr;
use Gomee\Tools\Office\SmartSheet;

/**
 * @method PromptRow[]|array[] getSheetData($sheetIndex = null, $options = []) Lấy dữ liệu trang tính
 */
class PromptImporter extends SmartSheet
{


    const IMPORT_SHEET_INDEX = 0;

    protected $rowClass = PromptRow::class;

    /**
     * cac tuoc tinh sheet user
     *
     * @var array
     */
    protected $importSheetOptions = [
        'title' => 'Nhập liệu từ file',
        'data_row_start' => 2,
        'columns' => [
            'topic_id'              => 'Mã Chủ đề',
            'name'                  => 'Tên Prompt',
            'description'           => 'Mô tả',
            'prompt'                => 'Nội dung prompt',
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
        $this->setup($filename, $this->_options);
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

    public function __call($method, $arguments)
    {
        $indexes = $this->getIndexes();
        $mt = strtolower($method);
        foreach ($indexes as $key => $index) {
            if (count($mts = explode($key, $mt)) == 2) {
                if (in_array($mts[0], ['set', 'add'])) {
                    if (in_array($mts[1], ['datetitle', 'title'])) {
                        if ($mts[1] == 'title') {
                            $this->setSheetTitle($index, ...$arguments);
                        } else {
                            $this->setDefaultDateTitle($index, ...$arguments);
                        }
                    } elseif (in_array($mts[1], ['s', 'list', ''])) {
                        $this->addRows($index, ...$arguments);
                    }

                    return $this;
                }
                break;
            }
        }
    }
}
