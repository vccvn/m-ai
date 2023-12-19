<?php

namespace App\Repositories\Notices;

use Gomee\Repositories\BaseRepository;

class UserNoticeRepository extends BaseRepository
{


    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\UserNotice::class;
    }

    /**
     * theme user notice
     * @param int $user_id
     * @param int $notice_id
     * @return mixed
     */
    public function addUserNotice($user_id, $notice_id)
    {
        if($this->first($data = compact('user_id', 'notice_id'))) return false;
        return $this->save($data);
    }

}
