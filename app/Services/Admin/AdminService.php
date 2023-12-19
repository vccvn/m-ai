<?php

namespace App\Services\Admin;

use App\Services\Service;


class AdminService extends Service
{
    /**
     * @var string $routeNamePrefix
     */
    protected $routeNamePrefix = 'admin.';

    /**
     * @var string $viewFolder thu muc chua view
     * khong nen thay doi lam gi
     */
    protected $viewFolder = 'admin';
    /**
     * @var string dường dãn thư mục chứa form
     */
    protected $formDir = 'admin/forms';

    /**
     * @var string $menuName
     */
    protected $menuName = 'admin_menu';


    protected $scope = 'admin';



}
