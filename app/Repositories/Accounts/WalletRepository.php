<?php

namespace App\Repositories\Accounts;

use Gomee\Repositories\BaseRepository;
use App\Masks\Accounts\WalletMask;
use App\Masks\Accounts\WalletCollection;
use App\Models\Wallet;
use App\Validators\Accounts\WalletValidator;
use Illuminate\Http\Request;

/**
 * @method WalletCollection<WalletMask>|Wallet[] filter(Request $request, array $args = []) lấy danh sách Wallet được gán Mask
 * @method WalletCollection<WalletMask>|Wallet[] getFilter(Request $request, array $args = []) lấy danh sách Wallet được gán Mask
 * @method WalletCollection<WalletMask>|Wallet[] getResults(Request $request, array $args = []) lấy danh sách Wallet được gán Mask
 * @method WalletCollection<WalletMask>|Wallet[] getData(array $args = []) lấy danh sách Wallet được gán Mask
 * @method WalletCollection<WalletMask>|Wallet[] get(array $args = []) lấy danh sách Wallet
 * @method WalletCollection<WalletMask>|Wallet[] getBy(string $column, mixed $value) lấy danh sách Wallet
 * @method WalletMask|Wallet getDetail(array $args = []) lấy Wallet được gán Mask
 * @method WalletMask|Wallet detail(array $args = []) lấy Wallet được gán Mask
 * @method WalletMask|Wallet find(integer $id) lấy Wallet
 * @method WalletMask|Wallet findBy(string $column, mixed $value) lấy Wallet
 * @method WalletMask|Wallet first(string $column, mixed $value) lấy Wallet
 * @method Wallet create(array $data = []) Thêm bản ghi
 * @method Wallet update(integer $id, array $data = []) Cập nhật
 */
class WalletRepository extends BaseRepository
{
    /**
     * class chứ các phương thức để validate dử liệu
     * @var string $validatorClass
     */
    protected $validatorClass = WalletValidator::class;
    /**
     * tên class mặt nạ. Thường có tiền tố [tên thư mục] + \ vá hậu tố Mask
     *
     * @var string
     */
    protected $maskClass = WalletMask::class;

    /**
     * tên collection mặt nạ
     *
     * @var string
     */
    protected $maskCollectionClass = WalletCollection::class;


    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\Wallet::class;
    }

    /**
     * get
     *
     * @param int $user_id
     * @return Wallet
     */
    public function createDefaultWallet($user_id){
        if(!($wallet = $this->first($p = ['user_id' => $user_id])))
            $wallet = $this->create($p);
        return $wallet;
    }

}
