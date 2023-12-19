<?php

namespace App\Http\Controllers\Admin\General;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminController;

use App\Repositories\Posts\CategoryRepository;
use App\Repositories\Metadatas\MetadataRepository;

use Gomee\Helpers\Arr;

class PostCategoryController extends AdminController
{
    protected $module = 'posts.categories';

    protected $moduleName = 'Danh mục';

    protected $redirectRoute = 'posts.categories.update';


    protected $dynamic = null;

    /**
     * metadata
     *
     * @var MetadataRepository
     */
    public $metadataRepository;


    /**
     * @var string $formLayout
    */
    // protected $formLayout = 'forms.grid';


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(CategoryRepository $CategoryRepository, MetadataRepository $metadataRepository)
    {
        if(admin_check_dynamic()){
            $this->activeMenu(get_web_data('dynamic')->slug);
        }
        $this->repository = $CategoryRepository;
        $this->repository->dynamicInit();
        $this->metadataRepository = $metadataRepository;

        $this->init();

    }


    public function start()
    {
        admin_breadcrumbs([
            [
                'url' => admin_dynamic_url('categories.list'),
                'text' => 'Danh mục'
            ]
        ]);
    }


    /**
     * chuan bi truoc cho hiển thị crud form
     * @param Request $request
     * @param Arr $config
     * @param Arr $attrs
     * @param Arr $vars
     *
     * @return void
     */
    public function prepareGetCrudForm(Request $request, Arr $config, Arr $attrs, Arr $vars)
    {
        $attrs->action = admin_dynamic_url('categories.save');
        $this->cancelButtonUrl = admin_dynamic_url('categories.list');

        admin_breadcrumbs([
            [
                'url' => admin_dynamic_url('categories.create'),
                'text' => 'Thêm danh mục'
            ]
        ]);
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
     * can thiệp trước khi tạo mới
     * @param Illuminate\Http\Request $request
     * @param Arr $data dũ liệu đã được validate
     * @return void
     */
    protected function beforeCreate(Request $request, $data)
    {
        $data->dynamic_id = get_web_data('dynamic')->id;
    }

    /**
     * can thiệp trước khi luu
     * @param Request $request
     * @param Arr $data dũ liệu đã được validate
     * @return void
     */
    protected function beforeSave(Request $request, $data)
    {
        $data->slug = $this->repository->getSlug(
            $request->slug ?? $request->name,
            $request->id
        );
        // $this->uploadImageAttachFile($request, $data, 'featured_image', get_content_path('categories'), 400, 300);

        if(is_numeric($data->featured_image)){
            $data->featured_image  = '@media:'.$data->featured_image;
        }
    }




    /**
     * can thiệp sau khi luu
     * @param Illuminate\Http\Request $request
     * @param App\Models\Model $model bản ghi sau khi được lưu trữ
     * @return void
     */
    public function afterSave(Request $request, $result)
    {
        $this->redirectRouteParams = [
            'dynamic' => $request->route('dynamic'),
            'id' => $result->id
        ];
    }


    public function beforeGetListData($request)
    {
        $this->repository->withCountPostQuery();
    }


    public function beforeGetListView($request, $data)
    {
        add_js_data('category_map_data', [
            'map' => get_post_category_map(),
            'default_parent' => 'Không'
        ]);
    }

    /**
     * danh sach option
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

}
