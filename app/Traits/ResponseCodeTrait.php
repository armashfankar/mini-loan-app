<?php

namespace App\Traits;

trait ResponseCodeTrait
{
    /**
     * to get data for responseCode
     * @param int $code Response code param
     * @return array
     * @author Anil Chatla <anil.chatla@kissht.com>
     */
    public function getResponseCode($code)
    {
        $responseCode = [
            /*
            |--------------------------------------------------------------------------
            | GENERAL SUCCESS RESPONSE CODE
            |--------------------------------------------------------------------------
            */
            '1' => ['request_id' => '', 'success' => true, 'response_code' => 0, 'message' => 'Success', 'http_code' => 200],

            /*
            |--------------------------------------------------------------------------
            | GENERAL ERROR RESPONSE CODE
            |--------------------------------------------------------------------------
            */
            '101' => ['request_id' => '', 'success' => false, 'response_code' => 101, 'message' => 'Validation errors', 'http_code' => 400],
            '102' => ['request_id' => '', 'success' => false, 'response_code' => 102, 'message' => 'Application errors', 'http_code' => 200],
            '103' => ['request_id' => '', 'success' => false, 'response_code' => 103, 'message' => 'Request Id missing in header', 'http_code' => 400],
            '104' => ['request_id' => '', 'success' => false, 'response_code' => 104, 'message' => 'No Data Found', 'http_code' => 400],
            '105' => ['request_id' => '', 'success' => false, 'response_code' => 105, 'message' => 'Token is missing', 'http_code' => 400],
            '106' => ['request_id' => '', 'success' => false, 'response_code' => 106, 'message' => 'Invalid Token', 'http_code' => 400],
            '107' => ['request_id' => '', 'success' => false, 'response_code' => 107, 'message' => 'Token Expired', 'http_code' => 400],
            '108' => ['request_id' => '', 'success' => false, 'response_code' => 108, 'message' => 'Not Found', 'http_code' => 404],
            '109' => ['request_id' => '', 'success' => false, 'response_code' => 109, 'message' => 'Device type or version is missing in header', 'http_code' => 400],

            /*
            |--------------------------------------------------------------------------
            | SERVICE SPECIFIC RESPONSE CODE
            |--------------------------------------------------------------------------
            */
            '201' => ['request_id' => '', 'success' => false, 'response_code' => 201, 'message' => '', 'http_code' => 200],
            '204' => ['request_id' => '', 'success' => false, 'response_code' => 204, 'message' => 'No content', 'http_code' => 204],
        ];

        return $responseCode[$code];
    }
}