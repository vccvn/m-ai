<?php

namespace App\Http\Controllers\Web\Lang;

use App\Http\Controllers\Web\WebController;

use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\Languages\LanguageRepository;
use Illuminate\Support\Facades\Session;

class LanguageController extends WebController
{
    protected $module = 'languages';

    protected $moduleName = 'Language';

    protected $flashMode = true;

    /**
     * repository chinh
     *
     * @var LanguageRepository
     */
    public $repository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(LanguageRepository $repository)
    {
        $this->repository = $repository;
        $this->init();
    }

    public function setLocale(Request $request, $locale = 'en')
    {
        if (!($lang = $this->repository->first(['slug' => $locale])))
            $lang = $this->repository->first(['is_default' => 1]);
        if (!$lang) $language = 'en';
        else $language = $lang->slug;
        config(['app.locale' => $language]);

        Session::put('website_language', $locale);
        return redirect()->back()->withCookie(cookie('locale', $locale, 36*24*365));
    }
}
