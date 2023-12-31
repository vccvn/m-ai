<?php

namespace App\Repositories\Notices;

use App\Masks\Notices\NoticeCollection;
use App\Masks\Notices\NoticeMask;
use Gomee\Repositories\BaseRepository;

use App\Repositories\Metadatas\MetadataRepository;
use App\Repositories\Users\UserRepository;


class NoticeRepository extends BaseRepository
{


    /**
     * class chứ các phương thức để validate dử liệu
     * @var string $validatorClass
     */
    protected $validatorClass = 'App\Validators\Notices\NoticeValidator';

    /**
     * tên class mặt nạ. Thược có tiền tố [tên thư mục] + \ vá hậu tố Mask
     *
     * @var string
     */
    protected $maskClass = NoticeMask::class;

    /**
     * tên collection mặt nạ
     *
     * @var string
     */
    protected $maskCollectionClass = NoticeCollection::class;

    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\Notice::class;
    }


    /**
     * lấy số thông báo mới của user
     * @param int $user_id
     */
    public function getBadge($user_id)
    {
        if(!($user = app(UserRepository::class)->find($user_id))) return 0;
        $uid = $user->id;
        $user_type = $user->type;

        $this->where(function($query) use($uid){
            $query->where('notices.type', 'personal')
                ->where('notices.to_id', $uid)
                ->where('notices.seen', 0);
        })->orWhere(function($q) use($uid, $user_type){
            // tat ca hoac nhom
            $q->where('notices.type', 'all')
            ->orWhere(function($r) use($uid, $user_type){
                $r->where('notices.type', 'group')
                    ->where('notices.to_group', $user_type);
            });

        })->selectRaw("COUNT(1) - (select count(1) from notices n INNER JOIN user_notices u on n.id = u.notice_id WHERE (n.type='all' or (n.type='group' and to_group = '$user_type')) AND u.user_id = '$uid') as notice_count");

        $total = $this->first()->notice_count;


        return $total;
    }

    /**
     * first seen
     * @param User $user
     */
    protected function firstSeen($user_id, $user_type)
    {
        $userNotices = new UserNoticeRepository();

        $this->where('notices.type', 'personal')
                ->where('notices.to_id', $user_id)
                ->where('notices.seen', 0);
        if(count($list = $this->get())){
            foreach ($list as $notice) {
                $this->update($notice->id,['seen'=>1]);
            }
        }
        $this->where(function($q) use($user_id, $user_type){
            // tat ca hoac nhom
            $q->where('notices.type', 'all')
            ->orWhere(function($r) use($user_type){
                $r->where('notices.type', 'group')
                    ->where('notices.to_group', $user_type);
            });

        })->whereRaw("notices.id NOT IN (select n.id from notices n INNER JOIN user_notices u on n.id = u.notice_id WHERE (n.type='all' or (n.type='group' and to_group = '$user_type')) AND u.user_id = $user_id)");
        if(count($list = $this->get())){
            foreach ($list as $notice) {
                $userNotices->addUserNotice($user_id, $notice->id);
            }
        }

    }

    /**
     * chuẩn bị để lấy thông báo
     * @param Request $request
     */
    public function prepareGetUserNotices($request)
    {
        $user = $request->user();
        $uid = $user->id;
        $user_type = $user->type;
        $this->mode('mask');
        $this->firstSeen($uid, $user_type);
        $this->where(function($query) use($uid){
            $query->where('notices.type', 'personal')
                ->where('notices.to_id', $uid);
        })->orWhere(function($q) use($uid, $user_type){
            // tat ca hoac nhom
            $q->where('notices.type', 'all')
            ->orWhere(function($r) use($uid, $user_type){
                $r->where('notices.type', 'group')
                    ->where('notices.to_group', $user_type);
            });

        });

    }

    /**
     * gui thong bao cho user
     *
     * @param array $mails
     * @param array $data
     * @return mixed
     */
    public function sendNoticeToUserByMails($mails, $data)
    {
        $users = app(UserRepository::class)->select('id')->get(['email' => $mails]);
        if(count($users)){
            $a = array_merge($data, ['type' => 'personal']);
            foreach ($users as $key => $user) {
                $a['to_id'] = $user->id;
                $this->create($a);
            }
            return true;
        }
        return false;
    }
}
