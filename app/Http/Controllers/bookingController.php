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
    
        
    // public function clientAddBooking (Request $request) {

    //     $newDate = str_replace('/', '', $request->date);
    //     $newTime = str_replace(':', '', $request->time);

    //     return $referenceNumber = $newDate . $newTime;

    //     $addbooking = booking::bookClient($request, $referenceNumber);

    //     if($addbooking){
    //         return response() ->json([
    //             'success'   => true,
    //             'data'      =>  $addbooking,
    //             'message'   => 'Successfully made a booking'
    //         ],200);
    //         return response() ->json([
    //             'success'   => false,
    //             'data'      =>  [],
    //             'message'   => 'There is something wrong'
    //         ],200);
    //     }
    
    //     $validation = Validator::make($request->all(), [
    //             // 'reference_number'  =>  'required|unique:booking_table,reference_number,NULL,id,reference_number,id',
    //             'reference_number'  =>  'required|unique:booking_table,reference_number,id',
    //             'book_date'         =>  'required|date_format:Y-m-d',
    //             'book_time'         =>  'required|date_format:H:i:s',
    //             'theme_id'          =>  'required|exists:themes,id',
    //             'discount_id'       =>  'required|exists:discounts,id',
    //             'maxpax'            =>  'required|int',
    //             'venue'             =>  'required|string'

    //     ]);

    //     if($validation->fails()){
    //         $error = $validation->messages()->first();
    //         return response() -> json([
    //             'response'  => 'false',
    //             'message'   =>  $error
    //         ],200);
    //     }

       
   //}

    // public function checkIfAvailable (Requeset $request) {

    //     $query = booking::checkAvailableBooking($request);

    //     if(!$query){
    //         return response()   ->json([
    //             'response'  =>true,
    //             'message'   =>'date and time available'
    //         ],200);
    //     }else{
    //         return response()   ->json([
    //             'response'  =>false,
    //             'message'   =>'choose another date and time'
    //         ],200);
    //     }
    // }

    
    // public function clientAddBooking (Request $request){

    //     $referenceNumber = $this->generaterefnumber(date('Y-m-d H:i:s'));
         

    //      //$checkifexists = $this->checkifrefexists($referenceNumber);

    //     if($checkexists){   
    //         $validation = Validator::make($request->all(), [
               
    //             'book_date'         =>  'required|date_format:Y-m-d',
    //             'book_time'         =>  'required|date_format:H:i:s',
    //             'theme_id'          =>  'required|exists:themes,id',
    //             'maxpax'            =>  'required|int',
    //             'venue'             =>  'required|string'
    
    //         ]);
    
    //         if($validation->fails()){
    //              $error = $validation->messages()->first();
    //             return response() -> json([
    //             'response'  => 'false',
    //             'message'   =>  $error
    //             ],200);
    //         }
    //     }
    //     $addInfo = booking::bookClient($request, $referenceNumber);
    
    //     if($addInfo){
    //         return response() ->json([
    //             'success'   => true,
    //             'data'      => $addInfo,
    //             'message'   => 'Successfully made a booking'
    //                 ],200);
    //     }else{
    //         return response()->json([
    //             'response'      => false,
    //             'message'       => "Failed",
    //             'data'          =>  []
    //             ], 200);
    //     }
        
    // }

    public function generaterefnumber($date){
   
        $dataDate;
        $dataTime;

        $dateParts = explode(" ", $date);

        $dataDate = str_replace("-", "", $dateParts[0]);
        $dataTime = str_replace(":", "", $dateParts[1]);
        

        return $referencenumber = $dataDate.$dataTime;

    }

    public function checkifrefexists($referenceNumber){
        $checkexists = booking::where('reference_number', $referenceNumber)->get();
        if($checkexists){
            $regenerate = $this->generaterefnumber(date("Y-m-d H:i:s"));
            $this->checkifrefexists($regenerate);
            return $referenceNumber;
        }
    } 

    public function addRefNum (Request $request){

        $referenceNumber = $this->generaterefnumber(date('Y-m-d H:i:s'));

        $query = booking::addref($request, $referenceNumber);
        
        if($query){
            return response()->json([
                'success'   => true,
                'data'      => $query,
            ],200);
        }else{
            return response()->json([
                'success'   =>false,
                'data'      =>[]
            ],200);
        }
    }

    public function bookingInfoSave(Request $request){
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

        $query = booking::bookingInfo($request);
        

        if($query){
            return response()->json([
                'success'   => true,
                'data'      => $query,
            ],200);
        }else{
            return response()->json([
                'success'   =>false,
                'data'      =>[]
            ],200);
        }
    }

    public function clientInfoSave(Request $request){
        $validation = Validator::make($request->all(), [
            'game_id'               =>  'required|unique:booking_table,reference_number,id',
            'fname'                 =>  'required|string',
            'lname'                 =>  'required|string',
            'mobile_number'         =>  'required|string',
            'verification_number'   =>  'required|string',
            'email'                 =>  'required|string',
            
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
                
            ],200);
            return response() ->json([
                'success'   => false,
                'message'   => 'There is something wrong',
                
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

        $query = client::getInformationClient($request);

        if(sizeof($query) > 0){
            return response()   ->json([
                'response'  =>  true,
                'message'   =>  'success',
                'data'      =>  $query
            ],  200);
        }else{
            return response()   ->json([
                'response'  =>  false,
                'message'   =>  'there is a missing data',
                'data'      =>  []
            ],200);
        }
    }
}
