<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\Models\model\booking;
use App\Models\model\theme;
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
                'mobile_number'         =>  'required|string',
                'email'                 =>  'required|string',
                

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

        $query = booking::create([
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
                    'verification_number'       =>  $verification,
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

       // $dayTime = ["00:00:00", "11:59:59"];

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
        $query = booking::sendVerificationCode($request->id);

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
