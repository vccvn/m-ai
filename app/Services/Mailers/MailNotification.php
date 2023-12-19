<?php

namespace App\Services\Mailers;

use Gomee\Mailer\Email;
use Illuminate\Support\Facades\Config;

class MailNotification extends Mailer
{

    public function __construct()
    {
        static::updateConfig();
        $this->updateConfigData();
    }

    protected function updateConfigData(){
        $setting = setting();
        $siteinfo = siteinfo();
        if ($setting->send_mail_notification && $setting->mail_notification) {
            $to = [];
            if (count($lines = nl2array($setting->mail_notification))) {
                foreach ($lines as $line) {
                    $emails = array_filter(array_map('trim', explode(',', $setting->mail_notification)), function ($email) {
                        return strlen($email) > 0 && filter_var($email, FILTER_VALIDATE_EMAIL);
                    });
                    foreach ($emails as $email) {
                        $to[] = $email;
                    }
                }
            }

            if ($to) {
                $this->to($to);
                $this->__canSend__ = true;
            } else {
                $this->__canSend__ = false;
            }
        }
    }

	protected function _body($body=null)
	{
		$this->__body = 'mails.notifications.' . $body;
		return $this;
	}

    public function beforeSend()
    {
        if(!$this->__body) $this->body('simple');
        if(!$this->__subject) $this->__subject = __('Notification');
        if(!$this->addressData['to'])
            $this->updateConfigData();
        if(!$this->addressData['to'])
            return false;
        return true;

    }
}
