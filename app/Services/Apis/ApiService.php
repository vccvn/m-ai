<?php

namespace App\Services\Apis;

use Gomee\Services\Traits\ApiMethods;
use Gomee\Services\Traits\Events;
use Gomee\Services\Traits\FileMethods;
use Gomee\Services\Traits\ModuleMethods;
use Illuminate\Foundation\Bus\DispatchesJobs;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ApiService
{
    use
        // module quan li lien quan toi modulw se dc ke thua tu Service nay
        ModuleMethods,
        // tap hop cac thuoc tinh va ham lien quan den xu ly su kien
        ApiMethods,
        // tap hop cac thuoc tinh va ham lien quan den xu ly su file
        FileMethods,
        Events;


    /**
     * Create a new Service instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * thuc thi mot so thiet lap
     * @return void
     */
    public function init()
    {
        $this->moduleInit();
        $this->crudInit();
        $this->fileInit();

    }

    public function response($data = null, $status = true, $message = '', $errors = [], $http = 200)
    {

        return $this->json(compact(...$this->apiSystemVars), $http);
    }

}
