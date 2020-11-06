<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\Models\model\booking;
use App\Models\model\client;
use App\Models\model\theme;
use Validator;
use DB;

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
        //return $request;
        $referenceNumber = $this->generaterefnumber(date('Y-m-d H:i:s'));
        
        $validation = Validator::make($request->all(), [ 
            'book_date'         =>  'required|date_format:Y-m-d',
            'book_time'         =>  'required|date_format:H:i:s',
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

    public function checkAvailableTime(Request $request){
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
            return response()   ->json([
                'response'  => true,
                'message'   => 'Success',
                'data'      => $theme
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
}
