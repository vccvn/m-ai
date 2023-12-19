<?php

namespace App\Repositories\Orders;

use Gomee\Repositories\BaseRepository;
/**
 * validator 
 * 
 */
use App\Validators\Orders\CartItemValidator;
use App\Masks\Orders\CartItemMask;
use App\Masks\Orders\CartItemCollection;
use Gomee\Helpers\Arr;

class CartItemRepository extends BaseRepository
{
    /**
     * class chứ các phương thức để validate dử liệu
     * @var string $validatorClass 
     */
    protected $validatorClass = CartItemValidator::class;
    /**
     * tên class mặt nạ. Thường có tiền tố [tên thư mục] + \ vá hậu tố Mask
     *
     * @var string
     */
    protected $maskClass = CartItemMask::class;

    /**
     * tên collection mặt nạ
     *
     * @var string
     */
    protected $maskCollectionClass = CartItemCollection::class;


    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\CartItem::class;
    }

    /**
     * loc và tinh gia tien don hang
     * @param array $items
     */
    public function parseItems(array $item_list = [])
    {
        $items = [];
        $total_money = 0;
        if(count($item_list)){
            $productRepository = app(ProductRepository::class);
            foreach($item_list as $itm){
                $item = new Arr($itm);
                // nếu tìm thấy sản phẩm
                if($product = $productRepository->findBy('id', $item->product_id)){
                    // nạp một số thông tin từ sản phẩm sang 
                    // $item->cart_id = $cart_id;
                    $item->list_price = $product->list_price;
                    $item->final_price = $product->getFinalPrice();
                    $total_money += $item->final_price * $item->quantity;
                    if($product->hasPromo()){
                        $item->note = "Sản phẩm dược hưởng khuyến mãi";
                    }
                    $key = $item->product_id;
                    // xử lý thuộc tính nếu có
                    if($item->attr_values){
                        $av = array_values($item->attr_values);
                        sort($av);
                        $item->attr_values = $av;
                        $key .= '.'. implode('-', $av);
                    }
                    // kiểm tra sản phẩm trùng lặp
                    if(array_key_exists($key, $items)){
                        // trong trường hợp item trước đó đã có id thì + quantity vào key trước đó
                        if($items[$key]->id){
                            $items[$key]->quantity+=$item->quantity;
                        }
                        // trường hợp item hiện tại có id thì + quan tity vào item6 hiện tại và thế chỗ cho item trong mảng data
                        elseif ($item->id) {
                            $item->quantity+=$items[$key]->quantity;
                            $items[$key] = $item;
                        }
                        // trường hợp mới toanh không có item id thì cộng dồn
                        else{
                            $items[$key]->quantity+=$item->quantity;
                        }
                            
                    }else{
                        $items[$key] = $item;
                    }                    
                }
            }
        }

        return compact('items', 'total_money');
    }

    /**
     * cập nhật hoặc thêm mới cart item
     * @param int $cart_id Mã đơn hàng
     * @param array $items Danh sách item [['id' => $cart_item_id, 'product_id' => $product_id, 'quantity' => $quantity, 'attributes' => []]]
     * 
     * @return bool
     */
    public function saveCartItems(int $cart_id, array $items = [])
    {
        $ignore = [];
        $data = [];
        if(count($items)){
            foreach ($items as $key => $item) {
                if($item->id){
                    $ignore[] = $item->id;
                }
                $item->cart_id = $cart_id;
                $data[$key] = $item;
            }
        }
        if(count($list = $this->getBy('cart_id', $cart_id))){
            foreach ($list as $cartItem) {
                if(!in_array($cartItem->id, $ignore)){
                    $cartItem->delete();
                }
            }
        }
        $dataSaved = [];
        if($data){
            foreach ($data as $key => $cartItem) {
                if(!$cartItem->id) $cartItem->remove('id');
                $itemdata = $cartItem->all();

                $dataSaved[] = $this->save($itemdata, $cartItem->id);
            }
        }
        return $dataSaved;
    }

    public function beforeSave($data)
    {
        if(isset($data['attr_values']) && is_array($data['attr_values'])){
            $data['attr_values'] = implode('-', $data['attr_values']);
        }
        return $data;
    }
}