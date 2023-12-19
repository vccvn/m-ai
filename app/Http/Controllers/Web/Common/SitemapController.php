<?php

namespace App\Http\Controllers\Web\Common;

use App\Http\Controllers\Web\WebController;
use App\Repositories\Categories\CategoryRepository;
use App\Repositories\Courses\CourseRepository;
use App\Repositories\Dynamics\DynamicRepository;
use App\Repositories\Pages\PageRepository;
use App\Repositories\Posts\CategoryRepository as PostsCategoryRepository;
use App\Repositories\Posts\PostRepository;
use App\Repositories\Posts\SearchRepository;
use App\Repositories\Products\ProductRepository;
use Illuminate\Http\Request;
use Crazy\Helpers\Arr;

use Illuminate\Support\Facades\View;

class SitemapController extends WebController
{
    protected $module = 'sitemaps';

    protected $moduleName = 'Sitemap';

    protected $flashMode = true;

    public $prods = [];
    public $postList = [];

    /**
     * repository chinh
     *
     * @var SearchRepository
     */
    public $repository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        SearchRepository $repository,
        DynamicRepository $dynamicRepository,
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
        PostsCategoryRepository $postCategoryRepository,
        PageRepository $pageRepository,
        PostRepository $postRepository
    )
    {
        $this->repository = $repository;
        $this->dynamicRepository = $dynamicRepository->notTrashed();
        $this->productRepository = $productRepository->notTrashed();
        $this->categoryRepository = $categoryRepository->notTrashed();
        $this->postCategoryRepository = $postCategoryRepository->notTrashed();
        $this->pageRepository = $pageRepository->notTrashed();
        $this->postRepository = $postRepository->notTrashed();


        $this->init();
    }


    public function sitemap(Request $request)
    {

        $data = [
            route('sitemap.home')
        ];

        $type = get_web_type();
        if($type == 'ecommerce'){
            $data[] = route('sitemap.product-category');
            $data[] = route('sitemap.products');

        }
        if(count($dynamics = $this->dynamicRepository->get())){
            foreach ($dynamics as $dynamic) {
                $data[] = route('sitemap.posts', ['slug' => $dynamic->slug]);

            }
        }

        $content = View::make('sitemap.sitemap')->with(compact('data'));
        $response = response($content, '200');
        $response->header('Content-Type', 'text/xml');
        return $response;
    }

    public function home(Request $request)
    {
        $p = $this->pageRepository->get(['privacy' => ['public', 'publish', 'published']]);
        $pages = [];
        foreach ($p as $key => $a) {
            $pages[] = [
                'url' => $a->getViewUrl(),
                'time' => $a->updated_at . ''
            ];
        }
        $content = View::make('sitemap.home')->with(compact('pages'));
        $response = response($content, '200');
        $response->header('Content-Type', 'text/xml');
        return $response;

    }


    public function productCategory(Request $request)
    {
        $p = $this->categoryRepository->get();
        $list = [];
        foreach ($p as $key => $a) {
            $list[] = [
                'url' => $a->getViewUrl(),
                'time' => $a->updated_at . ''
            ];
        }
        $content = View::make('sitemap.urls')->with(compact('list'));
        $response = response($content, '200');
        $response->header('Content-Type', 'text/xml');
        return $response;

    }


    public function products(Request $request)
    {

        $list = [];
        $this->productRepository->query(['privacy' => ['public', 'publish', 'published']])->chunkById(100, function($prods){
            foreach ($prods as $key => $a) {
                $this->prods[] = [
                    'url' => $a->getViewUrl(),
                    'time' => $a->updated_at . ''
                ];
            }

        });
        $list = $this->prods;
        $content = View::make('sitemap.urls')->with(compact('list'));
        $response = response($content, '200');
        $response->header('Content-Type', 'text/xml');
        return $response;

    }


    public function posts(Request $request, $slug = null)
    {

        $list = [];
        if($request->slug && $dynamic = $this->dynamicRepository->detail(['slug' => $slug])){

            if($dynamic->use_category && $categories = $this->postCategoryRepository->get(['dynamic_id' => $dynamic->id])){
                foreach ($categories as $key => $a) {
                    $this->postList[] = [
                        'url' => $a->getViewUrl(),
                        'time' => $a->updated_at . ''
                    ];
                }
            }
            $this->postRepository->query(['privacy' => ['public', 'publish', 'published'], 'dynamic_id' => $dynamic->id])->chunkById(100, function($prods){
                foreach ($prods as $key => $a) {
                    $this->postList[] = [
                        'url' => $a->getViewUrl(),
                        'time' => $a->updated_at . ''
                    ];
                }

            });
            $list = $this->postList;

        }
        $content = View::make('sitemap.urls')->with(compact('list'));
        $response = response($content, '200');
        $response->header('Content-Type', 'text/xml');
        return $response;

    }
}
