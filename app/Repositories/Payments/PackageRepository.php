<?php

namespace App\Repositories\Payments;

use Gomee\Repositories\BaseRepository;
use App\Masks\Payments\UploadPackageMask;
use App\Masks\Payments\UploadPackageCollection;
use App\Models\UploadPackage;
use App\Validators\Payments\PackageValidator;
use Illuminate\Http\Request;

/**
 * @method UploadPackageCollection<UploadPackageMask>|UploadPackage[] filter(Request $request, array $args = []) lấy danh sách UploadPackage được gán Mask
 * @method UploadPackageCollection<UploadPackageMask>|UploadPackage[] getFilter(Request $request, array $args = []) lấy danh sách UploadPackage được gán Mask
 * @method UploadPackageCollection<UploadPackageMask>|UploadPackage[] getResults(Request $request, array $args = []) lấy danh sách UploadPackage được gán Mask
 * @method UploadPackageCollection<UploadPackageMask>|UploadPackage[] getData(array $args = []) lấy danh sách UploadPackage được gán Mask
 * @method UploadPackageCollection<UploadPackageMask>|UploadPackage[] get(array $args = []) lấy danh sách UploadPackage
 * @method UploadPackageCollection<UploadPackageMask>|UploadPackage[] getBy(string $column, mixed $value) lấy danh sách UploadPackage
 * @method UploadPackageMask|UploadPackage getDetail(array $args = []) lấy UploadPackage được gán Mask
 * @method UploadPackageMask|UploadPackage detail(array $args = []) lấy UploadPackage được gán Mask
 * @method UploadPackageMask|UploadPackage find(integer $id) lấy UploadPackage
 * @method UploadPackageMask|UploadPackage findBy(string $column, mixed $value) lấy UploadPackage
 * @method UploadPackageMask|UploadPackage first(string $column, mixed $value) lấy UploadPackage
 * @method UploadPackage create(array $data = []) Thêm bản ghi
 * @method UploadPackage update(integer $id, array $data = []) Cập nhật
 */
class PackageRepository extends BaseRepository
{
    /**
     * class chứ các phương thức để validate dử liệu
     * @var string $validatorClass 
     */
    protected $validatorClass = PackageValidator::class;
    /**
     * tên class mặt nạ. Thường có tiền tố [tên thư mục] + \ vá hậu tố Mask
     *
     * @var string
     */
    protected $maskClass = UploadPackageMask::class;

    /**
     * tên collection mặt nạ
     *
     * @var string
     */
    protected $maskCollectionClass = UploadPackageCollection::class;


    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\UploadPackage::class;
    }

}