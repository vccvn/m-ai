<?php

namespace App\Validators\Common;

trait RemoveScriptTag
{
    public function activeRemoveAction()
    {
        $this->addParseAction(function ($data) {
            $d =  $data;
            foreach ($data as $key => $value) {
                $d[$key] = $this->removeScriptTag($value);
            }
            return $d;
        });
    }
    public function removeScriptTag($value)
    {
        if (is_string($value) && !is_bool($value) && !is_numeric($value))
            return strip_tags($value, ['p', 'img', 'div', 'br', 'strong', 'u', 'b', 'i', 'br']);
        if (is_array($value)) {
            $d = $value;
            foreach ($value as $k => $v) {
                $d[$k] = $this->removeScriptTag($v);
            }
            return $d;
        }
        return $value;
    }
}
