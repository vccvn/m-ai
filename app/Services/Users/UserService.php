<?php

namespace App\Services\Users;

use App\Exceptions\NotReportException;
use App\Models\Award;
use App\Models\AwardCheckLog;
use App\Models\User;
use App\Models\Voucher;
use App\Services\Service;

use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\Users\UserRepository;
use App\Services\Encryptions\HashService;
use App\Services\Mailers\Mailer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * @property-read VoucherRepository $voucherRepository
 * @property-read ExamTestRepository $examTestRepository
 * @property-read CheckLogRepository $checkLogRepository
 * @property-read LogItemRepository $logItemRepository
 */
class UserService extends Service
{
    protected $module = 'users';

    protected $moduleName = 'User';

    protected $flashMode = true;

    protected $topUsers = [];

    protected $userRankData = [];


    /**
     * repository chinh
     *
     * @var UserRepository
     */
    public $repository;

    /**
     * Create a new Service instance.
     *
     * @return void
     */
    public function __construct(
        UserRepository $repository
    ) {
        $this->repository = $repository;
        $this->init();
    }

    public function addMonth($user_id, $month = 0)
    {
        if (!$month || $month < 1)
            return false;
        elseif (is_a($user_id, User::class))
            $user = $user_id;
        elseif (is_a($user_id, User::class))
            $user = $user_id;
        elseif ($u = $this->repository->find($user_id))
            $user = $u;
        else
            return false;
        $time = strtotime($user->expired_at);
        if ($time < time())
            $datetime = Carbon::now()->addMonths($month);
        else
            $datetime = Carbon::parse($user->expired_at)->addMonths($month);
        return $this->update($user->id, ['expired_at' => $datetime->toDateTimeString()]);
    }
}
