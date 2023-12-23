<?php

namespace App\Repositories\GPT;

use Gomee\Repositories\BaseRepository;
use App\Masks\GPT\CriteriaMask;
use App\Masks\GPT\CriteriaCollection;
use App\Models\GPTCriteria;
use App\Validators\GPT\CriteriaValidator;
use Illuminate\Http\Request;

/**
 * @method CriteriaCollection<CriteriaMask>|GPTCriteria[] filter(Request $request, array $args = []) lấy danh sách GPTCriteria được gán Mask
 * @method CriteriaCollection<CriteriaMask>|GPTCriteria[] getFilter(Request $request, array $args = []) lấy danh sách GPTCriteria được gán Mask
 * @method CriteriaCollection<CriteriaMask>|GPTCriteria[] getResults(Request $request, array $args = []) lấy danh sách GPTCriteria được gán Mask
 * @method CriteriaCollection<CriteriaMask>|GPTCriteria[] getData(array $args = []) lấy danh sách GPTCriteria được gán Mask
 * @method CriteriaCollection<CriteriaMask>|GPTCriteria[] get(array $args = []) lấy danh sách GPTCriteria
 * @method CriteriaCollection<CriteriaMask>|GPTCriteria[] getBy(string $column, mixed $value) lấy danh sách GPTCriteria
 * @method CriteriaMask|GPTCriteria getDetail(array $args = []) lấy GPTCriteria được gán Mask
 * @method CriteriaMask|GPTCriteria detail(array $args = []) lấy GPTCriteria được gán Mask
 * @method CriteriaMask|GPTCriteria find(integer $id) lấy GPTCriteria
 * @method CriteriaMask|GPTCriteria findBy(string $column, mixed $value) lấy GPTCriteria
 * @method CriteriaMask|GPTCriteria first(string $column, mixed $value) lấy GPTCriteria
 * @method GPTCriteria create(array $data = []) Thêm bản ghi
 * @method GPTCriteria update(integer $id, array $data = []) Cập nhật
 */
class CriteriaRepository extends BaseRepository
{
    /**
     * class chứ các phương thức để validate dử liệu
     * @var string $validatorClass 
     */
    protected $validatorClass = CriteriaValidator::class;
    /**
     * tên class mặt nạ. Thường có tiền tố [tên thư mục] + \ vá hậu tố Mask
     *
     * @var string
     */
    protected $maskClass = CriteriaMask::class;

    /**
     * tên collection mặt nạ
     *
     * @var string
     */
    protected $maskCollectionClass = CriteriaCollection::class;


    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\GPTCriteria::class;
    }

}