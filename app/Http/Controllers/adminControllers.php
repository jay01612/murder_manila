<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\model\booking;
use App\Models\model\payment;
use Hash;
use Auth;
use Illuminate\Support\Facades\Validator;



class adminControllers extends Controller
{

    public function showPositions(Request $request){

        $position = User::getPositionId($request);

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

    public function logIn(Request $request){
        $validation = Validator::make($request->all(), [
            'username'  =>  'required|string',
            'password'  =>  'required|string'
        ]);

        if($validation->fails()){
            $error = $validation->messages()->first();
            return response()->json([
                'response'  =>  false,
                'message'   =>  $error
            ]);
        }

        $logIn = Auth::attempt([
            'username'  => $request->username,
            'password'  => $request->password
        ]);

        if($logIn){
            $accessToken = Auth::User()->createToken('authToken')->accessToken;

            return response()  ->json([
                'response'      =>  true,
                'message'       =>  "success",
                'token'         =>  $accessToken,
                'admin'         =>  Auth::user()
            ],200);
        }else{
            return response()      ->json([
                'response'      =>  false,
                'message'       =>  "User not Exist",
                'token'         =>  "",
                'admin'         =>  []
            ],200);
        }
    }


    public function registerAdmin(Request $request){
        if(Auth::User()->position_id == 1){   
            
            // $validation = Validator::make($request->all(), [
            //     'fname'     => 'required|string',
            //     'lname'     => 'required|string',
            //     'username'  => 'required|string',
            //     'email'     => 'required|email|unique:users'
            // ],200);
          
            // if($validation->fails()){
            //     $error = $validation->messages()->first();
            //     return response()->json([
            //         'response'  => false,
            //         'message'   => $error
            //     ],200);
            // }
            
            $firstname  =   str_shuffle($request->fname);
            $lastname   =   str_shuffle($request->lname);
                
            $password   =   $request->$firstname.$lastname;

            $query = User::addAdmin($request, $password);

            if($query){
                return response()->json([
                    'response'  =>  true,
                    'message'   =>  "successfully added an admin"
                ],200);
            }else{
                return response()->json([
                    'response'  =>  false,
                    'message'   =>  "there is something wrong"
                ],200);
            }
        }
    }

    public function deleteAdmin (Request $request){
        if(Auth::User()->position_id == 1){

            $query = User::DeleteAdmin($request);

            if($query){
                return response()   ->json([
                    'response'  =>  true,
                    'message'   =>  'Admin deleted'
                ],200);
            }else{
                return response()   ->json([
                    'response'  =>  false,
                    'message'   =>  'Error'
                ],200);
            }
        }
    }

    public function getPendBookings(Request $request){
        if(Auth::User()->position_id == 1 || Auth::User()->position_id == 2 || Auth::User()->position_id == 3){

            $query = User::getPendingBookings($request);

            if($query){
                return response()   ->json([
                    'response'  => true,
                    'data'      => $query
                ],200);
            }else{
                return response()   ->json([
                    'response'  =>  false,
                    'data'      =>  []
                ],200);    
            }
        }
    }

    public function getPaidBooking(Request $request){
        if(Auth::User()->position_id == 1 || Auth::User()->position_id == 2 || Auth::User()->position_id == 3){
            
            $query = User::PaidBookings($request);

            if($query){
                return response()   ->json([
                    'response'  => true,
                    'data'      => $query
                ],200);
            }else{
                return response()   ->json([
                    'response'  => false,
                    'data'      => []
                ],200);
            }
        }

    }

    public function bookingEdit(Request $request){
        if(Auth::User()->position_id == 1 || Auth::User()->position_id == 2){

            $query = booking::editBooking($request);

            if($query){
                return response()   ->json([
                    'response'      => true,
                    'message'       => 'Successfully Updated'
                ],200);
            }else{
                return response()   ->json([
                    'response'      =>  false,
                    'message'       =>  'There is somethingwrong'
                ],200);
            }
        }
    }

    public function paymentEdit(Request $request){
        if(Auth::User()->position_id == 1 || Auth::User()->position_id == 2){

            $query = booking::editPayment($request);

            if($query){
                return response()   ->json([
                    'response'      => true,
                    'message'       => 'Successfully Updated'
                ],200);
            }else{
                return response()   ->json([
                    'response'      =>  false,
                    'message'       =>  'There is somethingwrong'
                ],200);
            }
        }
    }




   
}
