<?php

namespace App\Http\Controllers\Admin\Ecommerce;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminController;
use App\Models\Order;
use Gomee\Helpers\Arr;

use App\Repositories\Orders\OrderRepository;
use App\Repositories\Products\ProductRepository;
use App\Repositories\Orders\OrderItemRepository;
use App\Repositories\Customers\CustomerRepository;

use App\Repositories\Orders\OrderAddressRepository;
use Illuminate\Support\Facades\DB;

/**
 * @property OrderRepository $repository
 */
class OrderController extends AdminController
{
    protected $module = 'orders';

    protected $moduleName = 'Đơn hàng';

    protected $flashMode = true;


    protected $statusList = [];

    protected $paymentStatusList = [];

    protected $deliveryStatusList = [];

    /**
     * @var array $items
     */
    public $items = [];


    /**
     * quản lý địa chỉ của order
     *
     * @var \App\Repositories\Orders\OrderAddressRepository
     */
    protected $orderAddressRepository = null;
    /**
     *
     *
     * @var OrderRepository
     */
    public $orderRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        OrderRepository $orderRepository,
        OrderItemRepository $orderItemRepository,
        ProductRepository $productRepository,
        CustomerRepository $customerRepository,
        OrderAddressRepository $orderAddressRepository
    ) {
        $this->repository = $orderRepository;
        $this->productRepository = $productRepository;
        $this->orderItemRepository = $orderItemRepository;
        $this->customerRepository = $customerRepository;
        $this->orderAddressRepository = $orderAddressRepository;
        $this->statusList = Order::STATUS_SLUGS;
        $this->paymentStatusList = Order::getDeliveryStatusSlugs();
        $this->deliveryStatusList = Order::getDeliveryStatusSlugs();
        $this->init();
    }

    /**
     * khơi chạy controller
     */
    public function start()
    {
        /* them data cho js xư ly */
        add_js_data('order_data', [
            'urls' => [
                'change_status' => $this->getModuleRoute('change-status'),
                'change_payment_status' => $this->getModuleRoute('change-payment-status'),
                'change_delivery_status' => $this->getModuleRoute('change-delivery-status'),
                'get_detail' => $this->getModuleRoute('resource-detail'),
                'region_options' => route("admin.location.regions.options"),
                'district_options' => route("admin.location.districts.options"),
                'ward_options' => route("admin.location.wards.options"),
            ],
            'data' => [
                'status' => Order::getStatusLabels(),
                'payment_status' => Order::getPaymentStatusLabels(),
                'delivery_status' => Order::getDeliveryStatusLabels(),
                'payment_method_id' => get_payment_method_id_options()
            ]
        ]);
    }


    /**
     * can thiệp xử lý trước khi lưu
     * @param Request $request
     * @param Order $Order
     * @param Arr $data
     *
     * @return void
     */
    public function beforeSave(Request $request, Arr $data)
    {
        $orderDetail = $this->orderItemRepository->parseItems(is_array($data->items) ? $data->items : []);
        $this->items = $orderDetail['items'];
        $data->total_money = $orderDetail['total_money'];
        if (!$data->customer_id) {
            $customer = $this->customerRepository->createDataIfNotExists($data->prefix('billing_', null, null, true));
            $data->customer_id = $customer->id;
        }
        $data->shipping_fee = !$data->shipping_fee || $data->shipping_fee <= 0 ? 0 : $data->shipping_fee;

        $data->tax = !$data->tax || $data->tax <= 0 ? 0 : $data->tax;
    }


    /**
     * lưu các dữ liệu liên quan
     * @param Request $request
     * @param Order $order
     * @param Arr $data
     *
     * @return void
     */
    public function afterSave(Request $request, Order $order, Arr $data)
    {
        $this->orderItemRepository->saveOrderItems($order->id, $this->items);
        $billing = $data->prefix('billing_', null, null, true);
        $shipping = $data->prefix('shipping_', null, null, true);
        if (!$data->ship_to_different_address) {
            $shipping = $billing;
        }
        $this->orderAddressRepository->updateAddress($order->id, 'billing', $billing);
        $this->orderAddressRepository->updateAddress($order->id, 'shipping', $shipping);
        $this->repository->updateOrderData($order);
    }


    /**
     * lấy thông tin sản phẩm và thuộc tính
     * @param Request $request
     *
     * @return json
     */
    public function getProductInput(Request $request)
    {
        extract($this->apiDefaultData);
        if ($itemData = $this->productRepository->getOrderInputData($request->product_id)) {
            $status = true;
            $data = $this->view(
                'forms.templates.order-item',
                array_merge($itemData, [
                    'name' => $request->name,
                    'index' => $request->index,
                    'quantity' => 1
                ])
            )->render();
        }

        return $this->json(compact(...$this->apiSystemVars));
    }


    /**
     * lấy danh sach đơn hàng theo trạng thái
     * @param Request $request
     * @return view
     */
    public function getListByStatus(Request $request)
    {
        if ($stt = get_array_element($request->list, $this->statusList)) {
            $this->activeMenu('list-', $stt['value']);
            return $this->getFlashModeListData($request, ['status' => $stt['key']], ['list_group' => $stt['value']]);
        } else {
            return $this->getList($request);
        }
    }

    /**
     * thay đổi trạng thái đơn hàng
     * @param Request
     * @return json
     */
    public function changeStatus(Request $request)
    {
        extract($this->apiDefaultData);
        if (!($request->id && array_key_exists($request->status, $this->statusList) && $old = $this->repository->getOrderWithItems($request->id)))
            return $this->json(compact(...$this->apiSystemVars));
        $newStatus = $request->status;
        $oldStatus = $old->status;
        $updateData = ['status' => $request->status];
        if ($request->status == Order::COMPLETED) $updateData['completed_at'] = date('Y-m-d H:i:s');
        if (!($updated = $this->repository->update($request->id, $updateData)) || !($oldStatus != $newStatus))
            return $this->json(compact(...$this->apiSystemVars));

        $status = true;
        $message = 'Cập nhật trạng thái đơn hàng thành công!';
        $data = $updated;
        if ($old->delivery_status != Order::DELIVERY_PENDING)
            return $this->json(compact(...$this->apiSystemVars));

        if ($newStatus == Order::CANCELED) {
            $this->repository->restoreProductInStoreAvailable($old);
        } elseif ($oldStatus == Order::CANCELED) {
            $this->repository->restoreProductInStoreAvailable($old, 'dec');
        }
        $this->repository->sendMailAlertOrderStatus($updated->id);

        return $this->json(compact(...$this->apiSystemVars));
    }

    /**
     * thay đổi trạng thái đơn hàng
     * @param Request
     * @return json
     */
    public function changePaymentStatus(Request $request)
    {
        extract($this->apiDefaultData);
        if ($request->id && array_key_exists($request->status, $this->paymentStatusList) && $this->repository->find($request->id)) {
            $updateData = ['payment_status' => $request->status];

            if ($updated = $this->repository->update($request->id, $updateData)) {
                $status = true;
                $message = 'Cập nhật trạng thái đơn hàng thành công!';
                $data = $updated;
                // $this->repository->sendMailAlertOrderStatus($updated->id);
            }
        }
        return $this->json(compact(...$this->apiSystemVars));
    }

    /**
     * thay đổi trạng thái đơn hàng
     * @param Request
     * @return json
     */
    public function changeDeliveryStatus(Request $request)
    {
        extract($this->apiDefaultData);
        if ($request->id && array_key_exists($request->status, $this->deliveryStatusList) && $this->repository->find($request->id)) {
            $updateData = ['delivery_status' => $request->status];
            if ($updated = $this->repository->update($request->id, $updateData)) {
                $status = true;
                $message = 'Cập nhật trạng thái đơn hàng thành công!';
                $data = $updated;
                // $this->repository->sendMailAlertOrderStatus($updated->id);
            }
        }
        return $this->json(compact(...$this->apiSystemVars));
    }

    /**
     * thay đổi trạng thái đơn hàng
     * @param Request
     * @return json
     */
    public function getOrderDetail(Request $request)
    {
        extract($this->apiDefaultData);
        if ($request->id && $detail = $this->repository->mode('mask')->withFullData()->detail($request->id)) {
            $data = $detail->toArray();
            $status = true;
        }
        return $this->json(compact(...$this->apiSystemVars));
    }


    /**
     * tim kiếm thông
     * @param Request $request
     * @return json
     */
    public function getSelectOptions(Request $request)
    {
        extract($this->apiDefaultData);

        if ($options = $this->repository->getSelectOptions($request, ['@limit' => 10], "code")) {
            $data = $options;
            $status = true;
        } else {
            $message = 'Không có kết quả phù hợp';
        }

        return $this->json(compact(...$this->apiSystemVars));
    }

    // public function beforeGetListData($request)
    // {
    //     DB::enableQueryLog();
    // }
    // public function beforeGetListView($request, $data)
    // {
    //     dd(DB::getQueryLog());
    // }

}
