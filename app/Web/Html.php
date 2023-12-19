<?php

namespace App\Web;

use App\Engines\ViewManager;
use Gomee\Helpers\Arr;
use Gomee\Html\Html as GomeeHtml;
use Illuminate\Support\Str;

class Html
{
    protected $css = [
        // 'key' => [
        //     'type'           => 'code|link',
        //     'id'             => 'string',
        //     'selectors?'     => 'string',
        //     'code?'          => [
        //         'attribute'  => 'value'
        //     ],
        //     'link?'          => 'string'
        // ]
    ];
    protected $scripts = [];
    protected $codes = [];


    /**
     * area list
     *
     * @var HtmlAreaList
     */
    protected $htmlAreaList = null;
    protected $tagAttributes = [];

    public function __construct($htmlAreaList = null)
    {
        $this->htmlAreaList = $htmlAreaList?$htmlAreaList:(new Arr([
            'top' => new Arr(),
            'bottom' => new Arr(),
            'head' => new Arr(),

        ]));
    }

    /**
     * lấy khu vực dược thiết lập
     *
     * @param string $slug
     * @return HtmlAreaItem|Arr
     */
    public function get($slug = null)
    {
        return $this->htmlAreaList?$this->htmlAreaList->get($slug):null;
    }

    public function parseUrl($string)
    {
        $src = '';
        if (preg_match('/^(http\:\/\/|https\:\/\/|\/\/)+/i', $string)) {
            $src = $string;
        } elseif (strtolower(substr($string, 0, 7)) == '@asset/') {
            $src = theme_asset(substr($string, 7));
        } elseif (strtolower(substr($string, 0, 8)) == '@assets/') {
            $src = theme_asset(substr($string, 8));
        } elseif (strtolower(substr($string, 0, 5)) == '@css/') {
            $src = theme_css(substr($string, 5));
        } elseif (strtolower(substr($string, 0, 4)) == '@js/') {
            $src = theme_js(substr($string, 4));
        } elseif (preg_match('/.*\.(js|css)$/i', $string)) {
            $src = asset($string);
        }
        return $src;
    }

    /**
     * thêm css vào html
     *
     * @param string|array $selectors
     * @param array|string $attrs
     * @return bool
     */
    public function addStyle($selectors = null, $attrs = null, $tagAttributes = [])
    {
        if ($selectors) {
            if (is_array($selectors)) {
                foreach ($selectors as $key => $value) {
                    if (!is_numeric($key)) {
                        $this->addStyle($key, $value);
                    } else {
                        $this->addStyle($key);
                    }
                }
            } elseif (is_string($selectors)) {
                if (is_array($attrs) && $attrs) {
                    if (!isset($this->css[$selectors]))
                        $this->css[$selectors] = [
                            'type' => 'code',
                            'code' => $attrs,
                            'selectors' => $selectors,
                            'attributes' => $tagAttributes,
                            'id' => null
                        ];
                    else {
                        $this->css[$selectors]['code'] = array_merge($this->css[$selectors]['code'], $attrs);
                        if ($tagAttributes) {
                            $this->css[$selectors]['attributes'] = array_merge($this->css[$selectors]['attributes'], $tagAttributes);
                        }
                    }
                } else {
                    $link = '';
                    $id = '';
                    $src = '';
                    if (is_string($attrs) && $attrs) {
                        $link = $attrs;
                        $id = $selectors;
                    } else {
                        $link = $selectors;
                    }
                    $src = $this->parseUrl($link);
                    $key = $id ? $id : $src;
                    if (!isset($this->css[$key]))
                        $this->css[$key] = [
                            'type' => 'link',
                            'link' => $src,
                            'attributes' => $tagAttributes,
                            'id' => $id
                        ];
                    else {
                        $this->css[$key]['link'] = $src;
                        if ($tagAttributes) {
                            $this->css[$key]['attributes'] = array_merge($this->css[$key]['attributes'], $tagAttributes);
                        }
                    }
                }
            }
            return true;
        }
        return false;
    }

