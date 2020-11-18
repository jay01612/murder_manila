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
            'client.game_id as gameId',
            'client.fname as firstname',
            'client.lname as lastname',
            'client.mobile_number as mobileNumber',
            'client.email as e-mail',
            
            
            DB::raw("DATE_FORMAT(booking.book_date, '%M %d %Y') as date"),
            DB::raw("TIME_FORMAT(booking.book_time, '%h:%i %p') as time"),
           
            'theme.name as game',

            'discount.discount_code as code',

            'booking.maxpax as maxpax',
            'booking.venue as venue',  
        )
        ->join('themes as theme', 'booking.theme_id', '=', 'theme.id')
        ->leftjoin('discounts as discount', 'booking.discount_id', '=', 'discount.id')
        ->join('client_info as client', 'booking.id', '=', 'client.game_id')
        ->where('client.id', '=', $data->id)
        ->get();
    }

    public static function clientBookingAmount ($data){

        return $query = DB::connection('mysql')
        ->table('payment_table')
        ->insertGetId([
            'reference_id'      => $data->reference_id,
            'client_id'         => $data->client_id,
            'is_paid'           => 0,
            'paid_time'         => $data->paid_time,
            'paid_date'         => $data->paid_date,
            'amount'            => $data->amount,
            'is_emailed'        => 0,
            'created_at'        => DB::raw("NOW()")
        ]);
    }

    public static function bookingSummaryWithAmount($data){

        return $query = DB::connection('mysql')
        ->table('payment_table as payment')
        ->select(
            'payment.id as id',

            'payment.reference_id as booking id',

            DB::raw("CONCAT(client.lname,',',client.fname) as name"),
            'client.email as email',
            'client.mobile_number as mobile number',

            'theme.name as game',

            DB::raw("DATE_FORMAT(booking.book_date, '%M %d %Y') as date"),
            DB::raw("TIME_FORMAT(booking.book_time, '%h:%i %p') as time"),
            'booking.venue as venue',
            'booking.maxpax as number of players',

            'payment.amount as total amount'
        )
        ->join('client_info as client', 'payment.client_id', '=', 'client.id')
        ->join('booking_table as booking', 'payment.reference_id', '=', 'booking.id')
        ->join('themes as theme', 'booking.theme_id', '=', 'theme.id')
        ->where('payment.id', '=', $data->id)
        ->get();
    }

    public static function editBooking($data){
        return booking::where('id', $data->id)
        ->update([
            'is_booked' => 1
        ]);
        
    } 
}
