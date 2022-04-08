<?php


namespace App\Http\Controllers;
use App\Http\Controllers\Controller as Controller;


class BaseController extends Controller
{

    public function handleResponse($data, $message = "",  $code = 200)
    {
    	$res = [
            'success' => true
        ];
        if(!empty($message)){
            $res['message'] = $message;
        }
        if(!empty($data)){
            $res['data'] = $data;
        }
        return response()->json($res, $code);
    }

    public function handleError($errorMsg = [], $code = 404)
    {
    	$res = [
            'success' => false,
        ];
        if(!empty($errorMsg)){
            $res['errors'] = $errorMsg;
        }
        return response()->json($res, $code);
    }
}