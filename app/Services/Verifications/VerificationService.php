<?php

namespace App\Services\Verifications;

use App\Repositories\Users\UserRepository;
use App\Repositories\Verifications\VerificationRepository;
use App\Services\ESMS\ESMSService;
use App\Services\Mailers\Mailer;
use App\Services\Service;
use Carbon\Carbon;

class VerificationService extends Service
{
    protected $module = 'verifications';

    protected $moduleName = 'Verification';

    protected $flashMode = true;


    /**
     * VerificationRepository
     *
     * @var VerificationRepository
     */
    public $repository = null;

    /**
     * Campaign
     *
     * @var UserRepository
     */
    public $userRepository = null;


    /**
     * ESMSService
     *
     * @var ESMSService
     */
    protected $eSMSService = null;

    protected $errorMessage = null;

    /**
     * Create a new Service instance.
     *
     * @return void
     */
    public function __construct(VerificationRepository $verificationRepository, UserRepository $userRepository, ESMSService $eSMSService)
    {
        $this->repository = $verificationRepository;
        $this->userRepository = $userRepository;
        $this->eSMSService = $eSMSService;
        $this->init();
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    public function createPhoneRegisterVerification($phone)
    {
        if (!($verification = $this->repository->createPhoneVerification($phone, 'register'))) {
            $this->errorMessage = $this->repository->getErrorMessage();
            return false;
        }
        if (!($send = $this->eSMSService->sendVerifyCode($verification->send_to, $verification->code))) {
            $this->errorMessage = 'Không thể gửi tin nhắn xác thực';
        }

        return $verification;
    }
    public function createEmailRegisterVerification($email)
    {
        if (!($verification = $this->repository->createEmailVerification($email, 'register'))) {
            $this->errorMessage = $this->repository->getErrorMessage();
            return false;
        }
        $mailer = get_mailer_setting();
        try {
            Mailer::from($mailer->mail_from_address(siteinfo('email', 'no-reply@' . get_non_www_domain())), $mailer->mail_from_name(siteinfo('site_name', 'Trekka')))
                ->to($email)
                ->subject("Xác thực email trên hệ thống " . siteinfo('site_name'))
                ->body('mails.verify-email')
                ->data([
                    'code' => $verification->code,
                    'email' => $email,
                ])
                ->sendAfter(1);
            return $verification;
        } catch (\Throwable $th) {
            $this->errorMessage = 'Không thể gửi mail';
        }

        return false;
    }

    public function verifyPhoneRegister($code)
    {
        if (!($verification = $this->repository->verifyPhone($code, 'register'))) {
            $this->errorMessage = $this->repository->getErrorMessage();
            return false;
        }
        return $verification;
    }
    public function verifyEmailRegister($code)
    {
        if (!($verification = $this->repository->verifyEmail($code, 'register'))) {
            $this->errorMessage = $this->repository->getErrorMessage();
            return false;
        }
        return $verification;
    }
}
