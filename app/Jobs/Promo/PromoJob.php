<?php
/**
 * @Author phuongnam
 * @Date   May 30, 2022
 */

namespace App\Jobs\Promo;

use App\Jobs\Job;
use App\Models\ProductRef;
use App\Models\Promo;
use App\Models\UserDiscount;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class PromoJob extends Job
{
    public function handle()
    {
        try {
            $date = Carbon::now();

            $promoSchedules = Promo::query()->where('schedule', '=', 'on')
                                   ->where('finished_at', '<', $date->format('Y-m-d H:i:s'))
                                   ->get();
            $promoId        = [];
            foreach ($promoSchedules as $promoSchedule) {
                if ($promoSchedule->type_schedule == 'week') {
                    $finishedAt = $date->addWeek($promoSchedule->value_schedule);
                } elseif ($promoSchedule->type_schedule == 'month') {
                    $finishedAt = $date->addMonth($promoSchedule->value_schedule);
                } elseif ($promoSchedule->type_schedule == 'year') {
                    $finishedAt = $date->addYear($promoSchedule->value_schedule);
                } else {
                    $finishedAt = $date->addDay($promoSchedule->value_schedule);
                }
                $promoId[]                 = $promoSchedule->id;
                $promos                    = new Promo();
                $promos->name              = $promoSchedule->name;
                $promos->description       = $promoSchedule->description;
                $promos->scope             = $promoSchedule->scope;
                $promos->type              = $promoSchedule->type;
                $promos->down_price        = $promoSchedule->down_price;
                $promos->limited_total     = $promoSchedule->limited_total;
                $promos->usage_total       = $promoSchedule->usage_total;
                $promos->quantity_per_user = $promoSchedule->quantity_per_user;
                $promos->code              = $promoSchedule->code;
                $promos->started_at        = date('Y-m-d H:i:s');
                $promos->finished_at       = $finishedAt;
                $promos->trashed_status    = $promoSchedule->trashed_status;
                $promos->schedule          = $promoSchedule->schedule;
                $promos->type_schedule     = $promoSchedule->type_schedule;
                $promos->value_schedule    = $promoSchedule->value_schedule;
                $promos->created_at        = date('Y-m-d H:i:s');
                $promos->updated_at        = date('Y-m-d H:i:s');

                $promos->save();

                $productRefOlds = ProductRef::query()->where('ref_id', '=', $promoSchedule->id)
                                            ->where('ref', '=', 'promo')
                                            ->get();

                $productRefs = [];
                foreach ($productRefOlds as $productRef) {
                    $productRefs[] = [
                        'product_id' => $productRef->product_id,
                        'ref'        => 'promo',
                        'ref_id'     => $promos->id,
                    ];
                }
                ProductRef::query()->insert($productRefs);

                $userDiscountOld = UserDiscount::query()->where('discount_id', '=', $promoSchedule->id)->get();

                $userDiscounts = [];
                foreach ($userDiscountOld as $userDiscount) {
                    $userDiscounts[] = [
                        'discount_id' => $promos->id,
                        'user_id'     => $userDiscount->user_id,
                        'total'       => $userDiscount->total,
                        'is_linited'  => $userDiscount->is_linited,
                        'usage'       => $userDiscount->usage,
                        'created_at'  => $date->format('Y-m-d H:i:s'),
                        'updated_at'  => $date->format('Y-m-d H:i:s')
                    ];
                }

                UserDiscount::query()->insert($userDiscounts);
            }
            Promo::query()->whereIn('id', $promoId)->update([
                                                                'schedule' => null
                                                            ]);

        } catch (Exception $e) {
            Log::info('[PromoJob] error : ' . $e->getMessage());
        }
    }

}
