<?php

namespace App\Console\Commands;

use App\Services\Permissions\PermissionService;
use Illuminate\Console\Command;

class UpdatePermissionMatrix extends Command
{
    /**
     * permission service
     *
     * @var PermissionService
     */
    public $permission;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:matrix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'import module from routes';



    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(PermissionService $permissionService)
    {
        if($permissionService->updateModuleRouteMatrix()){
            $this->output->info('Cap nhat ma tran phan quyen thanh cong');
        }else{
            $this->output->error('loi không xac dinh');
        }
        return 0;
    }
}
