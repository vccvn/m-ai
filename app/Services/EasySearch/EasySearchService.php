<?php

namespace App\Services\EasySearch;

use App\Models\EasySearchRef;
use App\Repositories\EasySearch\EasySearchRefRepository;
use App\Services\Service;
use Carbon\Carbon;

class EasySearchService extends Service
{
    protected $module = 'es';

    protected $moduleName = 'es';

    protected $flashMode = true;


    /**
     * VerificationRepository
     *
     * @var EasySearchRefRepository
     */
    public $repository = null;

    protected $errorMessage = null;

    /**
     * instance
     *
     * @var EasySearchService
     */
    public static $instance = null;

    /**
     * lấy instance
     *
     * @return EasySearchService
     */
    public static function getInstance(){
        if(!static::$instance) static::$instance = app(static::class);
        return static::$instance;
    }

    /**
     * Create a new Service instance.
     *
     * @return void
     */
    public function __construct(EasySearchRefRepository $repository)
    {
        $this->repository = $repository;
        $this->init();
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
    /**
     * grant Keywords
     *
     * @param string $ref
     * @param string $ref_id
     * @param string $text
     * @param string $field
     * @param integer $priority
     * @return EasySearchRef[]
     */
    public static function grantKeywords(string $ref, string $ref_id, string $text, $field = null, $priority = 10)
    {
        return static::getInstance()->repository->grantKeywords($ref, $ref_id, $text, $field, $priority);
    }

    /**
     * grand fulltext
     *
     * @param string $ref
     * @param string $ref_id
     * @param string $text
     * @param string $field
     * @param integer $priority
     * @return array<EasySearchRef>
     */
    public static function grantFullText(string $ref, string $ref_id, $text, $field = null, $priority = 10)
    {
        return self::getInstance()->repository->grantFullText($ref, $ref_id, $text, $field, $priority);
    }

    /**
     * grant keyword
     *
     * @param string $ref
     * @param string $ref_id
     * @param array $data
     * @return EasySearchRef[]
     */
    protected static function grantDAta(string $ref, string $ref_id, $data = [])
    {

        return self::getInstance()->repository->grantDAta($ref, $ref_id, $data);
    }

}
