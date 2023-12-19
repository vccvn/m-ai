<?php


namespace App\Http\Controllers\Apis\Helpers;

/**
 * Respond
 *
 * Trait RespondTrait
 * @package App\Http\Helpers
 */

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait RespondTrait
{
    /*
     * Define version API
     * */
    public static string $VERSION_API = '1.0.0';

    /**
     * Respond with api result
     *
     * @param mixed $data
     * @param int   $statusCode
     *
     * @return JsonResponse
     */
    public function json(mixed $data, int $statusCode = Response::HTTP_OK): JsonResponse
    {
        $data_format = [
            'version' => self::$VERSION_API,
            'status'  => [
                'code'    => $statusCode,
                'message' => Response::$statusTexts[$statusCode]
            ],
            'data'    => $data ?? []
        ];

        return response()->json($data_format)->setStatusCode($statusCode);
    }
}
