<?php

namespace App\Services\Mailers;

use Gomee\Mailer\Email;
use Illuminate\Support\Facades\Config;

use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class Mailer extends Email
{
    protected static $__config = [];
    public static function updateConfig()
    {
        if (!static::$__config) {
            if ($setting = get_mailer_setting()) {
                $config = [
                    'default' => $setting->mail_driver(config('mail.default')),
                    'mailers' => [
                        'smtp' => [
                            'transport' => 'smtp',
                            'host' => $setting->mail_host(config('mail.mailers.smtp.host')),
                            'port' => $setting->mail_port(config('mail.mailers.smtp.port')),
                            'encryption' => $setting->mail_encryption(config('mail.mailers.smtp.encryption')),
                            'username' => $setting->mail_username(config('mail.mailers.smtp.username')),
                            'password' => $setting->mail_password(config('mail.mailers.smtp.password')),
                            'timeout' => null,
                        ],

                        'ses' => [
                            'transport' => 'ses',
                        ],

                        'mailgun' => [
                            'transport' => 'mailgun',
                        ],

                        'postmark' => [
                            'transport' => 'postmark',
                        ],

                        'sendmail' => [
                            'transport' => 'sendmail',
                            'path' => config('mail.mailers.sendmail.path',  '/usr/sbin/sendmail -bs -i'),
                        ],

                        'log' => [
                            'transport' => 'log',
                            'channel' => config('mail.mailers.log.channel'),
                        ],

                        'array' => [
                            'transport' => 'array',
                        ],

                        'failover' => [
                            'transport' => 'failover',
                            'mailers' => [
                                'smtp',
                                'log',
                            ],
                        ],
                    ],


                    'from' => [
                        'address' => $setting->mail_from_address(config('mail.from.address')),
                        'name' => $setting->mail_from_name(config('mail.from.address')),
                    ],

                    'markdown' => [
                        'theme' => 'default',

                        'paths' => config('mail.markdown.paths')
                    ],
                ];
                static::$__config = $config;
                Config::set('mail', $config);
            }
        }
    }
    /**
     * khoi tao
     */
    public function __construct()
    {
        if (!static::$__config) {

            static::updateConfig();
        }
    }


    /**
     * gửi mail
     *
     * @param string $body
     * @param array $var
     * @return void
     */
    public function _sendMail($body = null, $vars = [])
    {
        if (!$this->__canSend__) return false;
        static::updateConfig();
        if (method_exists($this, 'beforeSend')) {
            $s = $this->beforeSend();
            if($s === false){
                return false;
            }
        }
        if (static::$__oneTimeData) {
            $vars = array_merge(static::$__oneTimeData, $vars);
            static::$__oneTimeData = [];
        }
        if (!$body) $body = $this->__body;
        Mail::send($body, $vars, function ($message) {
            $data = $this->addressData;
            foreach ($data as $key => $value) {
                $this->callMessageMethod($message, $key, $value);
            }
            $message->subject($this->__subject);
            if ($this->__attachments) {
                $files = $this->__attachments;
                foreach ($files as $file) {
                    $message->attach($file);
                }
            }
        });
    }
}
