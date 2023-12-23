<?php

namespace App\Http\Controllers\Admin\GPT;

use App\Http\Controllers\Admin\AdminController;

use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\GPT\CriteriaRepository;
use Illuminate\Support\Str;

class CriteriaController extends AdminController
{
    protected $module = 'gpt.criteria';

    protected $moduleName = 'Tiêu chí';

    protected $flashMode = true;

    /**
     * repository chinh
     *
     * @var CriteriaRepository
     */
    public $repository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(CriteriaRepository $repository)
    {
        $this->repository = $repository;
        $this->init();
        $this->activeMenu('gpt');
    }

    public function beforeSave(Request $request, $data, $old = null)
    {
        $data->name = strtoupper(Str::slug($data->label, '_'));
    }
}
