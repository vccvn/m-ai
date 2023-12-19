<?php

namespace App\Http\Controllers\Admin\General;

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Http\Request;

class ErrorController extends AdminController
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
        return redirect()->route('admin.dashboard')->with('error', 'Trang bạn vừa truy cập hiện không tồn tại!');
    }
}
