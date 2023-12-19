<?php

namespace App\Services\System;

use App\Repositories\Notices\NoticeRepository;
use App\Services\Service;

use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

class NoticeService extends Service
{
    protected $module = 'notices';

    protected $moduleName = 'Notice';

    protected $flashMode = true;
    
    /**
     * @var NoticeRepository
     */
    public $repository = null;

    /**
     * instance of service
     *
     * @var NoticeService
     */
    protected static $instance = null;
    
    public function __construct(NoticeRepository $repository)
    {
        $this->repository = $repository;
    }


    /**
     * get instance of service
     *
     * @return NoticeService
     */
    public static function getInstance()
    {
        if(self::$instance) return self::$instance;
        self::$instance = app(static::class);
        return self::$instance;
    }



    /**
     * gui thong bao den user
     *
     * @param array $mails
     * @param array $data
     * @return mixed
     */
    public static function sendNoticeByMails($mails, $data)
    {
        return static::getInstance()->repository->sendNoticeToUserByMails($mails, $data);
    }
}
