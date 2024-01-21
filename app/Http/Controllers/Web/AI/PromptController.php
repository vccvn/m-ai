<?php

namespace App\Http\Controllers\Web\AI;

use App\Http\Controllers\Web\WebController;

use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\GPT\PromptRepository;

class PromptController extends WebController
{
    protected $module = 'ai.prompts';

    protected $moduleName = 'Prompt';

    protected $flashMode = true;

    /**
     * repository chinh
     *
     * @var PromptRepository
     */
    public $repository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(PromptRepository $repository)
    {
        $this->repository = $repository;
        $this->init();
    }

    public function getSearchResults(Request $request){
        extract($this->apiDefaultData);
        $status = true;
        $data = [];
        $results = $this->repository->mode('mask')->paginate(12)->getResults($request);
        $json = array_merge(compact(...$this->apiSystemVars), $results->toArray());

        return $this->json($json);
    }
}
