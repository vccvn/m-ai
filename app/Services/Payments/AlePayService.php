<?php

namespace App\Services\Payments;

use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Services\Service;
use Gomee\Apis\Api;

class AlePayService extends Service
{
    use AlePayMessages;
    protected $module = 'alepays';

    protected $moduleName = 'Alepay';

    protected $flashMode = true;


    protected $tokenKey = null;
    protected $checksumKey = null;

    protected $baseURL = 'https://alepay-v3.nganluong.vn/api/v3/checkout';
    protected $assetURL = 'https://alepay.vn/dataimage';
    /**
     * Api
     *
     * @var Api
     */
    protected $api;
    /**
     * config
     *
     * @var Arr
     */
    protected $config;
    /**
     * Create a new Service instance.
     *
     * @return void
     */
    public function __construct($tokenKey = null, $checksumKey = null)
    {
        if (is_array($tokenKey)) {
            $this->tokenKey = $tokenKey['tokenKey'] ?? ($tokenKey['token'] ?? '');
            $this->checksumKey = $tokenKey['checksumKey'] ?? ($tokenKey['checksum'] ?? '');
        } else {
            $this->tokenKey = $tokenKey;
            $this->checksumKey = $checksumKey;
        }


        $this->init();
        $this->api = new Api();
        $this->api->setResponseType('json');
    }

    /**
     * set token
     *
     * @param string $tokenKey
     * @return void
     */
    public function setToken($tokenKey)
    {
        $this->tokenKey = $tokenKey;
    }

    /**
     * set checksum
     *
     * @param string $checksumKey
     * @return void
     */
    public function setChecksum($checksumKey)
    {
        $this->checksumKey = $checksumKey;
    }
    /**
     * set base url
     *
     * @param string $baseURL
     * @return $this
     */
    public function setBaseURL($baseURL) {
        $this->baseURL = $baseURL;
        return $this;
    }

    /**
     * set base url
     *
     * @param string $baseURL
     * @return $this
     */
    public function setAssetURL($assetURL) {
        $this->assetURL = $assetURL;
        return $this;
    }



    /**
     * xoa dau tieng Viet
     *
     * @param array $arrData
     * @return array
     */
    public function cleanUnicode($arrData = [])
    {
        $data = [];
        foreach ($arrData as $key => $value) {
            $data[$key] = is_string($value) ? vnclean($value) : $value;
        }
        return $data;
    }

    public function parseRequestData($data)
    {
        $default = [
            'tokenKey' => $this->tokenKey
        ];
        $a = array_merge($default, $data);
        ksort($a, SORT_ASC);
        return $a;
    }

    /**
     * chuẩn hoá dữ liệu tạo request
     *
     * @param array $data
     * @return array
     */
    public function parseCreateParams($data)
    {
        $default = [
            'tokenKey' => $this->tokenKey,
            'orderCode' => '',
            'customMerchantId' => '1',
            'amount' => 0,
            'currency' => 'VND',
            'orderDescription' => 'Mo Ta',
            'totalItem' => 1,
            'checkoutType' => 4, // 0 - 4,
            // 'installment' => false,
            // 'month' => '',
            'bankCode' => '', // [BASE URL]/get-list-banks,
            // 'paymentMethod' => 'BANK_TRANSFER_ONLINE', // ATM_ON, IB_ON,  VIETQR,
            'returnUrl' => route('api.payment.complete'),
            'cancelUrl' => route('api.payment.cancel'),
            'buyerName' => 'user',
            'buyerEmail' => 'user@vcc.vn',
            'buyerPhone' => '0987654321',
            'buyerAddress' => 'Số 56, Đường An Dương Vương',
            'buyerCity' => 'Hà Nội',
            'buyerCountry' => 'Việt Nam',
            'paymentHours' => 8,
            'promotionCode' => '',
            // 'allowDomestic' => false,
            'language' => 'vi'

        ];
        $a = array_merge($default, $data);
        ksort($a, SORT_ASC);
        return $a;
    }

    /**
     * chuyển dữ liệu thành url query
     *
     * @param array $data
     * @return string
     */
    public function encodeUrlData($data = [])
    {
        // $data = $this->parseCreateParams($data);
        ksort($data, SORT_ASC);
        $string = '';
        foreach ($data as $key => $value) {
            $string .= "&$key=" . (is_string($value) && !is_bool($value) ? vnclean($value) : (is_bool($value)? ($value === true?'true':'false'): $value ));
        }
        $string = ltrim($string, '&');
        return $string;
    }

