<?php

namespace App\Repositories\Permissions;

use App\Models\PermissionModule;
use Gomee\Repositories\BaseRepository;

class ModuleRepository extends BaseRepository
{


    /**
     * class chứ các phương thức để validate dử liệu
     * @var string $validatorClass 
     */
    protected $validatorClass = 'App\Validators\Permissions\ModuleValidator';

    /**
     * @var string $resource
     */
    protected $resourceClass = 'App\Http\Resources\ModuleResource';

    /**
     * @var string $collectionClass
     */
    protected $collectionClass = 'App\Http\Resources\ModuleCollection';

    /**
     * @var string $system
     */
    protected $system = 'both';

    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\PermissionModule::class;
    }
    

    function moduleQuery($query, $level = 0) {
        $query->with([
            'moduleRoles' => function($query){},
            'modules' => function ($query) use($level) {
                if($level<10){
                    $this->moduleQuery($query, $level+1);
                }
            }
        ]);
    }

    public function getModuleMatrix()
    {
        // 4 level
        return $this->where('type', PermissionModule::TYPE_SCOPE)->whereNull('parent_uuid')->with([
            'moduleRoles' => function($query){},
            // module
            'modules' => function ($query) {
                $this->moduleQuery($query, 1);
            },
        ])->get();
    }


    /**
     * lấy các module tương với route
     * @param array $route
     */

    public function getModuleByRouteInfo(array $route = [])
    {
        $modules = [];
        $this->where(function ($query) use ($route) {
            $query->where('type', 'uri')->where('ref', $route['uri']);
        });
        if ($route['name']) {
            $this->orWhere(function ($query) use ($route) {
                $query->where('type', 'name')->where('ref', $route['name']);
            });
        }
        if ($route['prefix']) {
            $this->orWhere(function ($query) use ($route) {
                $query->where('type', 'prefix')->where('ref', $route['prefix']);
            });
        }
        if (count($list = $this->get())) {
            $modules = $list;
        }
        return $modules;
    }

    /**
     * lay option parent
     * @param int $ignore
     * 
     * @return array
     */
    public static function getParentOptions($ignore = null)
    {
        $data = ["" => 'Module cha'];
        $rep = new static();
        if ($ignore) {
            $rep->where('uuid', '!=', $ignore);
        }
        if ($list = $rep->get()) {
            foreach ($list as $module) {
                $data[$module->id] = htmlentities($module->name);
            }
        }
        return $data;
    }
}
