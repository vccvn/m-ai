<?php

namespace App\Http\Controllers\Admin\Ecommerce;

use App\Http\Controllers\Admin\AdminController;
use App\Models\ProductCollection;
use App\Repositories\Categories\CategoryRefRepository;
use App\Repositories\Categories\CategoryRepository;
use App\Repositories\Files\FileRefRepository;
use App\Repositories\Metadatas\MetadataRepository;
use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\Products\CollectionRepository;
use App\Repositories\Products\LabelRefRepository;
use App\Repositories\Tags\TagRefRepository;

class ProductCollectionController extends AdminController
{
    protected $module = 'products.collections';

    protected $moduleName = 'Bộ sưu tập sản phẩm';

    protected $flashMode = true;

    /**
     * repository chinh
     *
     * @var CollectionRepository
     */
    public $repository;
    
    /**
     * @var TagRefRepository $tagRefRepository
     */
    protected $tagRefRepository;
    /**
     * @var CategoryRefRepository $tagRefRepository
     */
    protected $categoryRefRepository;
    
    /**
     * @var LabelRefRepository $tagRefRepository
     */
    protected $labelRefRepository;

    /**
     * lien ket file
     *
     * @var FileRefRepository
     */
    protected $fileRefRepository;
    /**
     * @var MetadataRepository $metadataRepository
     */
    protected $metadataRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        CollectionRepository $repository,
        CategoryRefRepository $categoryRefRepository,
        TagRefRepository $tagRefRepository,
        LabelRefRepository $labelRefRepository,
        FileRefRepository $fileRefRepository,
        MetadataRepository $metadataRepository
    )
    {
        $this->repository = $repository;
        $this->categoryRefRepository = $categoryRefRepository;
        $this->tagRefRepository = $tagRefRepository;
        $this->labelRefRepository = $labelRefRepository;
        $this->fileRefRepository = $fileRefRepository;
        $this->metadataRepository = $metadataRepository;
        $this->init();
    }

    /**
     * can thiệp trước khi luu
     * @param Request $request
     * @param Arr $data dũ liệu đã được validate
     * @return void
     */
    protected function beforeSave(Request $request, $data)
    {
        $slug = str_slug($request->custom_slug? $request->slug : $request->name);
        $data->slug = $this->repository->getSlug(
            $slug?$slug : uniqid(),
            $request->id
        );
        
        if($data->category_id){
            // $data->category_map = $this->repository->makeCategoryMap($data->category_id);

        }
    }

    
    /**
     * lưu các dữ liệu liên quan như thuộc tính, meta, gallery
     * @param Request $request
     * @param ProductCollection $productCollection
     * @param Crazy\Helpers\Arr $data dữ liệu từ input đã dược kiểm duyệt
     *
     * @return void
     */
    public function afterSave(Request $request, $collection, $data)
    {
        $this->tagRefRepository->updateTagRef(ProductCollection::REF_KEY, $collection->id, $data->tags??[]);
        // meta data
        $this->metadataRepository->saveOne(ProductCollection::REF_KEY, $collection->id, 'custom_slug', $data->custom_slug);

        $this->fileRefRepository->updateFileRef(ProductCollection::REF_KEY, $collection->id, $data->featured_image?[$data->featured_image]:[]);

        $this->labelRefRepository->updateLabelRefs(ProductCollection::REF_KEY, $collection->id, $data->labels??[]);
        $this->categoryRefRepository->updateCategoryRef(ProductCollection::REF_KEY, $collection->id, $data->categories??[]);
        
    }

    
    /**
     * tim kiếm thông tin sản phẩm
     * @param Request $request
     * @return json
     */
    public function getCollectionTagData(Request $request)
    {
        extract($this->apiDefaultData);

        if($options = $this->repository->getCollectionTagData($request, ['@limit'=>10])){
            $data = $options;
            $status = true;
        }else{
            $message = 'Không có kết quả phù hợp';
        }

        return $this->json(compact(...$this->apiSystemVars));
    }

}
