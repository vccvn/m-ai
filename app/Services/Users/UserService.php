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


    public function encryptCIScan($filename = null)
    {
        if ($filename) {
            $parts = explode('.', $filename);
            $ext = array_pop($parts);
            if ($ext != User::SCAN_ENCRYPT_FILE_EXTENSION) {
                $parts[] = User::SCAN_ENCRYPT_FILE_EXTENSION;
                $newFile = md5(uniqid(). rand(0, 000000). '-'. microtime(true)) . '.' . User::SCAN_ENCRYPT_FILE_EXTENSION;
                if (file_exists($f = storage_path('users/cccd/' . $filename))) {
                    $path = $f;
                } elseif (file_exists($f = public_path(content_path('users/cccd/' . $filename)))) {
                    $path = $f;
                } else return $filename;
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                $encrypt = HashService::encrypt($base64);

                file_put_contents($new = storage_path('users/cccd/' . $newFile), gzcompress($encrypt));
                if (file_exists($new)) {
                    unlink($path);
                    return $newFile;
                }
            }
        }
        return $filename;
    }

    public function decryptCIScan($filename = null)
    {
        if ($filename) {
            $parts = explode('.', $filename);
            $ext = array_pop($parts);
            if ($ext == User::SCAN_ENCRYPT_FILE_EXTENSION) {
                if (file_exists($f = storage_path('users/cccd/' . $filename))) {
                    try {
                        $data = file_get_contents($f);
                        $encrypt = @gzuncompress($data);
                        $base64 = HashService::decrypt($encrypt);
                        return $this->filemanager->getBase64Data($base64);
                    } catch (NotReportException $th) {
                        //throw $th;
                    }
                }
            }elseif (file_exists($f = public_path(content_path('users/cccd/' . $filename)))) {
                $type = pathinfo($f, PATHINFO_EXTENSION);
                $data = file_get_contents($f);
                return new Arr(compact('type', $data));
            }
        }
        return null;
    }
}
