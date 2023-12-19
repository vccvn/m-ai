<?php

namespace App\Http\Helpers;


use Illuminate\Support\Facades\Auth;

class Common
{



    /**
     * Return role wether admin and Depatment ID via API token
     * @return array
     */
    public static function getRoleAndDeptIDViaApiToken()
    {
        $user = Auth::guard('api')->user();
        $isNotAdmin = !($user->hasRole('admin'));
        $thisDept = $user->department_id;

        return [$isNotAdmin, $thisDept];
    }
}
