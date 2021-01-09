<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\Models\model\booking;
use App\Models\model\client;
use App\Models\model\theme;
use App\Models\model\payment;
use App\Mail\BillingMain;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Validator;
use DB;
use Nexmo;

class bookingController extends Controller
{
    public function generaterefnumber($date){
   
        $dataDate;
        $dataTime;

        $dateParts = explode(" ", $date);

        $dataDate = str_replace("-", "", $dateParts[0]);
        $dataTime = str_replace(":", "", $dateParts[1]);
        

        return $referencenumber = $dataDate.$dataTime;

    }

    public function bookingInfoSave(Request $request){

        $referenceNumber = $this->generaterefnumber(date('Y-m-d H:i:s'));
        $validation = Validator::make($request->all(), [ 
            'book_date'         =>  'required|date_format:Y-m-d',
            'book_time'         =>  'required|date_format:H:i',
            'theme_id'          =>  'required|exists:themes,id',
            'maxpax'            =>  'required|int',
            'venue'             =>  'required|string'
            
        ]);
            
        if($validation->fails()){
            $error = $validation->messages()->first();
            return response() -> json([
                'response'  => 'false',
                'message'   =>  $error
            ],200);
        }

        $query = booking::bookingInfo($request, $referenceNumber);
        
        if($query){
            return response()->json([
                'success'   =>  true,
                'message'   =>  "successfully save",
                
            ],200);
        }else{
            return response()->json([
                'success'   =>  false,
                'message'   =>  "there is something wrong"
                
            ],200);
        }
    }

    public function checkAvailability(Request $request){
        
        

        // $sched1 = ["00:00:00", "05:59:59"];
        // $sched2 = ["06:00:00", "11:59:59"];
        // $sched3 = ["12:00:00", "17:59:59"];
        // $sched4 = ["18:00:00", "23:59:59"];

        $checkBooking;

        // if(($request->book_time > "00:00:00") && ( $request->book_time < "05:59:59")){
            $checkBooking = booking::where('book_date', $request->book_date)
                            ->orderBy('book_time', 'asc')
                            ->get(['book_time']);      
                            $data = [];
                            foreach($checkBooking as $out){
                                $data[] = $out->book_time;
                            }
                            
                        //    $bookedDates = count($data);

                            if(sizeOf($checkBooking) > 3){
                                return response()      ->json([
                                    'response'          =>  true,
                                    'message'           =>  " date and time is taken"
                                ],200);
                            }else    {
                                return response()       ->json([
                                    'response'          => false,
                                    'message'           =>  "date and time is available"
                                ],200);
                            }          
    }      
    //     }else if(($request->book_time > "06:00:00") && ($request->book_time < "11:59:59")){
    //         $checkBooking = booking::where('book_date', $request->book_date)
    //                         ->orderBy('book_time', 'asc')
    //                         ->get(['book_time']);
    //                         $data = [];
    //                         foreach($checkBooking as $out){
    //                             $data[] = $out->book_time;

    //                         }

    //                         $bookedDates = array($data);
    //                         if(sizeOf($bookedDates)>0){
    //                             return response()      ->json([
    //                                 'response'          =>  true,
    //                                 'message'           =>  "time is taken"
    //                             ],200);
    //                         }else{
    //                             return response()       ->json([
    //                                 'response'          => false,
    //                                 'message'           =>  "time  available"
    //                             ],200);
    //                         }
    //     }else if(($request->book_time > "12:00:00") && ($request->book_time < "15:59:59")){
    //         $checkBooking = booking::where('book_date', $request->book_date)
    //                         ->orderBy('book_time', 'asc')
    //                         ->get(['book_time']);
    //                         $data = [];
    //                         foreach($checkBooking as $out){
    //                             $data[] = $out->book_time;
    //                         }

    //                         if(!sizeOf($checkBooking) != $sched3){
    //                             return response()      ->json([
    //                                 'response'          =>  true,
    //                                 'message'           =>  "time is taken"
    //                             ],200);
    //                         }else{
    //                             return response()       ->json([
    //                                 'response'          => false,
    //                                 'message'           =>  "time  available"
    //                             ],200);
    //                         }
    //     }else if(($request->book_time > "16:00:00") && ($request->book_time < "23:59:59")){
    //         $checkBooking = booking::where('book_date', $request->book_date)
    //                         ->orderBy('book_time', 'asc')
    //                         ->get(['book_time']);
    //                         $data = [];
    //                         foreach($checkBooking as $out){
    //                             $data[] = $out->book_time;
    //                         }

    //                         if(!sizeOf($checkBooking)>0){
    //                             return response()      ->json([
    //                                 'response'          =>  true,
    //                                 'message'           =>  "time is taken"
    //                             ],200);
    //                         }else{
    //                             return response()       ->json([
    //                                 'response'          => false,
    //                                 'message'           =>  "time  available"
    //                             ],200);
    //                         }
    //     }else{
    //         return response()       ->json([
    //             'response'          => false,
    //             'message'           =>  "time  available"
    //         ],200);
    //     }
    // }
    
