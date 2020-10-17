<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\model\admin;
use Hash;
use Auth;
use Validatior;


class adminControllers extends Controller
{
    public function showPositions(Request $request){

        $position = admin::getPositionId($request);

        if(sizeOf($position) > 0){
            return response()->json([
                'success'   => true,
                'data'      => $position
            ],200);
        }else{
            return response() ->json([
                'success'   => false,
                'data'      =>  []
            ],200);
        }

    }

    public function registerAdmin(Request $request){
        if(Auth::user()->position_id == 1){

            $validation = Validator::make($request-> all(), [
                'fname'     =>  'required|string',
                'lname'     =>  'required|string',
                'username'  =>  'required|string',
                'email'     =>  'required|email|unique:admin_table'
            ],200);

            if($validation->fails()){
                $error = $validation->messages()->first();
                return response() ->json([
                    'response'  => false,
                    'message'   => $error
                ],200);
            }
        }
    }

}
