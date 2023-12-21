<?php

namespace App\Services\Themes;

use App\Models\Theme;
use App\Repositories\Files\FileRepository;
use App\Repositories\Options\OptionRepository;
use App\Repositories\Themes\ThemeRepository;
use App\Services\Service;

class ThemeService extends Service
{
    protected $module = 'themes';

    protected $moduleName = 'Verification';

    protected $flashMode = true;

    /**
     * @var \App\Repositories\Themes\ThemeRepository $repository
     */
    public $repository;

    /**
     * Undocumented variable
     *
     * @var OptionRepository
     */
    public OptionRepository $optionRepository;
    /**
     * Undocumented variable
     *
     * @var FileRepository
     */
    public FileRepository $fileRepository;

    /**
     * @var string $themeZipDir
     */
    protected $themeZipDir = null;


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ThemeRepository $themeRepository, OptionRepository $optionRepository, FileRepository $fileRepository)
    {
        $this->repository = $themeRepository;
        $this->optionRepository = $optionRepository;
        $this->fileRepository = $fileRepository;
        $this->init();
        $this->themeZipDir = base_path('themes/zip');
    }

    public function updateAllTheme() {
        echo "Update themes..\n";
        if(count($themes = $this->repository->get())){
            foreach ($themes as $theme) {
                echo "Kiểm tra theme  " . $theme->name . "...\n";
                $data= $this->devUpdate($theme);
                echo $data['message'] . "\n";
            }
        }
        echo "Xong!\n";
    }

    public function devUpdate($id = null, $active = false)
    {
        extract($this->apiDefaultData);
        $theme = $id?(is_a($id, Theme::class)?$id:$this->repository->findBy($this->primaryKeyName, $id)):null;

        if (
            /*  || */
            !$theme
            || !($filemanager = $this->getFilemanager($this->themeZipDir))

        ) {
            $message = 'Giao diện không tồn tại';
        } elseif (
               !($themeDir = base_path('themes/containers/' . $theme->slug))
            || !is_dir($assets = $themeDir . '/assets')
            || !is_dir($views = $themeDir . '/views')

        ) {
            $message = 'Cấu trúc thư mục không hợp lệ';
        } elseif (
            /*  || */
               !$filemanager->copyFolder($assets, public_path('static/assets/' . $theme->slug))
            || !$filemanager->copyFolder($views, resource_path('views/themes/' . $theme->secret_id))

        ) {
            $message = 'Không thể copy file';
        } elseif (!$theme->available && !$this->repository->update($theme->id, ['available' => 1])) {
            $message = 'Không thể cập nhật available';
        }
        elseif(!$this->repository->createMetaData($id)){

        }
        elseif ($active && !$this->repository->active($id)) {
            $message = 'Không thể active';
        }

        else {
            $status = true;
            $message = "Cập nhật thành công!";
        }
        return compact(...$this->apiSystemVars);
    }

}