    public function clientInfoSave(Request $request){
        
        $validation = Validator::make($request->all(), [
            'fname'                 =>  'required|string',
            'lname'                 =>  'required|string',
            'mobile_number'         =>  'required|int',
            'email'                 =>  'required|string'
            
        ]);

        if($validation->fails()){
            $error = $validation->messages()->first();
            return response()   ->json([
                'response'  => false,
                'message'   => $error
        
            ],200);
        }

        $clientSave = client::saveClientInfo($request);

        if($clientSave){
            return response() ->json([
                'success'   => true,
                'message'   => 'Client Save',
                'data'      =>  $clientSave
                
                
            ],200);
            return response() ->json([
                'success'   => false,
                'message'   => 'There is something wrong',
                'data'      =>  []
                
            ],200);
        }
    }

    public function checkAvailableTime(Request$request){
        $query = booking::checkAvailableBooking($request);

        if(!$query){
            return response()   ->json([
                'success'   =>  true,
                'message'   =>  'date and time is available'

            ],200);
        }else{
            return response()   ->json([
                'success'   =>  false,
                'message'   =>  'Pls choose another date amd time'
            ],200);
        }    
    }

    public function showTheme(Request $request){
        
        $theme = theme::getThemeId($request);

        if(sizeOf($theme) > 0){
            $dataThemes = [];
            foreach($theme as $out){
                $dataThemes = $out->id;
            }

            $getThemes = DB::connection('mysql')
            ->table('themes')
            ->select(
                'themes.id',
                'themes.name'
            )
            ->get();
            return response()   ->json([
                'response'  => true,
                'message'   => 'Success',
                'data'      => $theme
            ],200);
        }else {
            return response()   ->json([
                'response'  =>  false,
                'message'   =>  'fails',
                'data'      =>  []
            ],200);
        }
    }

    public function bookingSummary (Request $request){

        $query = booking::ClientBookingSummary($request);

        if($query){
            return response() ->json([
                'success'   => true,
                'data'      =>  $query
            
            ],200);
        }else{
            return response() ->json([
                'success'   => false,
                'data'      => []
            
            ],200);
        }
    }

    public function bookingAmount(Request $request){


        $query = booking::clientBookingAmount($request);

        if($query){
            return response()   ->json([
                'success'       => true,
                'message'       => 'Here is the total billing'
            ],200);
        }else{
            return response()   ->json([
                'success'       => false,
                'message'       => 'there is an error'
            ],200);
        }
    }

    public function amountBookingSummary(Request $request){

        $query = booking::bookingSummaryWithAmount($request);

        if($query){
            return response()   ->json([
                'success'       => true,
                'data'          => $query
            ],200);
        }else{
            return response()   ->json([
                'success'       => false,
                'data'          => []
            ],200);
        }
    }

    public function send(){

    }

    public function sendVerificationNumber(Request $request){
        $query = client::getVerificationCode($request->id);

        $sendVerification = Nexmo::message()->send([
                    'to'    =>  '+63 921 721 5979',
                    'from'  =>  '+63 921 721 5979',
                    'text'  =>  "Your verification code is: ". $query[0]->verification_number
        ]);

        if($sendVerification){
            return response()   ->json([
                'response'  =>  true,
                'message'   =>  'Message sent'
            ],200);
        }else{
            return response()   ->json([
                'response'  =>  false,
                'message'   =>  'Sending failed'
            ],200);
        }
    }

    public function updateVerifyClient(Request $request){

        $query = client::verifyClient($request);
        $verified = client::updateVerified($request);

        if(!blank($query)){

            if($verified){
                return response()   ->json([
                    'response'      =>  true,
                    'message'       =>  'Successfully verified'
                ],200);
            }else{
                return response()   ->json([
                    'response'      =>  false,
                    'message'       =>  'there is something wrong'
                ],200);
            }

        }else{
            return response()   ->json([
                'response'      =>  false,
                'message'       =>  'error'
            ],200);
        }
    }

    public function sendBilling(Request $request){
        $name = $request->fname . " " . $request->lname;
        //$email = $request->email;
        $email = client::where('email', $request->email)->first();
        $data = array(
            'name'              =>  $request->fname.",".$request->lname,
            'referenceNumber'   =>  $request->referenceNumber,
            'contactNumber'     =>  $request->contactNumber,
            'theme'             =>  $request->themes,
            'date'              =>  $request->date,
            'time'              =>  $request->time,
            'maxpax'            =>  $request->maxpax,
            'venue'             =>  $request->venue,
            'amount'            =>  $request->amount,
        );
        Mail::send('email', $data, function($message) use ($name, $email){
            $message->to($email, $name)
                    ->subject('Murder Manila Billing');
            $message->from('murdermanilabilling@gmail.com','Murder Manila');
        });
    }

    public function saveInitialPaymentInfo(Request $request){

        // $validation = Validator::make($request->all(), [ 
        //     'amount'         =>  'required|int',
            
        // ]);
            
        // if($validation->fails()){
        //     $error = $validation->messages()->first();
        //     return response() -> json([
        //         'response'  => 'false',
        //         'message'   =>  $error
        //     ],200);
        // }

        $query = payment::InitialPaymentInfo($request);

        if($query){
            return response()       ->json([
                'response'          =>  true,
                'message'           =>  'successfully save'
            ],200);
        }else{
            return response()       ->json([
                'response'          =>  false,
                'message'           =>  'error'
            ],200);
        }



    }
 
}