    /**
     * thêm css vào html
     *
     * @param string|array $selectors
     * @param array|string $attrs
     * @return bool
     */
    public function addCss($selectors = null, $attrs = null, $tagAttributes = [])
    {
        return $this->addStyle($selectors, $attrs, $tagAttributes);
    }

    /**
     * get Style
     *
     * @param string $selector
     * @return string
     */
    public function getStyle($selector = null)
    {
        $attrs = [];
        if (!$selector) $attrs = $this->css;
        elseif (array_key_exists($selector, $this->css)) {
            $attrs = $this->css[$selector];
        }
        return $this->toCss($attrs);
    }



    /**
     * gwet css
     *
     * @param string $selector
     * @return string
     */
    public function getCss($selector = null)
    {
        return $this->getStyle($selector);
    }


    public function getAndCleanCss()
    {
        if ($this->css) {
            $style = $this->toCss($this->css);
            $this->css = [];
            return $style;
        }
        return null;
    }

    public function toCss($cssList)
    {
        $tags = '';
        foreach ($cssList as $key => $value) {
            $tags .= $this->toCssTag($value);
        }
        return $tags;
    }
    /**
     * convert to css
     *
     * @param array $attrs
     * @return string
     */
    public function toCssTag($data = [])
    {
        $id = array_key_exists('id', $data) ? $data['id'] : '';

        $type = array_key_exists('type', $data) ? $data['type'] : '';
        $tag = '';
        if ($type == 'code') {
            $tag = '    <style ' . ($id ? 'id="' . $id . '"' : '');
            $attrs = array_key_exists('attributes', $data) ? $data['attributes'] : [];
            if ($attrs && is_array($attrs)) {
                foreach ($attrs as $key => $value) {

                    if (is_bool($value)) {
                        if ($value) $tag  .= " " . $key;
                    } else {
                        $tag  .= " $key=\"" . htmlentities($value) . "\"";
                    }
                }
            }
            $tag .= ">\n";
            $tag .= "        " . $data['selectors'] . " {\n";
            foreach ($data['code'] as $key => $value) {
                $tag .= "            " . Str::snake($key, '-') . ': ' . ((string) $value) . ";\n";
            }
            $tag .= "        }\n";
            $tag .= "    </style>\n";
        } else {
            $tag = "<link rel=\"stylesheet\"" . ($id ? " id=\"$id\"" : "") . " href=\"$data[link]\"";
            $attrs = array_key_exists('attributes', $data) ? $data['attributes'] : [];
            if ($attrs && is_array($attrs)) {
                foreach ($attrs as $key => $value) {

                    if (is_bool($value)) {
                        if ($value) $tag  .= " " . $key;
                    } else {
                        $tag  .= " $key=\"" . htmlentities($value) . "\"";
                    }
                }
            }
            $tag .= ">\n";
        }
        return $tag;
    }


    /**
     * add js src
     *
     * @param string|array $src
     * @return void
     */
    public function addScript($src, $attributes = null, $passedCheckUrl = false)
    {
        if (is_array($src)) {
            foreach ($src as $key => $attribute) {
                if (is_numeric($key)) {
                    if (is_string($attribute)) {
                        $this->addScript($attribute, ['id' => null]);
                    } elseif (is_array($attribute) && array_key_exists('src', $attribute)) {
                        $this->addScript($attribute['src'], $attribute);
                    }
                } elseif ($u = $this->parseUrl($key)) {
                    $this->addScript($u, $attribute, true);
                } elseif (is_array($attribute) && array_key_exists('src', $attribute)) {
                    if (!array_key_exists('id', $attribute)) {
                        $attribute['id'] = $key;
                    }
                    $this->addScript($attribute['src'], $attribute);
                }
            }
        } else {
            $data = is_array($attributes) ? $attributes : [];
            if ($passedCheckUrl) {
                $data['src'] = $src;
            } elseif (!array_key_exists('src', $data)) {
                if ($s = $this->parseUrl($src)) {
                    $data['src'] = $src;
                    if (is_string($attributes) && !array_key_exists('id', $data)) {
                        $data['id'] = $attributes;
                    }
                } elseif (is_string($attributes) && $u = $this->parseUrl($attributes)) {
                    $data['src'] = $u;
                    if (!array_key_exists('id', $data)) {
                        $data['id'] = $src;
                    }
                }
            } elseif (!$passedCheckUrl && $url = $this->parseUrl($data['src'])) {
                $data['src'] = $url;
            }
            if (!array_key_exists('src', $data) || !$data['src']) {
                return false;
            }
            $this->scripts[$data['src']] = $data;
        }
    }

