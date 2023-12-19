<?php

namespace App\Repositories\Posts;

use Gomee\Repositories\BaseRepository;
/**
 * validator 
 * 
 */
use App\Masks\Posts\PostViewMask;
use App\Masks\Posts\PostViewCollection;
class ViewRepository extends BaseRepository
{
    /**
     * tên class mặt nạ. Thường có tiền tố [tên thư mục] + \ vá hậu tố Mask
     *
     * @var string
     */
    protected $maskClass = PostViewMask::class;

    /**
     * tên collection mặt nạ
     *
     * @var string
     */
    protected $maskCollectionClass = PostViewCollection::class;

    /**
     * @var \App\Models\PostView
     */
    static $__Model__;

    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\PostView::class;
    }

    public function init()
    {
        # code...
    }

    public function setDateView($post_id, $date = null)
    {
        $view_date = $date??date("Y-m-d");
        $params = compact('post_id', 'view_data');
        if($view = $this->first($params)){
            $this->update($view->id, ['view_total' => $view->view_total + 1]);
        }else{
            $params['view_total'] = 1;
            $view = $this->create($params);
        }
        return $view;
    }

}