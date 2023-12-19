<?php

namespace App\Excels;

use App\Engines\Excel;
use Gomee\Helpers\Arr;

class PaymentReport extends Excel
{

    protected $generalSheetOptions = [
        'title' => 'Báo cáo người thắng',
        'data_row_start' => 3,
        'columns' => [
            'id' => 'ID',
            'name' => 'Tên',
            'email' => 'Email',
            'branch_name' => 'Nhánh',
            'number_count' => 'Số con đã cược',
            'total_point'        => 'Tổng điểm cược',
            'win_points'    => 'Tổng điểm thắng',
            'payment_points'=> 'Tổng đã trả',
            'current_balance' => 'Số dư',
            'after_balance' => 'Số dư',
            'final_balance'    => 'Số dư sau trả điểm'
        ]
    ];
    protected $path = null;

    const GENERAL_SHEET_INDEX = 0;
    protected $_options = [];
    public function __construct($date = null)
    {
        $this->init();
        $d = $date ? str_replace('-', '/', $date) : date('Y/m/d');
        $a = [
            static::GENERAL_SHEET_INDEX => $this->generalSheetOptions,
        ];
        ksort($a);
        $this->path = storage_path('backups/' . $d . '/report-daily.xlsx');
        $this->_options = $a;
        $this->mode = 'file';
        $this->filename = $this->path;
        // $this->filename = $this->path;
        // parent::__construct($this->path, true, $options);

    }

    public function getIndeexes()
    {
        return [
            'general' => static::GENERAL_SHEET_INDEX
        ];
    }


    /**
     * setitle
     *
     * @param int $sheetIndex
     * @param string $title
     * @return static
     */
    public function setSheetTitle($sheetIndex = null, $title = null)
    {
        if (!($sheet = $this->getSheet($sheetIndex))) return $this;
        $sheetIndex = $this->parseIndex($sheetIndex);
        $title = $title ? $title : $this->_options[$sheetIndex]['title'];
        // $date = $date? $date: date('d/m/Y');
        $sheet->setCellValue('A1', $title);
        return $this;
    }

    /**
     * set ngay thang mac dinh cho title
     *
     * @param int $sheetIndex
     * @param string $date
     * @param string $title
     * @return static
     */
    public function setDefaultDateTitle($sheetIndex = null, $date = null, $title = null)
    {
        if (!($sheet = $this->getSheet($sheetIndex))) return $this;
        $sheetIndex = $this->parseIndex($sheetIndex);
        $title = $title ? $title : $this->_options[$sheetIndex]['title'];
        $date = $date ? $date : date('d/m/Y');
        $sheet->setCellValue('A1', "$title - $date");
        return $this;
    }

    /**
     * can thiệp trước khi đổ data
     *
     * @param int $index
     * @param array|object $data
     * @return array
     */
    public function beforeSetRowData($index, $data)
    {
        $d = is_array($data) ? $data : (is_object($data) ? $data->toArray() : []);
        $a = new Arr($d);
        $a->created_at = date('H:i:s - d/m/Y', strtotime($a->created_at));
        $a->updated_at = date('H:i:s - d/m/Y', strtotime($a->updated_at));
        $a->after_balance = $a->current_balance + to_number($a->win_points);
        $a->final_balance = $a->current_balance + $a->payment_points;
        return $a->all();
    }

    /**
     * load tài liệu
     *
     * @return void
     */
    public function loadTemplate()
    {
        $this->load(storage_path('excels/templates/win-payment-daily.xlsx'), $this->_options);

        $this->save($this->filename);
    }
    public function getPath()
    {
        return $this->path;
    }


    public function setDefaultDateTitleAll($date = null)
    {
        $indexes = $this->getIndeexes();
        foreach ($indexes as $index) {
            $this->setDefaultDateTitle($index, $date);
        }
        return $this;
    }

    public function __call($method, $arguments)
    {
        $indexes = $this->getIndeexes();
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
