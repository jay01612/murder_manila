<?php

namespace App\Models\model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DB;

class booking extends Model
{
    use HasFactory;

    protected $table = 'booking_table';

    protected $fillable =[
        'reference_number',
        'book_date',
        'end_date',
        'book_time',
        'end_time',
        'expiration_date',
        'theme_id',
        'maxpax',
        'venue',
        'fname',
        'lname',
        'mobile_number',
        'email',
        'initial_payment',
        'total_amount',
        'is_expired',
        'is_initial_paid',
        'is_fully_paid'

    ];

    public static function getBooking(){
        return booking_table::get();
    }

    public static function getVerificationCode ($data){
       
        return $query = DB::connection('mysql')
        ->table('booking_table as booking')
        ->select(
            
            'booking.reference_number as reference number',
            'booking.fname as firstname',
            'booking.lname as lastname',
            'booking.mobile_number as mobile number',
            'booking.email as email',
            'booking.verification_number as verification number',
            
            DB::raw("DATE_FORMAT(booking.book_date, '%M %d %Y') as date"),
            DB::raw("TIME_FORMAT(booking.book_time, '%h:%i %p') as time"),
           
            'theme.name as game',

            'booking.maxpax as maxpax',
            'booking.venue as venue',  
        )
        ->leftjoin('themes as theme', 'booking.theme_id', '=', 'theme.id')
        ->get();
    }

    public static function getInfo($data){
        return $query = DB::connection('mysql')
        ->table('booking_table as booking')
        ->select([
            'booking.id as id',

            'booking.reference_number as Reference Number',

            DB::raw("CONCAT(booking.lname,',',booking.fname) as Name"),
            'booking.email as email',

            'theme.name as game',
          
            DB::raw("DATE_FORMAT(booking.book_date, '%M %d %Y') as date"),
            DB::raw("TIME_FORMAT(booking.book_time, '%h:%i %p') as time"),
            'booking.venue as venue',
            'booking.maxpax as number of players',

            'booking.initial_payment as Downpayment',
            'booking.total_amount as Total Amount',

        ])
        ->leftjoin('themes as theme', 'booking.theme_id', '=', 'theme.id')
        ->where('booking.is_booked', '=', 0)
        ->get();
    }


    public static function sendVerificationCode($data){
        return $query = DB::connection('mysql')
        ->table('booking_table')
        ->select('*')
        ->where('id', $data)
        ->get();
    }


    public static function editBooking($data){
        return booking::where('id', $data->id)
        ->update([
            'is_booked'     =>  1,
            
        ]);
        
    }

    public static function editToCancelBooking($data){
        return booking::where('id', $data->id)
        ->update([
            'is_cancelled' => 1,
          
        ]);
    }

    public static function editPartialPaid($data){
        return booking::where('id', $data->id)
        ->update([
            'is_initial_paid' => 1,
          
        ]);
    }

    public static function editFullPaid($data){
        return booking::where('id', $data->id)
        ->update([
            'is_fully_paid' => 1,
          
        ]);
    }
}
