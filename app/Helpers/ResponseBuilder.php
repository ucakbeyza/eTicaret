<?php
namespace App\Helpers;


class ResponseBuilder
{   //Başarılı apı yanıtı için
    public static function success($data = null, $message = "OK", $code = 200) 
    {
        return response()->json([
            "meta" => [
                "status" => true,
                "code" => $code,
                "message" => $message
            ],
            "data" => $data
        ], $code);
    }
    //Hata durumları
    public static function error($errors = [], $errorMessage = "INPUT_INVALID", $errorCode = 422)
    {
        return response()->json([
            "meta" => [
                "status" => false,
                "errorCode" => $errorCode,
                "errorMessage" => $errorMessage
            ],
            "data" => null,
            "errors" => $errors
        ], $errorCode);
    }
}
