<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\model\booking;
use App\Models\model\payment;
use Hash;
use Carbon\Carbon;
use Auth;
use DB;
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

    public function showPositionName(Request $request){

        $query = DB::connection('mysql')
                 ->table('access_levels')
                 ->select(
                        'access_name'
                 )->get();

        if(sizeOf($query)>0){
            return response()       ->json([
                'response'          =>  true,
                'data'              =>  $query
            ],200);
        }else{
            return response()       ->json([
                'response'          =>false,
                'data'              =>[]
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
                'admin'         =>  Auth::User()
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

    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()       ->json([
            'response'          =>true,
            'message'           =>'Successfully Log out'
        ],200);
    }


    public function registerAdmin(Request $request){
        $validation = Validator::make($request->all(), [
                'fname'     => 'required|string',
                'lname'     => 'required|string',
                'username'  => 'required|string',
                'password'  => 'required|string',
                'email'     => 'required|email'
            ]);
          
            if($validation->fails()){
                $error = $validation->messages()->first();
                return response()->json([
                    'response'  => false,
                    'message'   => $error
                ],200);
            }
            
        if(Auth::User()->position_id == 1){
            $query = DB::connection('mysql')
                     ->table('users')
                     ->insertGetId([
                        'fname'         =>  $request->fname,
                        'lname'         =>  $request->lname,
                        'username'      =>  $request->username,
                        'password'      =>  Hash::make($request->password),
                        'email'         =>  $request->email,
                        'position_id'   =>  $request->position_id,
                        'created_at'    =>  DB::raw("NOW()")
                     ]);

            if($query){
                return response()->json([
                    'response'  =>  true,
                    'message'   =>  "successfully added an admin"
                ],200);
            }else{
                return response()->json([
                    'response'  =>  false,
                    'message'   =>  "there is something wrong, Please check the details carefully"
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
        // if(Auth::User()->position_id == 1 || Auth::User()->position_id == 2 || Auth::User()->position_id == 3){

            $query = User::getPendingBookings($request);
            //$query = booking::where('is_booked', '=', 0)->where('is_cancelled', '=', 0 )->get();


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
    

    public function getPaidBooking(Request $request){
        // if(Auth::User()->position_id == 1 || Auth::User()->position_id == 2 || Auth::User()->position_id == 3){
            
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

    public function getDailyBookings(Request $request){
        // if(Auth::User()->position_id == 1 || Auth::User()->position_id == 2 || Auth::User()->position_id ==3){

            $query = DB::connection('mysql')
                     ->table('booking_table as a')
                     ->Select([
                        'a.reference_number as reference_number',
                        'a.book_date as start',
                        'a.end_date as end',
                        'a.book_time as time',
                        'a.theme_id as theme',
                        'a.maxpax as maxpax',
                        'a.venue as venue',
                        DB::raw("CONCAT(a.lname,',',a.fname) as name"),
                        //'lname',
                        'a.mobile_number as mobile_number',
                        'a.email as email',
                     ])
                     ->where('book_date', $request->book_date)
                     ->orderBy('book_time', 'asc')
                     ->get();

                     $data = [];
                     foreach($query as $out){
                        $data[$out->name]=[
                            'reference_number'      => $out->reference_number,
                            'start'                 => $out->start,
                            'end'                   => $out->end,
                            'theme'                 => $out->theme,
                            'maxpax'                => $out->maxpax,
                            'name'                  => $out->name,
                            'mobile_number'         => $out->mobile_number,
                            'email'                 => $out->email
                        ];
                     }


                  //$dateToday = Carbon::parse($request->book_date)->toFormattedDateString('Y-m-d');

                     if(sizeOf($data) > 0){
                         return response()      ->json([
                             'response'         => true,
                             'data'             => $data
                         ],200);
                     }else{
                         return response()      ->json([
                             'response'         =>  false,
                             //'message'          =>  "there is no booking for " .  $dateToday,
                         ],200);
                     }    
    }

    public function getExpiryBookings(Request $request){
         // if(Auth::User()->position_id == 1 || Auth::User()->position_id == 2 || Auth::User()->position_id ==3){

            $query = DB::connection('mysql')
                     ->table('booking_table as a')
                     ->select([
                            'a.reference_number as reference_number',
                            'a.book_date as start',
                            'a.end_date as end',
                            'a.book_time as time',
                            'a.theme_id as theme',
                            'a.maxpax as maxpax',
                            'a.venue as venue',
                            DB::raw("CONCAT(a.lname,',',a.fname) as name"),
                            'a.mobile_number as mobile_number',
                            'a.email as email',
                    ])
                     ->where('is_booked', '=', 0)
                     ->where('is_expired', '=', 1)
                     ->get();

            if($query){
                return response()   ->json([
                    'response'      =>  true,
                    'data'          =>  $query
                ],200);
            }else{
                return response()   ->json([
                    'rseponse'      =>  true,
                    'data'          => []
                ],200);
            }
       // }

    }

    

    public function bookingEdit(Request $request){
        // if(Auth::User()->position_id == 1 || Auth::User()->position_id == 2){

            $query = booking::editBooking($request);

            if($query){
                return response()   ->json([
                    'response'      => true,
                    'message'       => 'Booking successfully updated'
                ],200);
            }else{
                return response()   ->json([
                    'response'      =>  false,
                    'message'       =>  'There is something wrong'
                ],200);
            }
        }
    

    public function cancelBookingEdit(Request $request){
        // if(Auth::User()->position_id == 1 || Auth::user()->position_id == 2){

            $query = booking::editToCancelBooking($request);
            if($query){
                return response()   ->json([
                    'response'      => true,
                    'message'       => 'Successfully cancel the bookng'
                ],200);
            }else{
                return response()   ->json([
                    'response'      => false,
                    'message'       => 'There is something wrong'
                ],200);
            }
        }
    

    public function getCanceledBookings(Request $request){
        // if(Auth::User()->position_id == 1 || Auth::user()->position_id == 2 || Auth::user()-> position_id ==3){
            $query = User::cancelBookings($request);

            if($query){
                return response()  ->json([
                    'response'      =>  true,
                    'data'          =>  $query
                ],200);
            }else{
                return response()   ->json([
                    'response'      => false,
                    'data'          => []
                ],200);
            }
            
        }
    // }

    public function adminList(Request $request){
       // if(Auth::User()->position == 1){
            $query = User::getAdmins($request);

            if($query){
                return response()  ->json([
                    'response'      =>  true,
                    'data'          =>  $query
                ],200);
            }else{
                return response()   ->json([
                    'response'      => false,
                    'data'          => []
                ],200);
            }
       }
    

    
    public function sendRecievedHalfPaymentEmail (Request $request){
        if(Auth::User()->position_id == 1 || Auth::User()->position_id == 2){

            $name = $request->fname." ". $request->lname;
            $email = client::where('email', $request->email)->first();
            $data =array(
                'name'              =>  $request->fname." ".$request->lname,
                'referenceNumber'   =>  $request->referenceNumber,
                'initital_payment'  =>  $request->initial_payment,
                'date'              =>  $request->date,
                'time'              =>  $request->time,
                'theme'             =>  $request->theme,
                'maxpax'            =>  $request->maxpax,
                'venue'             =>  $request->venue,
                

            );
            Mail::send('initialpaymentemail', function($message) use ($name, $email){
                $message->to($email, $name)
                        ->subject('Booking Confirmation');
                $message->from('murdermanilabilling@gmail.com', 'Murder Manila');

            });
        }
    }

    public function sendRecievedFullPaymentEmail (Request $request){
        if(Auth::User()->position_id == 1 || Auth::User()->position_id == 2){
            
            $name = $request->fname." ". $request->lname;
            $email = client::where('email', $request->email)->first();
            $data =array(
                'name'              =>  $request->fname." ".$request->lname,
                'referenceNumber'   =>  $request->referenceNumber,
                'amount'            =>  $request->amount,
                'date'              =>  $request->date,
                'time'              =>  $request->time,
                'theme'             =>  $request->theme,
                'maxpax'            =>  $request->maxpax,
                'venue'             =>  $request->venue,
                

            );
            Mail::send('fullpaymentemail', function($message) use ($name, $email){
                $message->to($email, $name)
                        ->subject('Booking Confirmation');
                $message->from('murdermanilabilling@gmail.com', 'Murder Manila');

            });
        }
    }

    public function cancelledBookingEmail (Request $request){
        if(Auth::User()->position_id == 1 || Auth::User()->position_id == 2){
            
            $name = $request->fname." ". $request->lname;
            $email = client::where('email', $request->email)->first();
            $data =array(
                'name'              =>  $request->fname." ".$request->lname,
                'referenceNumber'   =>  $request->referenceNumber,
                'amount'            =>  $request->amount,
                'date'              =>  $request->date,
                'time'              =>  $request->time,
                'theme'             =>  $request->theme,
                'maxpax'            =>  $request->maxpax,
                'venue'             =>  $request->venue,
                

            );
            Mail::send('cancelbookingemail', function($message) use ($name, $email){
                $message->to($email, $name)
                        ->subject('Cancel Booking');
                $message->from('murdermanilabilling@gmail.com', 'Murder Manila');

            });
        }
    }

    
}
