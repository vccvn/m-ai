<?php

namespace App\Excels;

use App\Engines\Excel;
use Gomee\Helpers\Arr;

class ReportPaymentDownloader extends Excel
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
            'user_name'             => 'Tên người dùng',
            'user_email'            => 'Email',

            'order_code'            => '"Mã đơn hàng',
            'payment_method_name'   => 'Phương thức',
            'transaction_code'      => 'Mã giao giao dịch',
            'amount'                => 'Số tiền',

            'time_format'           => 'Thời gian',
            'status_text'           => 'Trạng thái'
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
    public function beforeSetRowData($index, $data)
    {
        $d = is_array($data) ? $data : (is_object($data) ? $data->toArray() : []);
        $a = new Arr($d);
        $types = [
            "buy-connect"=> "Mua kết nối",
            "buy-voucher"=> "Mua voucher"
        ];
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
        $a->type_label = $types[$a->type]??null;
        $a->created_at = date('H:i:s - d/m/Y', strtotime($a->created_at));
        $a->updated_at = date('H:i:s - d/m/Y', strtotime($a->updated_at));
        $a->status_text = $a->getStatusLabel();
        return $a->all();
    }

    /**
     * load tài liệu
     *
     * @return void
     */
    public function loadTemplate()
    {
        $this->load(storage_path('excels/templates/payment-report.xlsx'), $this->_options);

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
