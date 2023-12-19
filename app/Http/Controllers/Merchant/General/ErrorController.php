<?php

namespace App\Http\Controllers\Merchant\General;

use App\Http\Controllers\Merchant\MerchantController;
use Illuminate\Http\Request;

class ErrorController extends MerchantController
{
    protected $module = 'errors';

    protected $moduleName = 'Errors';
    
    

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->repositoy = null;
    }

    public function handleError(Request $request, $uri = null)
    {
        return redirect()->route('merchant.dashboard')->with('error', 'Trang bạn vừa truy cập hiện không tồn tại!');
    }
}
