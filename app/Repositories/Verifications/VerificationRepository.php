<?php

namespace App\Repositories\Verifications;

use Gomee\Repositories\BaseRepository;
use App\Masks\Verifications\VerificationMask;
use App\Masks\Verifications\VerificationCollection;
use App\Models\Verification;
use App\Validators\Verifications\VerificationValidator;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * @method VerificationCollection<VerificationMask>|Verification[] filter(Request $request, array $args = []) lấy danh sách Verification được gán Mask
 * @method VerificationCollection<VerificationMask>|Verification[] getFilter(Request $request, array $args = []) lấy danh sách Verification được gán Mask
 * @method VerificationCollection<VerificationMask>|Verification[] getResults(Request $request, array $args = []) lấy danh sách Verification được gán Mask
 * @method VerificationCollection<VerificationMask>|Verification[] getData(array $args = []) lấy danh sách Verification được gán Mask
 * @method VerificationCollection<VerificationMask>|Verification[] get(array $args = []) lấy danh sách Verification
 * @method VerificationCollection<VerificationMask>|Verification[] getBy(string $column, mixed $value) lấy danh sách Verification
 * @method VerificationMask|Verification getDetail(array $args = []) lấy Verification được gán Mask
 * @method VerificationMask|Verification detail(array $args = []) lấy Verification được gán Mask
 * @method VerificationMask|Verification find(integer $id) lấy Verification
 * @method VerificationMask|Verification findBy(string $column, mixed $value) lấy Verification
 * @method VerificationMask|Verification first(string $column, mixed $value) lấy Verification
 * @method Verification create(array $data = []) Thêm bản ghi
 * @method Verification update(integer $id, array $data = []) Cập nhật
 */
class VerificationRepository extends BaseRepository
{
    /**
     * class chứ các phương thức để validate dử liệu
     * @var string $validatorClass
     */
    protected $validatorClass = VerificationValidator::class;
    /**
     * tên class mặt nạ. Thường có tiền tố [tên thư mục] + \ vá hậu tố Mask
     *
     * @var string
     */
    protected $maskClass = VerificationMask::class;

    /**
     * tên collection mặt nạ
     *
     * @var string
     */
    protected $maskCollectionClass = VerificationCollection::class;

    protected $errorMessage = '';

    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\Verification::class;
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * Tạo phiên xác thực
     *
     * @param string $type
     * @param string $method
     * @param string $send_to
     * @param string $ref
     * @param string $ref_id
     * @return Verification|null
     */
    public function createVerification($type = 'verify', $method = 'email', $send_to = null, $expired_in = 5, $ref = null, $ref_id = null)
    {
        if (!in_array($type, Verification::ALL_TYPE) || !in_array($method, Verification::ALL_METHOD)) {
            $this->errorMessage = 'Loại hoặc phương thức không hợp lệ';
            return false;
        } elseif (!$send_to) {
            $this->errorMessage = 'Thiếu ' . Verification::METHOD_LABELS[$method];
            return false;
        } elseif ($method == Verification::METHOD_EMAIL && !is_email($send_to))
            $code = null;
        $data = compact('type', 'method', 'send_to');
        if ($this->where('created_at', '>', Carbon::now()->subMinutes(Verification::TIME_BETWEEN)->toDateTimeString('millisecond'))->count($data) > 0) {
            $this->errorMessage = 'Bạn vừa gửi 2 yêu cầu liên tiếp trong vòng ' . Verification::TIME_BETWEEN . ' phút';
            return false;
        }
        $now = Carbon::now()->toDateTimeString('millisecond');

        if ($ref) {
            $data['ref'] = $ref;
            if ($ref_id) $data['ref_id'] = $ref_id;
        }
        $data['status'] = Verification::STATUS_IDLE;
        while (true) {
            $code = rand(0, 9).rand(0, 9).rand(0, 9).rand(0, 9).rand(0, 9).rand(0, 9);
            $data['code'] = $code;
            if (!$this->where('expired_at', '>', $now)->count($data)) {
                $data['expired_at'] = Carbon::now()->addMinutes($expired_in)->toDateTimeString('millisecond');
                return $this->create($data);
            }
        }
        return false;
    }
    /**
     * Tạo phiên xác thực
     *
     * @param string $email Địa chỉ email
     * @param string $type kiểu xác thực
     * @param integer $expired_in
     * @param string $ref
     * @param string $ref_id
     * @return Verification|null
     */
    public function createEmailVerification($email, $type = 'verify', $expired_in = 60, $ref = null, $ref_id = null)
    {
        return $this->createVerification($type, Verification::METHOD_EMAIL, $email, $expired_in, $ref, $ref_id);
    }

