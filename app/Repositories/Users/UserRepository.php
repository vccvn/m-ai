<?php

namespace App\Repositories\Users;

use Gomee\Repositories\BaseRepository;

/**
 * validator
 *
 */

use App\Validators\Users\UserValidator;
use App\Masks\Users\UserMask;
use App\Masks\Users\UserCollection;
use App\Models\User;
use App\Repositories\Accounts\AgentRepository;
use App\Repositories\Accounts\WalletRepository;

/**
 * @method UserCollection<UserMask>|User[] filter(Request $request, array $args = []) lấy danh sách User được gán Mask
 * @method UserCollection<UserMask>|User[] getFilter(Request $request, array $args = []) lấy danh sách User được gán Mask
 * @method UserCollection<UserMask>|User[] getResults(Request $request, array $args = []) lấy danh sách User được gán Mask
 * @method UserCollection<UserMask>|User[] getData(array $args = []) lấy danh sách User được gán Mask
 * @method UserCollection<UserMask>|User[] get(array $args = []) lấy danh sách User
 * @method UserCollection<UserMask>|User[] getBy(string $column, mixed $value) lấy danh sách User
 * @method UserMask|User getDetail(array $args = []) lấy User được gán Mask
 * @method UserMask|User detail(array $args = []) lấy User được gán Mask
 * @method UserMask|User find(integer $id) lấy User
 * @method UserMask|User findBy(string $column, mixed $value) lấy User
 * @method UserMask|User first(string $column, mixed $value) lấy User
 * @method User create(array $data = []) Thêm bản ghi
 * @method User update(integer $id, array $data = []) Cập nhật
 */
class UserRepository extends BaseRepository
{
    /**
     * class chứ các phương thức để validate dử liệu
     * @var string $validatorClass
     */
    protected $validatorClass = UserValidator::class;
    /**
     * tên class mặt nạ. Thường có tiền tố [tên thư mục] + \ vá hậu tố Mask
     *
     * @var string
     */
    protected $maskClass = UserMask::class;

    /**
     * tên collection mặt nạ
     *
     * @var string
     */
    protected $maskCollectionClass = UserCollection::class;

    /**
     * @var \App\Models\User
     */
    static $__Model__;


    /**
     * @var WalletRepository
     */
    protected $walletRepository = null;

    /**
     * @var AgentRepository
     */
    protected $agentRepository = null;

    protected $columns = [
        'id' => 'users.id',
        'name' => 'users.name',
        'email' => 'users.email',
        'username' => 'users.username',
        'phone_number' => 'users.phone_number',
        'type' => 'users.type',
        'status' => 'users.status',

    ];


