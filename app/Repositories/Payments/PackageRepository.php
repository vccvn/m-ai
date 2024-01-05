<?php

namespace App\Repositories\Payments;

use Gomee\Repositories\BaseRepository;
use App\Masks\Payments\PackageMask;
use App\Masks\Payments\PackageCollection;
use App\Models\ServicePackage;
use App\Validators\Payments\PackageValidator;
use Illuminate\Http\Request;

/**
 * @method PackageCollection<PackageMask>|ServicePackage[] filter(Request $request, array $args = []) lấy danh sách UploadPackage được gán Mask
 * @method PackageCollection<PackageMask>|ServicePackage[] getFilter(Request $request, array $args = []) lấy danh sách UploadPackage được gán Mask
 * @method PackageCollection<PackageMask>|ServicePackage[] getResults(Request $request, array $args = []) lấy danh sách UploadPackage được gán Mask
 * @method PackageCollection<PackageMask>|UploadPackage[] getData(array $args = []) lấy danh sách UploadPackage được gán Mask
 * @method PackageCollection<PackageMask>|UploadPackage[] get(array $args = []) lấy danh sách UploadPackage
 * @method PackageCollection<PackageMask>|UploadPackage[] getBy(string $column, mixed $value) lấy danh sách UploadPackage
 * @method PackageMask|UploadPackage getDetail(array $args = []) lấy UploadPackage được gán Mask
 * @method PackageMask|UploadPackage detail(array $args = []) lấy UploadPackage được gán Mask
 * @method PackageMask|UploadPackage find(integer $id) lấy UploadPackage
 * @method PackageMask|UploadPackage findBy(string $column, mixed $value) lấy UploadPackage
 * @method PackageMask|UploadPackage first(string $column, mixed $value) lấy UploadPackage
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
    protected $maskClass = PackageMask::class;

    /**
     * tên collection mặt nạ
     *
     * @var string
     */
    protected $maskCollectionClass = PackageCollection::class;


    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\ServicePackage::class;
    }

}
