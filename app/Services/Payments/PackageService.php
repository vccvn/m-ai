<?php
namespace App\Services\Payments;

use App\Repositories\Payments\PackageRepository;

class PackageService{

    public function __construct(protected PackageRepository $packageRepository)
    {

    }
}
