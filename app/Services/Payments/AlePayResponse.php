<?php

namespace App\Services\Payments;

use Countable;
use ArrayAccess;

use ArrayIterator;
use IteratorAggregate;
use JsonSerializable;

use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;

/**
 * @property-read boolean $status Trạng thái của hành động
 * @property-read boolean $isSuccess Trạng thái của hành động
 * @property-read string $code mã trả về
 * @property-read string $message nội dung thông báo
 *
 * @property-read string $orderCode => '',
 * @property-read string $customMerchantId => '1',
 * @property-read string $amount => 0,
 * @property-read string $currency => 'VND',
 * @property-read string $orderDescription => 'Mo Ta',
 * @property-read string $totalItem'=> 1,
 * @property-read string $checkoutType => 0, // 0 - 4,
 * @property-read string $installment => false,
 * @property-read string $month => '',
 * @property-read string $bankCode => '', // [BASE URL]/get-list-banks,
 * @property-read string $paymentMethod => 'BANK_TRANSFER_ONLINE', // ATM_ON, IB_ON,  VIETQR,
 * @property-read string $returnUrl => route('home'),
 * @property-read string $cancelUrl => route('home'),
 * @property-read string $buyerName => 'Thí Sinh',
 * @property-read string $buyerEmail => 'thisinh@cand.edu.vn',
 * @property-read string $buyerPhone => '0987654321',
 * @property-read string $buyerAddress => 'Số 56, Đường An Dương Vương',
 * @property-read string $buyerCity => 'Hà Nội',
 * @property-read string $buyerCountry => 'Việt Nam',
 * @property-read string $paymentHours => 8,
 * @property-read string $promotionCode => '',
 * @property-read string $allowDomestic => false,
 * @property-read string $language => 'vi'
 */
class AlePayResponse implements Countable, ArrayAccess, IteratorAggregate, JsonSerializable, Jsonable, Arrayable
{

    use AlePayMessages;

    protected $data = [];


    protected $props = [
        'code' => '999',
        // 'status' => false,
        'isSuccess' => false,
        'message' => ''
    ];

    public function __construct($data = [])
    {
        $this->data = $data;
        if (array_key_exists('code', $data)) $this->props['code'] = $data['code'];
        if ($this->props['code'] == '000') $this->props['isSuccess'] = true;
        if ($data['message'] ?? '') {
            if (!$this->props['message']) $this->props['message'] = $data['message'] ?? '';
        } else
            $this->props['message'] = $this->getMessage($this->props['code']);
        // $this->props['isSuccess'] = $this->props['status'];
    }

    public function count(): int
    {
        return count($this->data);
    }

    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * lấy giá trị phần tụ theo tên thuộc tính
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        if (array_key_exists($key, $this->props)) return $this->props[$key];
        $k = strtolower($key);
        if(in_array($k, ['success', 'issuccess'])) return $this->props['isSuccess'];
        return $this->data[$key] ?? null;
    }

    /**
     * gan gia tri cho phan tu
     * @param string $key
     * @param mixed $value
     *
     * @return object
     */
    public function __set($key, $value)
    {
        return $value;
    }

    /**
     * kiểm tra tồn tại
     *
     * @return boolean
     */
    public function  __isset($key)
    {

        if (array_key_exists($key, $this->props)) return true;
        return isset($this->data[$key]);
    }

    /**
     * xóa phần tử
     * @param string $key
     */
    public function __unset($key)
    {
        // unset($this->data[$key]);
    }

    public function offsetSet($offset, $value): void
    {
        // if (is_null($offset)) {
        //     $this->data[] = $value;
        // } else {
        //     $this->data[$offset] = $value;
        // }
    }

    public function offsetExists($offset): bool
    {
        if (array_key_exists($offset, $this->props)) return true;
        return isset($this->data[$offset]);
    }

    public function offsetUnset($offset): void
    {
        // unset($this->data[$offset]);
    }

    public function offsetGet($offset): mixed
    {
        if (array_key_exists($offset, $this->props)) return $this->props[$offset];
        return isset($this->data[$offset]) ? $this->data[$offset] : null;
    }


    /**
     * Get an iterator for the items.
     *
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return new ArrayIterator($this->data);
    }


    public function __call($name, $arguments)
    {
        $n = strtolower($name);
        if(in_array($n, ['success', 'issuccess'])){
            return $this->isSuccess;
        }
        if(in_array($n, ['fail', 'isfail', 'failed'])){
            return !$this->isSuccess;
        }
    }


    public function toArray()
    {
        return array_map(function ($value) {
            if ($value instanceof Arrayable) {
                return $value->toArray();
            } elseif (is_object($value) && is_callable([$value, 'toArray'])) {
                return $value->toArray();
            }

            return $value;
        }, array_merge($this->props, $this->data));
    }


    public function toJson($options = 0)
    {
        return json_encode($this->toArray());
    }


    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize(): mixed
    {
        return array_map(function ($value) {
            if ($value instanceof JsonSerializable) {
                return $value->jsonSerialize();
            } elseif ($value instanceof Jsonable) {
                return json_decode($value->toJson(), true);
            } elseif ($value instanceof Arrayable) {
                return $value->toArray();
            }

            return $value;
        }, $this->toArray());
    }
}