    /**
     * Tạo phiên xác thực
     *
     * @param string $email Địa chỉ email
     * @param string $type kiểu xác thực
     * @param integer $expired_in
     * @param string $ref
     * @param string $ref_id
     * @return Verification|null
     */
    public function createPhoneVerification($phone, $type = 'verify', $expired_in = 3, $ref = null, $ref_id = null)
    {
        return $this->createVerification($type, Verification::METHOD_PHONE, $phone, $expired_in, $ref, $ref_id);
    }

    /**
     * Undocumented function
     *
     * @param Verification $verification
     * @return Verification
     */
    public function updateVerified(Verification $verification)
    {

        $verification->code = null;
        $verification->status = Verification::STATUS_VERIFIED;
        $verification->save();
        return $verification;
    }


    public function verifyEmail($code, $type = null)
    {
        $data = ['code' => $code, 'method' => Verification::METHOD_EMAIL, 'status' => Verification::STATUS_IDLE];
        if ($type) {
            $data['type'] = $type;
        }
        if (!($verification = $this->first($data))) {
            $this->errorMessage = 'Mã xác thực không hợp lệ';
            return false;
        }
        return $this->updateVerified($verification);
    }
    public function verifyPhone($code, $type = null)
    {
        $data = ['code' => $code, 'method' => Verification::METHOD_PHONE, 'status' => Verification::STATUS_IDLE];
        if ($type) {
            $data['type'] = $type;
        }
        if (!($verification = $this->first($data))) {
            $this->errorMessage = 'Mã xác thực không hợp lệ';
            return false;
        }
        return $this->updateVerified($verification);
    }


    public function verify($code)
    {
        $data = ['code' => $code, 'status' => Verification::STATUS_IDLE];

        if (!($verification = $this->first($data))) {
            $this->errorMessage = 'Mã xác thực không hợp lệ';
            return false;
        }
        return $this->updateVerified($verification);
    }

    public function checkPhoneVerified($phone, $type = null, $minute_ago = 0) {
        $data = [
            'method' => Verification::METHOD_PHONE,
            'send_to' => $phone,
            'status' => Verification::STATUS_VERIFIED
        ];
        if($type && in_array($t = strtolower($type), Verification::ALL_TYPE)){
            $data['type'] = $t;
        }
        if($minute_ago){
            $now = Carbon::now()->subMinutes($minute_ago)->toDateTimeString($minute_ago);
            $this->where('expired_at', '>=', $now);
        }
        return $this->count($data) > 0;
    }
    public function checkEmailVerified($email, $type = null, $minute_ago = 0) {
        $data = [
            'method' => Verification::METHOD_EMAIL,
            'send_to' => $email,
            'status' => Verification::STATUS_VERIFIED
        ];
        if($type && in_array($t = strtolower($type), Verification::ALL_TYPE)){
            $data['type'] = $t;
        }
        if($minute_ago){
            $now = Carbon::now()->subMinutes($minute_ago)->toDateTimeString($minute_ago);
            $this->where('expired_at', '>=', $now);
        }
        return $this->count($data) > 0;
    }
}
