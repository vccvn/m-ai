<?php

namespace App\Repositories\Emails;

use Gomee\Repositories\BaseRepository;

use Gomee\Helpers\Any;

use Carbon\Carbon;

class EmailTokenRepository extends BaseRepository
{
    protected $supportTypes = [
        'confirm', 'reset-password', 'verify', 'verify-email', 'verify-phone',
    ];

    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\EmailToken::class;
    }

    /**
     * tạo token
     * @param string $email
     * @param string $type
     * @param string $ref
     * @param int $ref_id
     * @return \App\Models\EmailToken|false
     *
     */
    public function createToken(string $email, string $type = 'confirm', $ref = null, $ref_id = 0, $expired_minutes = 5)
    {
        if(filter_var($email, FILTER_VALIDATE_EMAIL) && in_array($type = strtolower($type), $this->supportTypes)){
            $this->deleteOldToken($email, $type, $ref, $ref_id);
            // $dt = Carbon::now('Asia/Ho_Chi_Minh');

            $data = compact('email', 'type');
            $data['token'] = uniqid();
            $data['code'] = $this->createCode($email);
            if(is_string($ref) && $ref){
                $data['ref'] = $ref;
                if($ref_id){
                    $data['ref_id'] = $ref_id;
                }
            }
            $newDateTime = Carbon::now()->addMinutes($expired_minutes > 0? $expired_minutes : 5)->toDateTimeString('millisecond');
            // $data['expired_at'] = $dt->addDays(1);
            $data['expired_at'] = $newDateTime;
            return $this->create($data);
        }
        return false;
    }


    /**
     * coa token cũ
     * @param string $email
     * @param string $type
     * @param string $ref
     * @param int $ref_id
     * @return \App\Models\EmailToken|false
     *
     */
    public function deleteOldToken(string $email, $type = null, $ref = null, $ref_id = 0)
    {
        $this->email = $email;
        if($type) $this->type = $type;
        if(is_string($ref) && $ref){
            $this->ref = $ref;
            if(is_numeric($ref_id) && $ref_id > 0) $this->ref_id = $ref_id;
        }
        if($list = $this->get()){
            foreach ($list as $email) {
                $email->delete();
            }
        }
    }

    /**
     * tạo code
     * @return integer $code
     */
    public function createCode($email = null)
    {
        $args = $email?['email' => $email]: [];
        do{
            $code = rand(100000, 999999);
            $now = (Carbon::now())->getTimestamp();
            $args['code'] = $code;
            if(!( $mail = $this->first($args))){
                return $code;
            }elseif(strtotime($mail->expired_at) < $now){
                $mail->delete();
                return $code;
            }
        }while(true);
    }

    /**
     * kiểm tra token
     * @param string $token
     * @param string $type
     * @param string $ref
     * @return \App\Models\EmailToken|false
     *
     */
    public function checkRoken(string $token, $type = null, $ref = null)
    {
        $args = ['token' => $token];
        if($type) $args['type'] = $type;
        if(is_string($ref) && $ref){
            $args['ref'] = $ref;
        }
        if($email = $this->first($args)){
            if(strtotime($email->expired_at) > time())
                return $email;
            else
                $email->delete();
        }
        // $this->where('expired_at', '<', Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString());
        return false;
    }


    /**
     * kiểm tra token
     * @param string $code
     * @param string $type
     * @param string $ref
     * @return \App\Models\EmailToken|false
     *
     */
    public function checkOTP(string $code, $type = null, $ref = null, $ref_id = '')
    {
        $args = ['code' => $code];
        if($type) $args['type'] = $type;
        if(is_string($ref) && $ref){
            $args['ref'] = $ref;
            if($ref_id){
                $args['ref_id'] = $ref_id;
            }
        }
        if($email = $this->where('expired_at', '>', date('Y-m-d H:i:s'))->first($args)){
            return $email;
        }elseif($m = $this->first($args)){
            $m->delete();
        }

        return false;
    }
}
