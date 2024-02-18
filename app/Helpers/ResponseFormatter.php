<?php

namespace App\Helpers;
// mendefinisakan class pada direktori yang didefine di namespace tersebut, selain itu hal tersebut juga digunakan untuk mengantisipasi terjadinya error ketika terdapat class yang sama.

class ResponseFormatter
{

    // static property atau static method merupakan bagian dari class yang mana ketika ingin menggunakannya class tersebut tidak perlu diintansiasikan menjadi object untuk mengaksesnya, tapi bisa langsung menyebutkan nama class yang diikuti operator ‘::’. Contohnya NamaClass::NamaProperty atau NamaClass::NamaMethod().  Dan untuk pemanggilan static property atau static method yang masih berada di dalam kelas tersebut gunakan self::NamaProperty atau self::NamaMethod

    protected static $response = [ // static property
        'code' => null,
        'success' => null,
        'message' => null,
        'data' => null,
    ];

    public static function sendResponse($code = null, $success = null, $message = null, $data = null) // static method
    // format ketika hasil dari suatu proses dieksekusi. Memiliki empat parameter yang nantinya argumennya akan disimpan kedalam static property dan di return dalam bentuk response JSON.
    {
        self::$response['code'] = $code;
        self::$response['success'] = $success;
        self::$response['message'] = $message;
        self::$response['data'] = $data;

        return response()->json(self::$response, self::$response['code']);
    }
}
