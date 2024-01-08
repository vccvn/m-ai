<?php

namespace App\Http\Controllers\Admin\General;

use App\Http\Controllers\Admin\AdminController;
use App\Models\CoverLetter;
use App\Models\User;
use App\Repositories\Accounts\AgentRepository;
use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\Accounts\CoverLetterRepository;
use App\Repositories\Users\UserRepository;
use App\Services\Mailers\Mailer;

class CoverLetterController extends AdminController
{
    protected $module = 'users.cover-letters';

    protected $moduleName = 'Đơn xét duyệt';

    protected $flashMode = true;

    /**
     * repository chinh
     *
     * @var CoverLetterRepository
     */
    public $repository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(CoverLetterRepository $repository, protected UserRepository $userRepository, protected AgentRepository $agentRepository)
    {
        $this->repository = $repository;
        $this->init();
    }
    public function beforeGetListView(Request $request, $data)
    {
        add_js_data('COVER_LETTER', [
            'urls' => [
                'changeStatus' => route('admin.users.cover-letters.change-status')

            ]
        ]);
    }

    public function changeStatus(Request $request)
    {
        extract($this->apiDefaultData);
        if (!$request->id || !($coverLetter = $this->repository->with('user')->find($request->id)) || !($user = $coverLetter->user)) {
            $message = 'Người dùng không tồn tãi';
        } elseif (!in_array($request->status, CoverLetter::STATUS_LIST)) {
            $message = 'Trạng thái không hợp lệ';
        } elseif ($request->status == $coverLetter->status) {
            $message = 'Trạng thái y như cũ';
        } elseif (!($coverLetterUpdate = $this->repository->update($coverLetter->id, ['status' => $request->status]))) {
            $message = 'Không thể update coverLetter';
        } else {

            if($request->status == 1){
                $this->userRepository->update($user->id, ['type' => User::AGENT]);
                $this->agentRepository->createDefaultAgent($user->id);
            }elseif($coverLetter->status == 1){
                $this->userRepository->update($user->id, ['type' => User::USER]);
            }
            $status = true;
            $data = $coverLetterUpdate;
            $mailConfig = [
                '-10' => [
                    'subject' => 'Thông báo tình trạng đơn đăng ký làm đại lý',
                    'content' => 'Rất buồn khi phải thông báo với bạn rằng chúng tôi từ chối đơn set duyệt của bạn'
                ],
                '-11' => [
                    'subject' => 'Thông báo tình trạng đơn đăng ký làm đại lý',
                    'content' => 'Chúng tôi tạm thời huỷ bỏ tư cách là đại lý phân phối của bạn'
                ],
                '01' => [
                    'subject' => 'Thông báo tình trạng đơn đăng ký làm đại lý',
                    'content' => 'Chúng tôi tạo hoãn tư cách đại lý của bạn để xem xét lại'
                ],
                '0-1' => [
                    'subject' => 'Thông báo tình trạng đơn đăng ký làm đại lý',
                    'content' => 'Đơn đăng ký của bạn đang được xem xét'
                ],
                '1-1' => [
                    'subject' => 'Thông báo khôi phục tư cách là đại lý',
                    'content' => 'Chúc mừng bạn đă được khôi phục tư cách là đại lý hoa hồng trên hệ thống của ' . siteinfo('site_name')
                ],
                '1-1' => [
                    'subject' => 'Chúc mừng bạn trở thành dại lý hoa hồng',
                    'content' => 'Chúc mừng bạn đă được duyệt đơn đăng ký trở thành đại lý hoa hồng trên hệ thống của ' . siteinfo('site_name')
                ]
            ];
            $key = $request->status.$coverLetter->status;
            if (array_key_exists($key, $mailConfig)) {
                $mail = $mailConfig[$key];
                if ($emailSetting = get_mailer_setting()) {
                    $from = $emailSetting->mail_from_address(siteinfo('email'));
                    $name = $emailSetting->mail_from_name(siteinfo('site_name'));
                } else {
                    $from = siteinfo('email', 'no-reply@' . get_non_www_domain());
                    $name = siteinfo('site_name', 'Gomee');
                }

                Mailer::from($from, $name)->to($user->email, $user->name)
                    ->subject($mail['subject'])
                    ->body('mails.simple-alert')
                    ->data([
                        'name' => $user->name,
                        'content' => $mail['content']
                    ])
                    ->sendAfter(1);
            }
        }
        return $this->json(compact(...$this->apiSystemVars));
    }
}
