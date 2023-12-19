<?php

namespace App\Repositories\Subscribes;

use App\Validators\Subscribes\SubscribeValidator;
use Gomee\Repositories\BaseRepository;

class SubscribeRepository extends BaseRepository
{
    /**
     * class chứ các phương thức để validate dử liệu
     * @var string $validatorClass 
     */
    protected $validatorClass = SubscribeValidator::class;
    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\Subscribe::class;
    }

}