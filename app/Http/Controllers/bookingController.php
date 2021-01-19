<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\Models\model\booking;
use App\Models\model\theme;
use App\Models\model\verificationCode;
use App\Mail\BillingMain;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Validator;
use DB;
use Nexmo;
 

class bookingController extends Controller
{
    public  function createCode(Request $request){

        $code = rand(1000, 9999);

        $query = verificationCode::create([
                   
                    'verificationCode'      =>  $code,
                    'expiry_date'           =>  Carbon::now()->addHours(2)
        ]);

        if($query){
            return response()   ->json([
                'response'      =>  true,
                'message'       =>  'Generating verification code',      
            ],200);
        }else{
            return response()   ->json([
                'response'      =>  false,
                'message'       =>  'error'
            ],200);
        }
    }

    public function verifyClient(Request $request){
        
        $codeExist = DB::table('verification_codes')
                     //->where('VerificationCode', '=', $request->VerificationCode)
                     ->where('is_active', '=',1)
                     ->get(['verificationCode']);
        
        if(sizeOf($codeExist)>0) {
            $activeCode = DB::table('verification_codes')
                          ->where('verificationCode', '=', $request->verificationCode)
                          ->update(['is_active' => 0, 'verificationCode' => null]);
            if($activeCode){
                return response()       ->json([
                    'response'          =>  true,
                    'message'           =>  "Verification Confirmed"
                ],200);
            }else{
                return response()       ->json([
                    'response'          =>  false,
                    'message'           =>  "Please Check your Verification Code"
                ],200);
            }
        }else{
            return response()       ->json([
                'response'          =>  false,
                'message'           =>  "Verification Code doesn't Exists"
            ],200);
        }
    }

    public function generaterefnumber($date){
   
        $dataDate;
        $dataTime;

        $dateParts = explode(" ", $date);

        $dataDate = str_replace("-", "", $dateParts[0]);
        $dataTime = str_replace(":", "", $dateParts[1]);
        

        return $referencenumber = $dataDate.$dataTime;

    }

    public function bookingInfoSave(Request $request){
        
            $bookDate = Carbon::parse($request->end_date);
            $dateValidation = $bookDate->addDays(6)->toFormattedDateString('Y-m-d');

            $validation = Validator::make($request->all(), [ 
                
                'book_date'             =>  'required|date_format:Y-m-d|after:today|after:' . $dateValidation,
                'book_time'             =>  'required|date_format:H:i',
                'theme_id'              =>  'required|exists:themes,id',
                'maxpax'                =>  'required|int',
                'venue'                 =>  'required|string',
                'fname'                 =>  'required|string',
                'lname'                 =>  'required|string',
                'mobile_number'         =>  'required|numeric',
                'email'                 =>  'required|email',
                

            ]);
        
                        
        if($validation->fails()){
            $error = $validation->messages()->first();
            return response() -> json([
                'response'  => 'false',
                'message'   =>  $error
            ],200);
        }
     

        $exceedingPlayer = ($request->maxpax)-8;
        $exceedingAmount = $exceedingPlayer * 500;
        $vatComputation = ($exceedingAmount + 8000) * .12;
        $totalAmount = $vatComputation + $exceedingAmount + 8000;
        $initialComputation = ($totalAmount) / 2; 

        $verification = rand(1000, 9999);
        $referenceNumber = $this->generaterefnumber(date('Y-m-d H:i:s'));

        $query = booking::InsertGetId([
                    'reference_number'          =>  $request->reference_number,
                    'book_date'                 =>  $request->book_date,
                    'end_date'                  =>  Carbon::parse($request->book_date)->addDays(6),
                    'book_time'                 =>  $request->book_time,
                    'end_time'                  =>  Carbon::parse($request->book_time)->addHours(4),
                    'expiration_date'           =>  Carbon::parse($request->created_at)->addDays(3),
                    'theme_id'                  =>  $request->theme_id,
                    'maxpax'                    =>  $request->maxpax,
                    'venue'                     =>  $request->venue,
                    'fname'                     =>  $request->fname,
                    'lname'                     =>  $request->lname,
                    'mobile_number'             =>  $request->mobile_number,
                    'email'                     =>  $request->email,
                    'initial_payment'           =>  $initialComputation,
                    'total_amount'              =>  $totalAmount
        
        ]);

     
        
        if($query){
            return response()->json([
                'success'   =>  true,
                'message'   =>  "Successfully reserve your booking",
                
            ],200);
            
        }else{
            return response()->json([
                'success'   =>  false,
                'message'   =>  "There is something wrong with your booking, Please check your details"
                
            ],200);
        }
    }

