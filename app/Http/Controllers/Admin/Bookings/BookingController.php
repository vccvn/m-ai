<?php

namespace App\Http\Controllers\Admin\Bookings;

use App\Http\Controllers\Admin\AdminController;
use App\Models\Booking;
use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\Bookings\BookingRepository;
use App\Services\Bookings\BookingService;
use Gomee\Html\Menu;

class BookingController extends AdminController
{
    protected $module = 'bookings';

    protected $moduleName = 'Lịch hẹn';

    protected $moduleTitle = 'Lịch hẹn';

    protected $flashMode = true;

    /**
     * repository chinh
     *
     * @var BookingRepository
     */
    public $repository;

    /**
     * @var BookingService
     */
    public $bookingService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(BookingRepository $repository, BookingService $bookingService)
    {
        $this->repository = $repository;
        $this->bookingService = $bookingService;
        $this->init();
    }

    public function beforeGetListData($request)
    {
        $this->repository->paginate(50);
    }

    public function getIdleList(Request $request) {
        $this->moduleTitle = 'Lịch hẹn chờ xử lý';
        $this->activeMenu($this->module . '.idle');
        Menu::removeActiveKey($this->module.'.list');
        $this->repository->where('bookings.status', Booking::STATUS_IDLE);
        return $this->getList($request);
    }
    public function getConfirmedList(Request $request) {
        $this->moduleTitle = 'Lịch hẹn đã xác nhận';
        $this->activeMenu($this->module . '.confirmed');
        Menu::removeActiveKey($this->module.'.list');
        $this->repository->where('bookings.status', Booking::STATUS_CONFIRMED);
        return $this->getList($request);
    }
    public function getCompletedList(Request $request) {
        $this->moduleTitle = 'Lịch hẹn đã hoàn thành';
        $this->activeMenu($this->module . '.completed');
        Menu::removeActiveKey($this->module.'.list');
        $this->repository->where('bookings.status', Booking::STATUS_COMPLETED);
        return $this->getList($request);
    }
    public function getCanceledList(Request $request) {
        $this->moduleTitle = 'Lịch hẹn đã huỷ';
        $this->activeMenu($this->module . '.canceled');
        Menu::removeActiveKey($this->module.'.list');
        $this->repository->where('bookings.status', Booking::STATUS_CANCELED);
        return $this->getList($request);
    }

    public function beforeGetListView($request, $data)
    {
        add_js_data('booking_data', [
            'urls' => [
                'change_status' => route('admin.bookings.change-status')
            ]
        ]);
        $this->moduleName = $this->moduleTitle;
    }

    public function changeStatus(Request $request){
        extract($this->apiDefaultData);
        if(!$request->id || !$request->status)
            $message = 'Không có thông tin lịch hẹn';
        elseif(!($booking = $this->bookingService->changeStatus($request->id, $request->status)))
            $message = 'Không thể cập nhật lịch hẹn';
        else{
            $status = true;
            $data = $booking;
        }

        return $this->json(compact(...$this->apiSystemVars));
    }


}
