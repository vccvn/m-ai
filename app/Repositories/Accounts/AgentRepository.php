<?php

namespace App\Repositories\Accounts;

use Gomee\Repositories\BaseRepository;
use App\Masks\Accounts\AgentMask;
use App\Masks\Accounts\AgentCollection;
use App\Models\AgentAccount;
use App\Repositories\Policies\CommissionRepository;
use App\Validators\Accounts\AgentValidator;
use Illuminate\Http\Request;

/**
 * @method AgentCollection<AgentMask>|AgentAccount[] filter(Request $request, array $args = []) lấy danh sách AgentAccount được gán Mask
 * @method AgentCollection<AgentMask>|AgentAccount[] getFilter(Request $request, array $args = []) lấy danh sách AgentAccount được gán Mask
 * @method AgentCollection<AgentMask>|AgentAccount[] getResults(Request $request, array $args = []) lấy danh sách AgentAccount được gán Mask
 * @method AgentCollection<AgentMask>|AgentAccount[] getData(array $args = []) lấy danh sách AgentAccount được gán Mask
 * @method AgentCollection<AgentMask>|AgentAccount[] get(array $args = []) lấy danh sách AgentAccount
 * @method AgentCollection<AgentMask>|AgentAccount[] getBy(string $column, mixed $value) lấy danh sách AgentAccount
 * @method AgentMask|AgentAccount getDetail(array $args = []) lấy AgentAccount được gán Mask
 * @method AgentMask|AgentAccount detail(array $args = []) lấy AgentAccount được gán Mask
 * @method AgentMask|AgentAccount find(integer $id) lấy AgentAccount
 * @method AgentMask|AgentAccount findBy(string $column, mixed $value) lấy AgentAccount
 * @method AgentMask|AgentAccount first(string $column, mixed $value) lấy AgentAccount
 * @method AgentAccount create(array $data = []) Thêm bản ghi
 * @method AgentAccount update(integer $id, array $data = []) Cập nhật
 * @property-read CommissionRepository $commissionRepository
 */
class AgentRepository extends BaseRepository
{
    /**
     * class chứ các phương thức để validate dử liệu
     * @var string $validatorClass
     */
    protected $validatorClass = AgentValidator::class;
    /**
     * tên class mặt nạ. Thường có tiền tố [tên thư mục] + \ vá hậu tố Mask
     *
     * @var string
     */
    protected $maskClass = AgentMask::class;

    /**
     * tên collection mặt nạ
     *
     * @var string
     */
    protected $maskCollectionClass = AgentCollection::class;

    /**
     * CommissionRepository
     *
     * @var CommissionRepository
     */
    protected $commissionRepository;


    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\AgentAccount::class;
    }

    public function init()
    {
        $this->commissionRepository = app(CommissionRepository::class);
    }

    public function createDefaultAgent($user_id, $policy_id = null)
    {
        if (!($userPolicy = $this->first($p = ['user_id' => $user_id]))) {
            if ($policy_id && $this->commissionRepository->detail($policy_id)) {
            } elseif ($policy = $this->commissionRepository->orderBy('level', 'ASC')->first()) {
                $policy_id = $policy->id;
            } else {
                $policy_id = 0;
            }
            $p['policy_id'] = $policy_id;
            $userPolicy = $this->create($p);
        }
        return $userPolicy;
    }

    public function updateAgent($user_id, $policy_id = 0)
    {
        if (!($userPolicy = $this->with('policy')->first($p = ['user_id' => $user_id]))) {
            return $this->createDefaultAgent($user_id, $policy_id);
        }
        if ($policy_id != $userPolicy->policy_id) {
            $data = [
                'commission_level_1' => 0,
                'commission_level_2' => 0,
                'commission_level_3' => 0,
                'commission_level_4' => 0
            ];
            $userPolicy = $this->update($userPolicy->id, $data);
        }
    }
    public function upgrade($user_id, $policy_id = 0)
    {
        if (
            !($userPolicy = $this->first($p = ['user_id' => $user_id])) ||
            ($policy_id == $userPolicy->policy_id) ||
            !($policy = $this->commissionRepository->find($policy_id)) ||
            !$userPolicy->policy ||
            $policy->level < $userPolicy->policy->level
            ) {
            return false;
        }
        $data = [
            'policy_id' => $policy_id,
            'revenue' => 0,
            'commission_level_1' => 0,
            'commission_level_2' => 0,
            'commission_level_3' => 0,
            'commission_level_4' => 0
        ];
        return $this->update($userPolicy->id, $data);
    }

    /**
     * get agent
     *
     * @param int $user_id
     * @return AgentAccount
     */
    public function getAgentOrCreate($user_id){
        if (!($agent = $this->with('policy')->first($p = ['user_id' => $user_id]))) {
            $this->createDefaultAgent($user_id);
            $agent = $this->with('policy')->first($p = ['user_id' => $user_id]);
        }
        return $agent;
    }

    function checkAgentMonthBalance($user_id, $month = 0) {
        return ($agent = $this->first(['user_id' => $user_id]))? $agent->month_balance >= $month: false;
    }


    public function updateMonthBalance($user_id, $month = 0) {
        $agent = $this->getAgentOrCreate($user_id);
        if(!$agent) return false;
        $agent->month_balance+= $month;
        $agent->save();
        return $agent;
    }
}
