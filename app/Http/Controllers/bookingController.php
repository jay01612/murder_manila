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

        $validation = Validator::make($request->all(), [ 

            'mobile_number'         =>  'required|numeric',
        ]);
    
                    
    if($validation->fails()){
        $error = $validation->messages()->first();
        return response() -> json([
            'response'  => 'false',
            'message'   =>  $error
        ],200);
    }

        $code = rand(1000, 9999);

        $query = verificationCode::create([
                    'mobileNumber'          =>  $request->mobileNumber,
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
                     ->where('VerificationCode', '=', $request->VerificationCode)
                     ->where('is_active', '=',1)
                     ->get(['verificationCode']);
        
        if($codeExist){
            $activeCode = DB::table('verification_codes')
                          ->where('verificationCode', '=', $request->verificationCode)
                          ->update(['is_active' => 0]);
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

        $referenceNumber = $this->generaterefnumber(date('Y-m-d H:i:s'));

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

        $query = booking::insertGetId([
                    'reference_number'          =>  $referenceNumber,
                    'book_date'                 =>  $request->book_date,
                    'end_date'                  =>  Carbon::parse($request->book_date)->addDays(7),
                    'book_time'                 =>  $request->book_time,
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

    public function checkAvailability(Request $request){

        $checkBooking = booking::where('book_date', $request->book_date)
                        ->orderBy('book_time', 'asc')
                        ->get(['book_time']);
                      
        
        $checkDates = Carbon::now();
        $availableDate = $checkDates->addDays(6)->toFormattedDateString();
                                   
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

    public function getVerifCode (Request $request){

        $query = booking::getVerificationCode($request);

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
                'message'   =>  'Message sent'
            ],200);
        }else{
            return response()   ->json([
                'response'  =>  false,
                'message'   =>  'Sending failed'
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
}
