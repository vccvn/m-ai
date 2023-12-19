<?php

namespace App\Services\Encryptions;

use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Services\Service;
use Gomee\Apis\Api;

/**
 * Hash Service class
 * @method string encrypt(mixed $data) Mã hoá dữ liệu
 * @method mixed decrypt(string $hash) Giải mã
 *
 * @method static string encrypt(mixed $data) Mã hoá dữ liệu
 * @method static mixed decrypt(string $hash) Giải mã
 *
 */
class HashService extends Service
{
    protected static $instance = null;
    protected $module = 'encryptions';

    protected $moduleName = 'Encryption';

    protected $flashMode = true;


    protected $hashKey = null;

    protected $engine = null;
    /**
     * Create a new Service instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->hashKey = config('system.encryption.key');
        $this->makeEngine();
    }


    protected function _encrypt($content)
    {
        if ($this->engine) {
            return $this->engine->encrypt($content);
        }
        $content = serialize($content);
        $content = $this->bcrypt($content);
        $content = $this->combineReverseHash($content);
        $content = base64_encode($content . $this->hashKey);
        $content = $this->combineReverseHash($content);
        return $content;
    }

    protected function _decrypt($content)
    {
        if ($this->engine) {
            $content = $this->engine->decrypt($content);
            return $content;
        }
        $content = $this->uncombinedReverseHash($content);
        $content = strrev(substr(strrev(base64_decode($content)), strlen($this->hashKey)));
        $content = $this->uncombinedReverseHash($content);
        $content = $this->umbcrypt($content);
        try {
            if ($this->isSerialized($content)) {
                $content = unserialize($content);
            } else {
                $content = '';
            }
        } catch (\Throwable $th) {
            $content = '';
        }
        return $content;
    }


    protected function makeEngine()
    {
        $this->engine = create_hash_engine($this->hashKey);
    }





    protected function bcrypt($string)
    {
        $str1 = base64_encode($string);
        $str1 = $this->combineReverse($str1);
        $str1 = '$' . strrev(
            base64_encode(
                '$' . $this->hashKey . strrev($str1) . strrev($this->hashKey)
            )
        );
        return $str1;
    }
    protected function umbcrypt($string)
    {
        $a = base64_decode(strrev(substr($string, 1)));
        $a = substr($a, strlen($this->hashKey) + 1);
        $string = substr(strrev($a), strlen($this->hashKey));
        $string = $this->uncombinedReverse($string);
        return base64_decode($string);
    }


    protected function combine($content)
    {

        $l = strlen($content);
        $a = $l / 2;
        $strOneLen = ((int) $a) + $l % 2;
        $strTwoLen = (int) $a;
        $strOne = substr($content, 0, $strOneLen);
        $strTwo = substr($content, $strOneLen);
        $newText = '';
        for ($i = 0; $i < $strOneLen; $i++) {
            $newText .= substr($strOne, $i, 1) . ($i < $strTwoLen ? substr($strTwo, $i, 1) : '');
        }
        return $newText;
    }
    protected function uncombined($content)
    {
        $strOne = '';
        $strTwo = '';
        $newText = '';
        $l = strlen($content);
        for ($i = 0; $i < $l; $i++) {
            if ($i % 2) $strTwo .= substr($content, $i, 1);
            else $strOne .= substr($content, $i, 1);
        }
        $newText = $strOne . $strTwo;
        return $newText;
    }
    protected function combineReverse($content)
    {

        $l = strlen($content);
        $a = $l / 2;
        $strOneLen = ((int) $a) + $l % 2;
        $strTwoLen = (int) $a;
        $strOne = strrev(substr($content, 0, $strOneLen));
        $strTwo = substr($content, $strOneLen);
        $newText = '';
        for ($i = 0; $i < $strOneLen; $i++) {
            $newText .= substr($strOne, $i, 1) . ($i < $strTwoLen ? substr($strTwo, $i, 1) : '');
        }
        return $newText;
    }

    protected function uncombinedReverse($content)
    {
        $strOne = '';
        $strTwo = '';
        $newText = '';
        $l = strlen($content);
        for ($i = 0; $i < $l; $i++) {
            if ($i % 2) $strTwo .= substr($content, $i, 1);
            else $strOne .= substr($content, $i, 1);
        }
        $newText = strrev($strOne) . $strTwo;
        return $newText;
    }
    protected function combineReverseHash($content)
    {

        $l = strlen($content);
        $a = $l / 2;
        $strOneLen = ((int) $a) + $l % 2;
        $strTwoLen = (int) $a;
        $strOne = substr(md5(uniqid()), 0, 16) . strrev(substr($content, 0, $strOneLen)) . $this->hashKey;
        $strTwo = $this->hashKey . substr($content, $strOneLen) . substr(md5(uniqid()), 0, 16);
        $newText = '';
        $strOneLen = strlen($strOne);
        $strTwoLen = strlen($strTwo);
        for ($i = 0; $i < $strOneLen; $i++) {
            $newText .= substr($strOne, $i, 1) . ($i < $strTwoLen ? substr($strTwo, $i, 1) : '');
        }
        $newText = base64_encode($newText);
        return $newText;
    }

    protected function uncombinedReverseHash($content)
    {
        $content = base64_decode($content);
        $strOne = '';
        $strTwo = '';
        $newText = '';
        $l = strlen($content);
        for ($i = 0; $i < $l; $i++) {
            if ($i % 2) $strTwo .= substr($content, $i, 1);
            else $strOne .= substr($content, $i, 1);
        }
        $a = substr($strTwo, strlen($this->hashKey));
        $b = substr(strrev($strOne), strlen($this->hashKey));
        $newText = substr($b, 0, strlen($b) - 16) . substr($a, 0, strlen($a) - 16);
        return $newText;
    }

    function isSerialized($data, $strict = true)
    {
        // If it isn't a string, it isn't serialized.
        if (!is_string($data)) {
            return false;
        }
        $data = trim($data);
        if ('N;' === $data) {
            return true;
        }
        if (strlen($data) < 4) {
            return false;
        }
        if (':' !== $data[1]) {
            return false;
        }
        if ($strict) {
            $lastc = substr($data, -1);
            if (';' !== $lastc && '}' !== $lastc) {
                return false;
            }
        } else {
            $semicolon = strpos($data, ';');
            $brace     = strpos($data, '}');
            // Either ; or } must exist.
            if (false === $semicolon && false === $brace) {
                return false;
            }
            // But neither must be in the first X characters.
            if (false !== $semicolon && $semicolon < 3) {
                return false;
            }
            if (false !== $brace && $brace < 4) {
                return false;
            }
        }
        $token = $data[0];
        switch ($token) {
            case 's':
                if ($strict) {
                    if ('"' !== substr($data, -2, 1)) {
                        return false;
                    }
                } elseif (false === strpos($data, '"')) {
                    return false;
                }
                // Or else fall through.
            case 'a':
            case 'O':
                return (bool) preg_match("/^{$token}:[0-9]+:/s", $data);
            case 'b':
            case 'i':
            case 'd':
                $end = $strict ? '$' : '';
                return (bool) preg_match("/^{$token}:[0-9.E+-]+;$end/", $data);
        }
        return false;
    }

    public static function __callStatic($method, $parameters)
    {
        if (!self::$instance) self::$instance = app(static::class);
        return call_user_func_array([self::$instance, $method], $parameters);
    }

    public function __call($method, $parameters)
    {
        if (method_exists($this, '_' . $method)) {
            return call_user_func_array([$this, '_' . $method], $parameters);
        }
        return null;
    }
}
