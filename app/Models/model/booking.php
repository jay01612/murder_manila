<?php

namespace App\Models\model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class booking extends Model
{
    use HasFactory;

    protected $table = 'booking_table';

    protected $fillable =[
        'reference_number',
        'book_date',
        'book_time',
        'theme_id',
        'discount_id',
        'pax',
        'venue'
    ];

    public static function getBooking(){
        return booking_table::get();
    }

    public static function bookingInfo($data, $referenceNumber){
        return $query = DB::connection('mysql')
        ->table('booking_table')
        ->insertGetId([
            'reference_number'  =>  $referenceNumber,
            'book_date'         =>  $data->book_date,
            'book_time'         =>  $data->book_time,
            'theme_id'          =>  $data->theme_id,
            'discount_id'       =>  $data->discount_id,
            'maxpax'            =>  $data->maxpax,
            'venue'             =>  $data->venue    
        ]);  
    }
    

    public static function checkAvailableBooking($data){
        return $query = DB::connection('mysql')
        ->table('booking_table')
        ->select('*')
        ->where('book_date', $data->book_date)
        ->where('book_time', $data->book_time)
        ->get()->first();
    }

    public static function ClientBookingSummary ($data){
       
        return $query = DB::connection('mysql')
        ->table('booking_table as booking')
        ->select(

            
            'booking.reference_number as referenceNumber',
            
            'client.fname as firstname',
            'client.lname as lastname',
            'client.mobile_number as mobileNumber',
            'client.email as e-mail',
            
            
            'booking.book_date as date',
            'booking.book_time as time',
           
            'theme.name as game',

            'discount.discount_code as code',

            'booking.maxpax as maxpax',
            'booking.venue as venue',  
        )
        ->join('themes as theme', 'booking.theme_id', '=', 'theme.id')
        ->leftjoin('discounts as discount', 'booking.discount_id', '=', 'discount.id')
        ->join('client_info as client', 'client.game_id', '=', 'booking.id')
        ->where('client.id', '=', $data->id)
        ->get();
    }
}
