<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class NotReportException extends Exception
{
    
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

}
