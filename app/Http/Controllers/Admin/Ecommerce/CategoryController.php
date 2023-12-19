<?php

namespace App\Http\Controllers\Admin\Ecommerce;

use App\Http\Controllers\Admin\AdminController;
use App\Models\Attribute;
use App\Repositories\Categories\CategoryRefRepository;
use Illuminate\Http\Request;
use Gomee\Helpers\Arr;
use App\Repositories\Products\CategoryRepository;
use App\Repositories\Metadatas\MetadataRepository;
use Gomee\Html\Menu;
use Illuminate\Pagination\LengthAwarePaginator;
/**
 * @property CategoryRefRepository $categoryRefRepository
 */
class CategoryController extends AdminController
{
    protected $module = 'products.categories';

    protected $moduleName = 'Danh mục';


    protected $flashMode = true;

    /**
     * repo
     *
     * @var CategoryRepository
     */
    public $repository = null;

    /**
     * @var string $formLayout
    */
    // protected $formLayout = 'forms.grid';
    

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(CategoryRepository $CategoryRepository, MetadataRepository $metadataRepository, CategoryRefRepository $categoryRefRepository)
    {
        $this->repository = $CategoryRepository;
        $this->metadataRepository = $metadataRepository;

        $this->categoryRefRepository = $categoryRefRepository;
        
        $this->init();
        $this->activeMenu('products');
        $this->activeMenu('products.categories.list');
        
        $this->addHeaderButtons('create');
        $refs = $categoryRefRepository->get(['ref' => Attribute::REF_KEY]);

        foreach ($refs as $ref) {
            $ref->delete();
        }
    }


    /**
     * cho phep can thiệp trước khi đổ ra update form view 
     * @param Request $request
     * @param Arr $config
     * @param Arr $inputs
     * @param Arr $data
     * @param Arr $attrs
     * 
     */
    public function beforeGetUpdateForm(Request $request)
    {        
        $this->repository->setActiveID($request->id);
    }

    /**
     * can thiệp trước khi luu
     * @param \Illuminate\Http\Request $request
     * @param Arr $data dũ liệu đã được validate
     * @return void
     */
    protected function beforeSave(Request $request, $data)
    {
        $data->slug = $this->repository->getSlug(
            $request->slug ?? $request->name,
            $request->id
        );
        if(is_numeric($data->featured_image)){
            $data->featured_image  = '@media:'.$data->featured_image;
        }
        // $this->uploadImageAttachFile($request, $data, 'featured_image', get_content_path('categories'));
    }

    /**
     * lưu các dữ liệu liên quan như thuộc tính, meta, gallery
     * @param Request $request
     * @param App\Models\Category $category
     * @param Crazy\Helpers\Arr $data dữ liệu từ input đã dược kiểm duyệt
     *
     * @return void
     */
    public function afterSave(Request $request, $category, $data)
    {
        $this->metadataRepository->saveMany('category', $category->id, $data->copy([
            'page_title',
            'meta_title'
        ]));



    }

    public function beforeGetListData($request)
    {
        $this->repository->withCountProductQuery();
    }

    public function beforeGetListView($request, $data)
    {
        add_js_data('category_map_data', [
            'map' => get_product_category_map(),
            'default_parent' => 'Không'
        ]);
    }

    /**
     * tim kiếm thông tin người dùng 
     * @param Request $request
     * @return json
     */
    public function getCategoryOptions(Request $request)
    {
        extract($this->apiDefaultData);
        if($options = $this->repository->getCategoryOptions($request->all())){
            $data = $options;
            $status = true;
        }else{
            $message = 'Không có kết quả phù hợp';
        }

        return $this->json(compact(...$this->apiSystemVars));
    }

    public function prepareMoveToTrash(Request $request, $ids = []){
        $this->repository->withMoveToTrashQuery();
    }
    public function prepareDelete(Request $request, $ids = []){
        $this->repository->withMoveToTrashQuery();
        
    }

    public function beforeMoveToTrash($data)
    {
        
    }
}