    public function updateBookingExpired(Request $request){
        
        $expiry = Carbon::now()->toFormattedDateString('%M %d %Y');

        $expirationData = booking::where('is_booked', 0)
                          ->get('expiration_date');

        if($expiry = $expirationData){

            $DataExpired = booking::where('is_booked', 0)
                            ->update(['is_expired' => 1]);

            if($DataExpired){
                return response()       ->json([
                    'response'          =>  true
                    
                ],200);
            }else{
                return response()       ->json([
                    'rersponse'         =>  false,
                    'message'           =>  'no expire booking'
                ],200);
            }
        }else{
            return response()       ->json([
                'rersponse'         =>  false,
                'message'           =>  'no expired booking'
            ],200); 
        }
    }

    public function updateBookingDone(Request $request){
        
        $checkDone = Carbon::now()->format('h:i:s');

        $doneBooking = booking::where('is_booked', 1)
                       ->get('end_time');

        if($doneBooking == $checkDone){
            $updateBooking = booking::where('is_booked', 1)
                             ->update([
                                 'is_done'  => 1,
                             ]);
            if($updateBooking){
                return response()   ->json([
                    'response'      =>  true,
                ],200);
            }else{
                return response()   ->json([
                    'response'      =>  false
                ],200);
            }
        }
       
    }

    public function checkAvailability(Request $request){

        $checkBooking = booking::where('book_date', $request->book_date)
                        ->orderBy('book_time', 'asc')
                        ->get(['book_time']);
                      
        
        $checkDates = Carbon::parse($request->book_date);
        $availableDate = $checkDates->addDays(7)->toFormattedDateString();
                                   
        if(sizeOf($checkBooking) > 3){
            return response()      ->json([
                'response'          =>  true,
                'message'           =>  "Sorry this date is already Fully booked, Please choose another date from " . $availableDate . " onwards"
            ],200);
        }else    {
            return response()       ->json([
                'response'          => false,
                'message'           =>  "Date is available"
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

    public function getThemeValue(Request $request){

        $query = theme::where('id', $request->id)->get();

        if($query){
            return response()       ->json([
                'response'          =>  true,
                'data'              =>  $query
            ],200);
        }else{
            return response()       ->json([
                'response'          =>  false,
                'data'              =>  []
            ],200);
        }
        
        
    }

    public function getVerifCode (Request $request){

        $query = verificationCode::where('is_active', 1)->get(['verificationCode']);

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
    public static function showReciept(Request $request){
        
    //    $query = booking::getInfo($request);

    $query = booking::where('is_booked', 0)->get()->last();


        if($query){
            return response()       ->json([
                'response'      =>  true,
                'data'          =>  $query
            ],200);
        }else{
            return response()      ->json([
                'response'      =>  false,
                'data'          =>  []
            ],200);
        }
    }

    public function sendVerificationNumber(Request $request){

        $codelist = DB::connection('mysql')
                    ->table('verification_codes as a')
                    ->Select([
                        'a.verificationCode'
                    ])
                    ->where('is_active', 1)
                    ->get()->last();
                    
        $sendVerification = Nexmo::message()->send([
                    'to'    =>  '+63 921 721 5979',
                    'from'  =>  '+63 921 721 5979',
                    'text'  =>  "Your verification code is: ". $codelist->verificationCode
        ]);

        if($sendVerification){
            return response()   ->json([
                'response'  =>  true,
                'message'   =>  'SMS Verification code sent'
            ],200);
        }else{
            return response()   ->json([
                'response'  =>  false,
                'message'   =>  'Verificaiton Code Sending failed'
            ],200);
        }
    }

    

    public function sendBilling(Request $request){
        $send_name = $request->fname . " " . $request->lname;

        $send_email = booking::where('email', $request->email)->first();

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
                        
                        'a.initial_payment as downpayment',
                        'a.total_amount as amount'
                 )
                 ->where('email', '=', $request->id)->get();
                 
                 

                 $data = [];
                 foreach($query as $out){
                    $data[] = [
                        'email'                 =>$out->email,
                        'name'                  =>$out->name,
                        'referenceNumber'       =>$out->referenceNumber,
                        'contactNumber'         =>$out->contactNumber,
                        'date'                  =>$out->date,
                        'time'                  =>$out->time,
                        'maxpax'                =>$out->maxpax,
                        'venue'                 =>$out->venue,
                        'downpayment'           =>$out->downpayment,
                        'amount'                =>$out->amount, 

                    ];

                 }

        $to_name = $request->fname;
        $to_email = $request->email;
            $data = array(
                "contactNumber"     => $request->contactNumber, 
                "date"              => $request->date, 
                "time"              => $request->time,
                "referenceNumber"   => $request->referenceNumber,
                "maxpax"            => $request->maxpax,
                "venue"             => $request->venue,
                "downpayment"       => $request->downpayment,
                "amount"            => $request->amount,
                "name"              => $request->fname . " " . $request->lname
            );
        Mail::send("email", $data, function($message) use ($to_name, $to_email) {
        $message->to($to_email, $to_name)
        ->subject("Online Billing Payment Required");
        $message->from("murdermanilabilling@gmail.com","Online Billing Payment Required");
        });

    } 
}