    /**
     * yao5 chữ ký
     *
     * @param array $data
     * @return string
     */
    public function getSignature($data = [])
    {
        $data = array_merge(['tokenKey' => $this->tokenKey], $data);
        ksort($data, SORT_ASC);
        $signature = hash_hmac("sha256", $this->encodeUrlData($data), $this->checksumKey);

        return $signature;
    }

    /**
     * tạo yêu cầu thanh toán
     *
     * @param array $data
     * @param string $signature
     * @return AlePayResponse
     */
    public function createPaymentRequest($data, $signature = null)
    {
        // $data = array_merge(, $data);
        // $signature = $this->getSignature($data);
        $data = $this->cleanUnicode($data);
        $data = $this->parseCreateParams($data);
        $signature = $signature ? $signature : $this->getSignature($data);
        $data = array_merge($data, ['signature' => $signature]);
        try {
            $rs = $this->api->post($this->baseURL . '/request-payment', $data);
            // $rs['input'] = $data;
            return new AlePayResponse($rs);
        } catch (\Throwable $th) {
            throw $th;
        }
        return new AlePayResponse();
    }

    /**
     * Lấy thông tin giao dịch
     *
     * @param string $signature
     *
     * @return AlePayResponse
     */
    public function getTransactionInfo($transactionCode)
    {
        $data = $this->parseRequestData(['transactionCode' => $transactionCode]);
        $signature = $this->getSignature($data);
        $data = array_merge($data, ['signature' => $signature]);
        try {
            $rs = $this->api->post($this->baseURL . '/get-transaction-info', $data);
            // $rs['input'] = $data;
            return new AlePayResponse($rs);
        } catch (\Throwable $th) {
            throw $th;
        }
        return new AlePayResponse();
    }

    public function getBankList()
    {
        $data = $this->parseRequestData([]);
        $signature = $this->getSignature($data);
        $data = array_merge($data, ['signature' => $signature]);
        try {
            $rs = $this->api->post($this->baseURL . '/get-list-banks', $data);
            // $rs['input'] = $data;
            return new AlePayResponse($rs);
        } catch (\Throwable $th) {
            throw $th;
        }
        return new AlePayResponse();
    }

    public function getBankOptions()
    {
        $options = [];
        $res = $this->getBankList();
        if ($res->isSuccess) {
            if (is_array($data = $res->data)) {
                foreach ($data as $bank) {
                    if($bank['methodCode'] != 'VA') continue;
                    $fname = $bank['bankCode'] . '.png';
                    $options[$bank['bankCode']] = '<span class="text-with-icon">' .
                        // '<span class="icon">' .
                        // '<img class="img-icon" src="' . $this->assetURL .'/'. $fname . '" />' .
                        // '</span>' .
                        '<span class="text">' . $bank['bankFullName'] . ' ('.$bank['bankCode'].') </span>' .
                        '</span>';
                }
            }
        }
        return $options;
    }

    public function getPaymentMethodData()
    {
        $options = [
            'ATM_ON' => [
                'methodName' => 'Thanh toán bằng thẻ ATM',
                'banks' => []
            ],
            'IB_ON' => [
                'methodName' => 'Thanh toán bằng tài khoản IB',
                'banks' => []
            ],
            'QRCODE' => [
                'methodName' => 'Thanh toán bằng cách quét mã QRCODE',
                'banks' => []
            ],
            'VIETQR' => [
                'methodName' => 'Thanh toán bằng cách quét mã QRCODE napas247',
                'banks' => []
            ],
            'BANK_TRANSFER_ONLINE' => [
                'methodName' => 'Thanh toán bằng hình thức chuyển khoản tự động thành công',
                'banks' => []
            ],
            'VA' => [
                'methodName' => 'Thanh toán bằng hình thức chuyển khoản 24/7',
                'banks' => []
            ]
        ];
        $res = $this->getBankList();
        if ($res->isSuccess) {
            if (is_array($data = $res->data)) {
                foreach ($data as $bank) {
                    // if($bank['methodCode'] != 'VA') continue;
                    // $fname = $bank['bankCode'] . '.png';
                    $bank['name'] = $bank['bankFullName'] . ' ('.$bank['bankCode'].')';
                    $bank['logo'] = $this->assetURL. $bank['urlBankLogo'];
                    $bank['code'] = $bank['bankCode'];
                    $options[$bank['methodCode']]['banks'][] = $bank;
                }
            }
        }
        return $options;
    }
}
