<?php

namespace App\Excels;

use App\Engines\Excel;
use Gomee\Helpers\Arr;

class AgentReportDownloader extends Excel
{

    const REPORT_SHEET_INDEX = 0;
    /**
     * cac tuoc tinh sheet user
     *
     * @var array
     */
    protected $reportSheetOptions = [
        'title' => 'Báo cáo thanh toán',
        'data_row_start' => 3,
        'columns' => [
            'index'                 => 'STT',
            'customer_id'         => 'Mã User',
            'customer_phone_number'        => 'SĐT',
            'transaction_code'      => 'Mã GD',
            'total'                 => 'Số tiền GD',
            'discount'              => 'Chiết khấu',
            'amount'                => 'Doanh thu',
            'time_format'           => 'Thời gian',
            'note'                  => 'Ghi chú'
        ]
    ];

    protected $path = null;
    protected $_options = [];
    public function __construct($filename = null)
    {
        $this->init();
        $a = [
            static::REPORT_SHEET_INDEX => $this->reportSheetOptions
        ];
        ksort($a);
        $this->path = storage_path('excels/downloads/' . $filename);
        $this->_options = $a;
        $this->mode = 'file';
        $this->filename = $this->path;
    }

    public function getIndexes()
    {
        return [
            'report' => static::REPORT_SHEET_INDEX,
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
    public function beforeSetRowData($index, $data, $row = null)
    {
        $d = is_array($data) ? $data : (is_object($data) ? $data->toArray() : []);
        $a = new Arr($d);
        $this->emit('setRowData', $data);
        switch ($index) {
            case static::REPORT_SHEET_INDEX:
                break;
                // case static::DETAILS_SHEET_INDEX:
                //     $a->betting_date = $s;
                //     break;
            default:
                # code...
                break;
        }
        $a->created_at = date('H:i:s - d/m/Y', strtotime($a->created_at));
        $a->updated_at = date('H:i:s - d/m/Y', strtotime($a->updated_at));
        $a->time_format = date('H:i:s - d/m/Y', strtotime($a->created_at));
        $email = $a->email;
        $phone = $a->phone;
        $mn = explode('@', $email);
        // die(json_encode($this));
        $s = substr($mn[0], 0, 2).$this->repeat('*', strlen($mn[0]) - 4) . substr($mn[0], strlen($mn[0]) - 2, 2) . '@' . $mn[1];
        $a->customer_email = $s;
        $a->customer_phone = substr($phone, 0, 4). $this->repeat('*', strlen($phone) - 6) . substr($phone, 4 + strlen($phone) - 6);
        $a->index = $row - 2;
        return $a->all();
    }

    function repeat($str, $times = 0) : string {
        $r = '';
        for ($i=0; $i < $times; $i++) {
            $r .= $str;
        }
        return $r;
    }
    /**
     * load tài liệu
     *
     * @return void
     */
    public function loadTemplate()
    {
        $this->load(storage_path('excels/templates/agent-report.xlsx'), $this->_options);

        $this->save($this->filename);
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
