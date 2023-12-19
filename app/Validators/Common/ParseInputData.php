<?php

namespace App\Validators\Common;

trait ParseInputData
{
    /**
     * danh sach callback action
     *
     * @var \Closure[]
     */
    protected $parseCallbacks = [];

    /**
     * Them ham xu ly
     *
     * @param \Closure $callback
     * @return $this
     */
    public function addParseCallback($callback)
    {
        if (is_callable($callback))
            $this->parseCallbacks[] = $callback;
        return $this;
    }

    /**
     * Them ham xu ly
     *
     * @param \Closure $callback
     * @return $this
     */
    public function addParseAction($callback){
        return $this->addParseCallback($callback);
    }

    public function parseInputs($data = [])
    {
        $d = $data;
        if (count($this->parseCallbacks) == 0)
            return $d;
        foreach ($this->parseCallbacks as $callback) {
            if (is_callable($callback) && is_array($a = $callback($d)))
                $d = $a;
        }
        return $d;
    }
}
