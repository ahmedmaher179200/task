<?php

namespace App\Traits;

trait response
{
    public static function success($message, $statusCode = 200, $dataName = '', $data = ''){
        if($dataName == ''){
            return response()->json([
                'successful' => true,
                'message' => $message,
            ], $statusCode);
        } else {
            return response()->json([
                'successful' => true,
                'message' => $message,
                $dataName => $data,
            ], $statusCode);
        }
    }

    public static function failed($message, $statusCode = 400, $status = 'E00'){
        return response()->json([
            'successful' => false,
            'status'     => $status,
            'message'    => $message,
        ], $statusCode);
    }
}