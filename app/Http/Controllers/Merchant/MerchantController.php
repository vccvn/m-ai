<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use Gomee\Files\Filemanager;
use Gomee\Html\Menu;

class MerchantController extends Controller
{
    /**
     * @var string $routeNamePrefix
     */
    protected $routeNamePrefix = 'merchant.';

    /**
     * @var string $viewFolder thu muc chua view
     * khong nen thay doi lam gi
     */
    protected $viewFolder = 'merchant';
    /**
     * @var string dường dãn thư mục chứa form
     */
    protected $formDir = 'merchant/forms';

    /**
     * @var string $menuName
     */
    protected $menuName = 'merchant_menu';
    

    protected $scope = 'merchant';



}
