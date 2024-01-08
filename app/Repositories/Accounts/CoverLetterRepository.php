<?php

namespace App\Repositories\Accounts;

use Gomee\Repositories\BaseRepository;
use App\Masks\Accounts\CoverLetterMask;
use App\Masks\Accounts\CoverLetterCollection;
use App\Models\CoverLetter;
use App\Validators\Accounts\CoverLetterValidator;
use Illuminate\Http\Request;

/**
 * @method CoverLetterCollection<CoverLetterMask>|CoverLetter[] filter(Request $request, array $args = []) lấy danh sách CoverLetter được gán Mask
 * @method CoverLetterCollection<CoverLetterMask>|CoverLetter[] getFilter(Request $request, array $args = []) lấy danh sách CoverLetter được gán Mask
 * @method CoverLetterCollection<CoverLetterMask>|CoverLetter[] getResults(Request $request, array $args = []) lấy danh sách CoverLetter được gán Mask
 * @method CoverLetterCollection<CoverLetterMask>|CoverLetter[] getData(array $args = []) lấy danh sách CoverLetter được gán Mask
 * @method CoverLetterCollection<CoverLetterMask>|CoverLetter[] get(array $args = []) lấy danh sách CoverLetter
 * @method CoverLetterCollection<CoverLetterMask>|CoverLetter[] getBy(string $column, mixed $value) lấy danh sách CoverLetter
 * @method CoverLetterMask|CoverLetter getDetail(array $args = []) lấy CoverLetter được gán Mask
 * @method CoverLetterMask|CoverLetter detail(array $args = []) lấy CoverLetter được gán Mask
 * @method CoverLetterMask|CoverLetter find(integer $id) lấy CoverLetter
 * @method CoverLetterMask|CoverLetter findBy(string $column, mixed $value) lấy CoverLetter
 * @method CoverLetterMask|CoverLetter first(string $column, mixed $value) lấy CoverLetter
 * @method CoverLetter create(array $data = []) Thêm bản ghi
 * @method CoverLetter update(integer $id, array $data = []) Cập nhật
 */
class CoverLetterRepository extends BaseRepository
{
    /**
     * class chứ các phương thức để validate dử liệu
     * @var string $validatorClass
     */
    protected $validatorClass = CoverLetterValidator::class;
    /**
     * tên class mặt nạ. Thường có tiền tố [tên thư mục] + \ vá hậu tố Mask
     *
     * @var string
     */
    protected $maskClass = CoverLetterMask::class;

    /**
     * tên collection mặt nạ
     *
     * @var string
     */
    protected $maskCollectionClass = CoverLetterCollection::class;


    protected $columns = [
        'id' => 'cover_letters.id',
        'name' => 'users.name',
        'email' => 'users.email',
        'username' => 'users.username',
        'phone_number' => 'users.phone_number',
        'status' => 'cover_letters.status',
        'message' => 'cover_letters.message',
        'message' => 'cover_letters.message',
        'created_at' => 'cover_letters.created_at',
        'updated_at' => 'cover_letters.updated_at',

    ];
    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\CoverLetter::class;
    }

    public function init()
    {
        $this
            ->setSearchable([
                'name' => 'users.name',
                'username' => 'users.username',
                'email' => 'users.email',
                'phone_number' => 'users.phone_number',
            ])
            ->searchRule([
                'users.name' => [
                    '{query}%',
                    '% {query}%',
                ],
                'users.email' => [
                    '{query}%'
                ],
                'users.phone_number' => [
                    '{query}%',
                    '%{query}'
                ]
            ])
            ->setSelectable($this->columns)
            ->setJoinable([
                ['join', 'users', 'users.id', '=', 'cover_letters.user_id']
            ]);
    }
}
