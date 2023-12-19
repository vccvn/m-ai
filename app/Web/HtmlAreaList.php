<?php

namespace App\Web;

use App\Engines\ViewManager;
use Gomee\Helpers\Arr;
use Gomee\Html\Html;
use Illuminate\Support\Str;

class HtmlAreaList
{
    protected $areas = [];

    public function __construct($areas = null)
    {
        if (is_countable($areas) && count($areas)) {
            foreach ($areas as $area) {
                $this->areas[str_slug($area->slug, '_')] = new HtmlAreaItem($area);
            }
        }
    }

    /**
     * lấy khu vực dược thiết lập
     *
     * @param string $slug
     * @return HtmlAreaItem|Arr
     */
    public function get($slug = null)
    {
        if (is_null($slug)) return $this->areas;
        return array_key_exists($slug, $this->areas) ? $this->areas[$slug] : (new Arr());
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
}
