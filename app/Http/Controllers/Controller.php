<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function _result($data, $status = 200, $msg = 'OK')
    {
        return json_encode(array(
            'status' => $status,
            'msg' => $msg,
            'data' => $data
        ));
    }

    public function check($code,$email)
    {
        $temp = Invitation::where('code', '=', $code)->where('email','=',$email)
            ->first();
        if($temp)
        {
            if(!$temp->active or $temp->used or strtotime("now") > strtotime($temp->expiration))
                return False;
            else
                return True;
        }
        else
            return False;
    }
}