    public function getAndCleanScripts()
    {
        $tags = '';

        foreach ($this->scripts as $k => $data) {
            $tags .= '    <script';
            foreach ($data as $key => $value) {
                if (is_bool($value)) {
                    if ($value) $tags  .= " " . $key;
                } else {
                    if(is_array($value)){
                        foreach ($value as $_k => $_v) {
                            if(!is_array($_v)){
                                $tags  .= " $key-$_k=\"" . htmlentities($_v) . "\"";
                            }
                        }
                    }
                    else{
                        $tags  .= " $key=\"" . htmlentities($value) . "\"";
                    }
                }
            }
            $tags.= "></script>\n";
        }
        $this->scripts = [];

        return $tags;
    }

    /**
     * thêm mã html
     *
     * @param string $code
     * @return bool
     */
    public function addUserCode($code = null)
    {
        if (!in_array($code, $this->codes)) {
            $this->codes[] = $code;
        }
        return $this;
    }

    /**
     * lấy chuỗi mã html đã thêm vào
     *
     * @return string
     */
    public function getUserCodes()
    {
        $str = '';
        while (count($this->codes) > 0) {
            $str .= (string) array_shift($this->codes);
        }
        return $str;
    }


    /**
     * thêm thuộc tính cho thẻ
     *
     * @param string $tag
     * @param array|string $attr
     * @param mixed $value
     * @return bool
     */
    public function addTagAttribute($tag = null, $attr = null, $value = null)
    {
        if (is_string($tag) && $t = trim($tag)) {
            // nếu có giá trị thuộc tính hoặc key thuộc tính
            if ($attr) {
                // kiểm tra nếu chưa set thì set một mảng
                if (!isset($this->tagAttributes[$tag])) {
                    $this->tagAttributes[$tag] = [];
                }
                // nếu là array thì merge
                if (is_array($attr)) {
                    $this->tagAttributes[$tag] = array_merge($this->tagAttributes[$tag], $attr);
                }
                // giá trị khác xet biunh2 thường
                else {
                    $this->tagAttributes[$tag][$attr] = $value;
                }
                return true;
            }
        }
        return false;
    }


    /**
     * lấy về chuổi thuộc tính của thẻ
     *
     * @param string $tag
     * @return mixed[]
     */
    public function getTagAttributes($tag = null)
    {
        return is_string($tag) && $tag && array_key_exists($tag, $this->tagAttributes) ? $this->tagAttributes[$tag] : [];
    }

    /**
     * lấy về chuổi thuộc tính của thẻ
     *
     * @param string $tag
     * @param array $defaultAttributes
     * @return string
     */
    public function getTagAttributeToString($tag = null, $defaultAttributes = [])
    {
        $attrString = '';
        $attrs = array_merge($defaultAttributes, $this->getTagAttributes($tag));
        if ($attrs) {
            foreach ($attrs as $key => $value) {

                if (is_bool($value)) {
                    if ($value) $attrString .= " " . $key;
                } else {
                    $attrString .= " " . $key . '="' . htmlentities($value) . '"';
                }
            }
        }
        return $attrString;
    }



    /**
     * lấy một area
     *
     * @param string $name
     * @return HtmlAreaItem|Arr
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * tạo một dom html bằng php
     *
     * @param string $name
     * @param array $arguments
     * @return Html
     */
    public function __call($name, $arguments)
    {
        return GomeeHtml::make($name, $arguments);
    }

    public function __toString()
    {
        $returnString = '';
        # code...

        return $returnString;
    }

    public function getRegisterForm($config = [])
    {
        return get_register_form($config);
    }

    public function getLoginForm($config = [])
    {
        return get_login_form($config);
    }

    public function getShareButtons($title = null, $data = [])
    {
        return ViewManager::libTemplate('social-share-buttons', compact('title', 'data'));
    }
}