    public function init()
    {
        $this->walletRepository = app(WalletRepository::class);
        $this->agentRepository = app(AgentRepository::class);
        $this->setSearchable([
            'name' => 'users.name',
            'username' => 'users.username',
            'email' => 'users.email',
            'phone_number' => 'users.phone_number',
        ])
            ->searchRule([
                'users.name' => [
                    '{query}%',
                    '% {query}%',
                ],
                'users.email' => [
                    '{query}%'
                ],
                'users.phone_number' => [
                    '{query}%',
                    '%{query}'
                ]
            ]);
    }


    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\User::class;
    }

    public function getUserDetail($args)
    {
        return $this->mode('mask')->detail($args);
    }

    public function updateAgentMonthBalance($user_id, $month = 0) {
        $agent = $this->agentRepository->getAgentOrCreate($user_id);
        if(!$agent) return false;
        $agent->month_balance+= $month;
        $agent->save();
        return $agent;
    }

    /**
     * @override
     * @param array $data Dữ liệu thông tin người dùng
     * @return array Mảng sau khi dược xử lý
     */
    public function beforeSave($data, $id = null)
    {
        if (array_key_exists('password', $data) && $data['password']) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        if (array_key_exists('affiliate_code', $data)) {
            if (!$data['affiliate_code']) {
                unset($data['affiliate_code']);
            } else {
                while (true) {
                    if (!($u = $this->first(['affiliate_code' => $data['affiliate_code']])))
                        break;
                    elseif ($id && $u->id == $id)
                        break;
                    $data['affiliate_code'] = strtoupper(substr(md5(uniqid()), rand(0, 16), 6));
                }
            }
        }
        return $data;
    }

    public function beforeCreate(array $data)
    {
        $affiliate_code = '';
        if (!array_key_exists('affiliate_code', $data) || !$data['affiliate_code'])
            $data['affiliate_code'] = strtoupper(substr(md5(uniqid()), rand(0, 16), 6));
        return $data;
    }

    public function beforeUpdate(array $data, $id = null)
    {
        return $data;
    }

    public function afterSave($result)
    {
        $this->walletRepository->createDefaultWallet($result->id);

    }

    public function deleteAvatar(string $id)
    {
        if ($staff = $this->find($id)) {
            if ($staff->deleteAvatar());
        }
    }


    /**
     * get staffs by info
     * @param string
     */
    public function findUser($info)
    {
        return $this->where(function ($query) use ($info) {
            $query->where('id', $info)
                ->orWhere('username', $info)
                ->orWhere('email', $info);
        })->first();
    }

    /**
     * get staffs by info
     * @param string
     */
    public function findLogin($info)
    {
        return $this->where(function ($query) use ($info) {
            $query->where('phone_number', $info)
                ->orWhere('username', $info)
                ->orWhere('email', $info);
        })->first(['status' => 1]);
    }


    /**
     * thiết lập truy vấn để sử dụng trong trang quản trị
     * @return object instance
     */
    public function fullInfo()
    {
        $this->setJoinable([
            // ['leftJoin', 'profiles', 'profiles.profile_id', '=', 'users.id']
        ])
            ->setSelectable(array_merge($this->columns, ['created_at' => 'users.created_at', 'avatar' => 'users.avatar']));
        // dd($this);
        return $this;
    }

    /**
     * thiet lap thong tin amn an toan
     * @param array $cols
     *
     * @return object
     */
    public function setSelectColumns(...$cols)
    {
        $columns = $this->columns;
        $c = [];
        if (count($cols)) {
            foreach ($columns as $key => $value) {
                if (in_array($key, $cols) || in_array($value, $cols)) {
                    $c[$key] = $value;
                }
            }
        }
        if (count($c)) {
            $this->setSelectable($c);
        }
        return $this;
    }

    /**
     * get user option
     * @param Request $request
     * @param array $args
     * @return array
     */
    public function getSelectOptions(array $args = [], $defaultFirst = null)
    {

        $data = [];
        if ($defaultFirst) $data = [$defaultFirst];
        $this->setSelectColumns('id', 'name', 'email');
        if ($list = $this->get(array_merge(['@limit' => 10], $args))) {
            foreach ($list as $user) {
                $data[$user->id] = htmlentities($user->name . " ($user->email)");
            }
        }
        return $data;
    }
    /**
     * get user option
     * @param Request $request
     * @param array $args
     * @return array
     */
    public function getUserSelectOptions($request, array $args = [])
    {
        $this->setSelectColumns('id', 'name', 'email');
        if ($request->ignore && is_array($request->ignore)) {
            $this->whereNotIn('users.id', $request->ignore);
        }
        $data = [];
        if ($list = $this->getFilter($request, $args)) {
            foreach ($list as $user) {
                $data[$user->id] = htmlentities($user->name . " ($user->email)");
            }
        }
        return $data;
    }
    /**
     * get user option
     * @param Request $request
     * @param array $args
     * @return array
     */
    public function getUserTagData($request, array $args = [])
    {
        $this->setSelectColumns('id', 'name', 'email');
        if ($request->ignore && is_array($request->ignore)) {
            $this->whereNotIn('users.id', $request->ignore);
        }
        $data = [];
        if ($list = $this->getFilter($request, $args)) {
            foreach ($list as $user) {
                $data[] = [
                    'id' => $user->id,
                    'name' => $user->name . ' (' . $user->email . ')',
                ];
            }
        }
        return $data;
    }



    /**
     * lay danh sach user cho crazy rag
     * @param array $args
     * @param array
     */
    public static function getDynamicSelectOptions(array $args = [])
    {
        $data = ['' => 'Chọn một'];
        if ($list = (new static())->staffQuery()->get(array_merge(['@limit' => 10], $args))) {
            foreach ($list as $user) {
                $data[$user->id] = htmlentities($user->name . " ($user->email)");
            }
        }
        return $data;
    }
    /**
     * lay danh sach user cho crazy rag
     * @param array $args
     * @param array
     */
    public static function getStaffSelectOptions(array $args = [])
    {
        $data = [];
        if ($list = (new static())->staffQuery()->get($args)) {
            foreach ($list as $user) {
                $data[$user->id] = htmlentities($user->name . " ($user->email)");
            }
        }
        return $data;
    }

    // public function staffQuery()
    // {
    //     if($this->isSetDefault) return $this;
    //     $this->isSetDefault = true;
    //     return ($id = $this->getOwnerID()) ? $this->enableStaffQuery()->addDefaultCondition('owner', 'where',function($query) use($id){
    //         $query->where('users.id', $id)->orWhere('users.owner_id', $id);
    //     }) : $this;

    // }
    public function removeStaffQuery()
    {
        $this->removeDefaultConditions();
        $this->isSetDefault = false;
        return $this;
    }
    public function ownerInit()
    {
        if (in_array('owner_id', $this->getFields()) && self::$_owner_id) {
            // $this->staffQuery();
            // $this->addDefaultValue('owner_id', self::$_owner_id)
            //       ->addDefaultParam('owner_id', self::$_owner_id);

        }
        return $this;
    }


    /**
     * lấy thông tin chủ web
     * @return \App\Models\User
     */
    // public function getOwner()
    // {
    //     return ($id = $this->getOwnerID()) ? $this->clear()->fullInfo()->getDetail(['id' => $id]) : null;
    // }

    public function getUsernameByEmail($email)
    {
        $e = explode('@', $email);
        $name = str_slug($e[0], '_');
        $i = 0;
        do {
            $na = $name . ($i == 0 ? '' : '_' . $i);
            if (!$this->findBy('username', $na)) return $na;
            $i++;
        } while (true);
    }

    public function getUniqueEmail($email = null)
    {
        $e = explode('@', $email);
        $name = str_slug($e[0], '_');
        $i = 0;
        $c = '@' . get_cfg_domain();
        do {
            $na = $name . ($i == 0 ? '' : '_' . $i) . $c;
            // dd($this->findBy('email', $na));
            if (!$this->findBy('email', $na)) return $na;
            $i++;
        } while (true);
    }

    public function getUniquePhone($start = '0978')
    {
        $i = 0;
        $phone = null;
        do {
            $phone = $this->getRepeatRandomNumber(6, $start);
            // dd($phone, $this->findBy('phone_number', $phone));
            if (!($user = $this->findBy('phone_number', $phone))) return $phone;
            // echo $phone;
            dump($user);
            $i++;
            if ($i > 99)
                break;
        } while ($i < 100);
        dd($phone);
        return $phone;
    }

    public function getRepeatRandomNumber($length = 6, $prefix = '')
    {
        for ($i = 0; $i < $length; $i++) {
            $prefix .= rand(0, 9);
        }
        return $prefix;
    }

    /**
     * lấy user không có voucher
     *
     * @param array<string, string|Arr|int> $args
     * @return User[]
     */
    public function getUserListByCustomerConfig($args = [])
    {
        $campaign_id = $args['campaign_id'] ?? null;
        $limit = $args['total'] ?? ($args['limit'] ?? 0);
        $customer_config = $args['customer_config'] ?? [];
        $this->select('users.id');
        // $this->where('users.id', '!=', function($query) use($campaign_id) {
        //     $query->select('vouchers.user_id')
        //         ->from('vouchers')
        //         ->where('vouchers.campaign_id', $campaign_id);
        // });
        if (is_array($customer_config)) {
            if (array_key_exists('ages', $customer_config) && ($ages = $customer_config['ages'])) {
                $year = (int) date("Y");
                if (array_key_exists('from', $ages) && is_numeric($from = $ages['from']) && $from >= 0)
                    $this->whereYear('users.birthday', '<=', $year - $from);

                if (array_key_exists('to', $ages) && is_numeric($to = $ages['to']) && $to >= 0)
                    $this->whereYear('users.birthday', '>=', $year - $to);
            }
            if (array_key_exists('genders', $customer_config) && is_array($genders = $customer_config['genders']) && ($genders = array_map('strtoupper', $genders))  && count($genders)) {
                $this->whereIn('users.gender', $genders);
            }
            if (array_key_exists('types', $customer_config) && is_array($types = $customer_config['types']) && count($types)) {
                $this->whereIn('users.type', $types);
            }
        }

        return $this->get(['@limit' => $limit, '@orderByRaw' => 'RANDOM()']);
    }
    /**
     * lấy user không có voucher
     *
     * @param array<string, string|Arr|int> $args
     * @return User[]
     */
    public function getUserListWithoutCampaignVoucher($args = [])
    {
        $campaign_id = $args['campaign_id'] ?? null;
        // $this->whereRaw("users.id not in (select vouchers.user_id from vouchers where vouchers.campaign_id = '$campaign_id')");
        $this->whereNotIn('users.id', function ($query) use ($campaign_id) {

            $query->select('vouchers.user_id')
                ->from('vouchers')
                ->where('vouchers.campaign_id', $campaign_id);
        });
        return $this->getUserListByCustomerConfig($args);
    }
}
