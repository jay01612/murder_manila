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
use Illuminate\Support\Facades\Mail;



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

           return $query = DB::connection('mysql')
                     ->table('booking_table as booking')
                     ->Select([
                        'booking.id as id',

                        'booking.reference_number as reference_number',
            
                        DB::raw("CONCAT(booking.fname,' ',booking.lname) as name"),
                        'booking.email as email',
            
                        'theme.name as game',
                      
                        DB::raw("DATE_FORMAT(booking.book_date, '%M %d %Y') as book_date"),
                        DB::raw("TIME_FORMAT(booking.book_time, '%H:%i %p') as start"),
                        DB::raw("TIME_FORMAT(booking.end_time, '%H:%i %p') as end"),
                        DB::raw("DATE_FORMAT(booking.expiration_date, '%M %d %Y') as expiration_date"),
                        'booking.venue as venue',
                        'booking.maxpax as maxpax',
            
                        'booking.initial_payment as Downpayment',
                        'booking.total_amount as Total_Amount',
                        'booking.is_paid as is_paid'
                        
                     ])
                     ->leftjoin('themes as theme', 'booking.theme_id', '=', 'theme.id')
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
                            'email'                 => $out->email,
                            
                        ];  
                     }
                if(sizeOf($data) > 0){
                    return response()      ->json([
                        'response'         => true,
                        'data'             => $data
                    ],200);
                }else{
                    return response()      ->json([
                        'response'         =>  false,
                        // 'message'          =>  "there is no booking for " .  $dateToday,
                    ],200);
                }  
                    
    }

    public function getExpiryBookings(Request $request){
         // if(Auth::User()->position_id == 1 || Auth::User()->position_id == 2 || Auth::User()->position_id ==3){

            $query = DB::connection('mysql')
                     ->table('booking_table as booking')
                     ->select([
                        'booking.id as id',

                        'booking.reference_number as reference_number',
            
                        DB::raw("CONCAT(booking.fname,' ',booking.lname) as name"),
                        'booking.email as email',
            
                        'theme.name as game',
                      
                        DB::raw("DATE_FORMAT(booking.book_date, '%M %d %Y') as date"),
                        DB::raw("TIME_FORMAT(booking.book_time, '%H:%i %p') as time"),
                        DB::raw("TIME_FORMAT(booking.end_time, '%H:%i %p') as end_time"),
                        DB::raw("DATE_FORMAT(booking.expiration_date, '%M %d %Y') as expiration_date"),
                        'booking.venue as venue',
                        'booking.maxpax as maxpax',
            
                        'booking.initial_payment as Downpayment',
                        'booking.total_amount as Total_Amount',
                        'booking.is_paid as is_paid'
                    ])
                    ->leftjoin('themes as theme', 'booking.theme_id', '=', 'theme.id')
                     ->where('booking.is_booked', '=', 0)
                     ->where('booking.is_expired', '=', 1)
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

    public function getDoneBookings(Request $request){
        // if(Auth::User()->position_id == 1 || Auth::User()->position_id == 2 || Auth::User()->position_id ==3){

           $query = DB::connection('mysql')
                    ->table('booking_table as booking')
                    ->select([
                        'booking.id as id',

                        'booking.reference_number as Reference_Number',
            
                        DB::raw("CONCAT(booking.fname,' ',booking.lname) as name"),
                        'booking.email as email',
            
                        'theme.name as game',
                      
                        DB::raw("DATE_FORMAT(booking.book_date, '%M %d %Y') as date"),
                        DB::raw("TIME_FORMAT(booking.book_time, '%H:%i %p') as time"),
                        DB::raw("TIME_FORMAT(booking.end_time, '%H:%i %p') as end_time"),
                        DB::raw("DATE_FORMAT(booking.expiration_date, '%M %d %Y') as expiration_date"),
                        'booking.venue as venue',
                        'booking.maxpax as maxpax',
            
                        'booking.initial_payment as Downpayment',
                        'booking.total_amount as Total_Amount',
                        'booking.is_paid as is_paid'
                   ])
                    ->leftjoin('themes as theme', 'booking.theme_id', '=', 'theme.id')
                    ->where('booking.is_booked', '=', 1)
                    ->where('booking.is_done', '=', 1)
                    ->where('booking.is_expired', '=', 0)
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

        public function isPartialPaid(Request $request){
            // if(Auth::User()->position_id == 1 || Auth::User()->position_id == 2){
    
                $query = booking::editPartialPaid($request);
    
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

            public function isFullPaid(Request $request){
                // if(Auth::User()->position_id == 1 || Auth::User()->position_id == 2){
        
                    $query = booking::editFullPaid($request);
        
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
       // if(Auth::User()->position_id == 1 || Auth::User()->position_id == 2){
            
            $query = DB::connection('mysql')
                    ->table('booking_table as a')
                    ->select(
                        'a.email as email',

                        DB::raw("CONCAT(a.lname,',',a.fname) as name"),
                        'a.reference_number as referenceNumber',
                        'a.mobile_number as contactNumber',
                        'a.book_date as date',
                        'a.book_time as time',
                        
                        'a.maxpax as maxpax',
                        'a.venue as venue',
                        'b.name as theme',
                        'a.initial_payment as downpayment',
                        
                    )
                    ->leftjoin('themes as b', 'b.id', '=', 'a.theme_id')
                    ->where('a.is_initial_paid', '=', 1)
                    ->get();
            foreach($query as $out){
                $to_name = $out->name;
                $to_email = $out->email;
                $data =array(
                    "referenceNumber"   => $out->referenceNumber,
                    "name"              => $out->name,
                    "theme"             => $out->theme,
                    "date"              => $out->date,
                    "time"              => $out->time,
                    "maxpax"            => $out->maxpax,
                    "venue"             => $out->venue,
                    "downpayment"       => $out->downpayment
                );
                Mail::send("initialpaymentemail", $data, function($message) use ($to_name, $to_email){
                    $message->to($to_email, $to_name)
                            ->subject("Booking Confirmation");
                    $message->from("murdermanilabilling@gmail.com", "Murder Manila");

                });
            }
       // }
    }

    public function sendRecievedFullPaymentEmail (Request $request){
        // if(Auth::User()->position_id == 1 || Auth::User()->position_id == 2){
            $query = DB::connection('mysql')
                    ->table('booking_table as a')
                    ->select(
                        'a.email as email',

                        DB::raw("CONCAT(a.lname,',',a.fname) as name"),
                        'a.reference_number as referenceNumber',
                        'a.mobile_number as contactNumber',
                        'a.book_date as date',
                        'a.book_time as time',
                        
                        'a.maxpax as maxpax',
                        'a.venue as venue',
                        'b.name as theme',
                        'a.total_amount as total_amount',
                        
                    )
                    ->leftjoin('themes as b', 'b.id', '=', 'a.theme_id')
                    ->where('a.is_fully_paid', '=', 1)
                    ->get();
                    foreach($query as $out){
                        $to_name = $out->name;
                        $to_email = $out->email;
                        $data =array(
                            "referenceNumber"   => $out->referenceNumber,
                            "name"              => $out->name,
                            "theme"             => $out->theme,
                            "date"              => $out->date,
                            "time"              => $out->time,
                            "maxpax"            => $out->maxpax,
                            "venue"             => $out->venue,
                            "total_amount"      => $out->total_amount
                        );
                        Mail::send("fullpaymentemail", $data, function($message) use ($to_name, $to_email){
                            $message->to($to_email, $to_name)
                                    ->subject("Booking Confirmation");
                            $message->from("murdermanilabilling@gmail.com", "Murder Manila");

                        });
                    }
        //}
    }


    public function cancelledBookingEmail (Request $request){
       // if(Auth::User()->position_id == 1 || Auth::User()->position_id == 2){
            $query = DB::connection('mysql')
                    ->table('booking_table as a')
                    ->select(
                        'a.email as email',

                        DB::raw("CONCAT(a.lname,',',a.fname) as name"),
                        'a.reference_number as referenceNumber',
                        'a.mobile_number as contactNumber',
                        'a.book_date as date',
                        'a.book_time as time',
                        
                        'a.maxpax as maxpax',
                        'a.venue as venue',
                        'b.name as theme',
                        'a.initial_payment as downpayment',
                        'a.total_amount as total_amount'
                        
                    )
                    ->leftjoin('themes as b', 'b.id', '=', 'a.theme_id')
                    ->where('a.is_cancelled', '=', 1)
                    ->get();
            foreach($query as $out){
                $to_name = $out->name;
                $to_email = $out->email;
                $data =array(
                    "referenceNumber"   => $out->referenceNumber,
                    "name"              => $out->name,
                    "theme"             => $out->theme,
                    "date"              => $out->date,
                    "time"              => $out->time,
                    "maxpax"            => $out->maxpax,
                    "venue"             => $out->venue,
                    "downpayment"       => $out->downpayment,
                    "total_amount"      => $out->total_amount
                );
                Mail::send("cancelbookingemail", $data, function($message) use ($to_name, $to_email){
                    $message->to($to_email, $to_name)
                            ->subject("Booking Cancellation");
                    $message->from("murdermanilabilling@gmail.com", "Murder Manila");

                });
            }
        //}
    }

    public function expiredBookingEmail (Request $request){
        // $query = DB::connection('mysql')
        //         ->table('booking_table as a')
        //         ->select(
        //             'a.email as email',

        //             DB::raw("CONCAT(a.lname,',',a.fname) as name"),
        //             'a.reference_number as referenceNumber',
        //             'a.mobile_number as contactNumber',
        //             'a.book_date as date',
        //             'a.book_time as time',
                    
        //             'a.maxpax as maxpax',
        //             'a.venue as venue',
        //             'b.name as theme',
        //             'a.initial_payment as downpayment',
        //             'a.total_amount as total_amount'
                    
        //         )
        //         ->leftjoin('themes as b', 'b.id', '=', 'a.theme_id')
        //         ->where('a.is_booked', '=', 0)
        //         ->get();
        $query = booking::where('expiration_date', '=', Carbon::now()->format('Y-m-d'))
                        ->where('is_booked',0)
                        ->where('is_cancelled', 0)
                        ->where('is_initial_paid', 0)
                        ->where('is_fully_paid', 0)
                        ->where('is_paid',0)
                        ->get();
        foreach($query as $out){
            $deleteData = booking::where('id', $out->id)->delete();
            $updateData = booking::where('id', $out->id)
                          ->update(['is_expired' => 1]);
            $to_name = $out->name;
            $to_email = $out->email;
            $data =array(
                "referenceNumber"   => $out->referenceNumber,
                "name"              => $out->name,
                "theme"             => $out->theme,
                "date"              => $out->date,
                "time"              => $out->time,
                "maxpax"            => $out->maxpax,
                "venue"             => $out->venue,
                "downpayment"       => $out->downpayment,
                "total_amount"      => $out->total_amount
            );
            
            Mail::send("expiredemail", $data, function($message) use ($to_name, $to_email){
                $message->to($to_email, $to_name)
                        ->subject("Booking expired");
                $message->from("murdermanilabilling@gmail.com", "Murder Manila");
            });                
        }
        return response()   ->json([
            'response'      =>  true
        ],200);
    }

        // $expiry = Carbon::now()->toFormattedDateString('%M %d %Y');

        // $expirationData = booking::where('is_booked', 0)
        //                     ->get('expiration_date');

        // if($expiry == $expirationData){
        //     $DataExpired = booking::where('is_booked', 0)
        //     ->update(['is_expired' => 1])->delete();

        //     if($DataExpired){
        //         return response()       ->json([
        //             'response'          =>  true    
        //         ],200);
        //     }else{
        //         return response()       ->json([
        //             'rersponse'         =>  false,
        //             'message'           =>  'no expire booking'
        //         ],200);
        //     }
        // }else{
        //     return response()       ->json([
        //         'rersponse'         =>  false,
        //         'message'           =>  'no expired booking'
        //     ],200); 
        // }

    

    public function doneBookingEmail (Request $request){
        $query = DB::connection('mysql')
                ->table('booking_table as a')
                ->select(
                    'a.email as email',

                    DB::raw("CONCAT(a.lname,',',a.fname) as name"),
                    'a.reference_number as referenceNumber',
                    'a.mobile_number as contactNumber',
                    'a.book_date as date',
                    'a.book_time as time',
                    
                    'a.maxpax as maxpax',
                    'a.venue as venue',
                    'b.name as theme',
                    'a.initial_payment as downpayment',
                    'a.total_amount as total_amount'
                    
                )
                ->leftjoin('themes as b', 'b.id', '=', 'a.theme_id')
                ->where('book_date', '=', Carbon::now()->format('Y-m-d'))
                ->where('end_time', '=', Carbon::now()->format('H:i:s'))
                ->where('a.is_booked', '=', 1)
                ->where('is_cancelled', 0)
                ->where('a.deleted_at', '=', null)
                ->get();
            
        // $query = booking::where('book_date', '=', Carbon::now()->format('Y-m-d'))
        //          ->where('end_time', '=', Carbon::now()->format('H:i:s'))
        //          ->where('is_booked',1)
        //          ->where('is_cancelled', 0)
        //          ->where('is_initial_paid', 1)
        //          ->where('is_fully_paid', 1)
        //          ->where('is_paid',0)
        //          ->get();           
        foreach($query as $out){

            $updateData = booking::where('id', $request->id)
            ->update(['is_done' => 1]);
            $deleteData = booking::where('id', $out->id)->delete();
            
            
            $to_name = $out->name;
            $to_email = $out->email;
            $data =array(
                "referenceNumber"   => $out->referenceNumber,
                "name"              => $out->name,
                "theme"             => $out->theme,
                "date"              => $out->date,
                "time"              => $out->time,
                "maxpax"            => $out->maxpax,
                "venue"             => $out->venue,
                "downpayment"       => $out->downpayment,
                "total_amount"      => $out->total_amount
            );
            
            Mail::send("doneemailbooking", $data, function($message) use ($to_name, $to_email){
                $message->to($to_email, $to_name)
                        ->subject("Finish booking");
                $message->from("murdermanilabilling@gmail.com", "Murder Manila");
            });
        }
        return response()   ->json([
            'response'      =>  true,
        ],200);
    }
        // $checkDone = Carbon::now()->format('h:i:s');
        // $doneBooking = booking::where('book_date', $request->book_date)
            //                ->get('end_time');

    
    //     if($checkDone == $doneBooking){
    //         $DataDone = booking::where('is_booked', 1)
    //         ->update(['is_done' => 1])->delete();
    //         if($DataDone){
    //             return response()       ->json([
    //                 'response'          =>  true    
    //             ],200);
    //         }else{
    //             return response()       ->json([
    //                 'rersponse'         =>  false,
    //                 'message'           =>  'no booking done'
    //             ],200);
    //         }
    //     }else{
    //         return response()       ->json([
    //             'rersponse'         =>  false,
    //             'message'           =>  'no booking done'
    //         ],200); 
    //     }                              
    // }

}
