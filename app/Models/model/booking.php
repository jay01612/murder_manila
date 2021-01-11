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
        'theme_id',
        'maxpax',
        'venue',
        'fname',
        'lname',
        'mobile_number',
        'email',
        'verification_number',
        'initial_payment',
        'total_amount'
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
        //->where('booking.id', '=', $data->id)
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

            'payment.initial_payment as initial payment',
            'payment.amount as total amount'
        )
       
        ->leftjoin('booking_table as booking', 'payment.reference_id', '=', 'booking.id')
        ->leftjoin('themes as theme', 'booking.theme_id', '=', 'theme.id')
        ->leftjoin('client_info as client', 'payment.client_id', '=', 'client.id')
        ->where('client.id', '=', $data->id)
        ->get();
    }

    public static function editBooking($data){
        return booking::where('id', $data->id)
        ->update([
            'is_booked' => 1
        ]);
        
    }

    public static function editToCancelBooking($data){
        return booking::where('id', $data->id)
        ->update([
            'is_cancelled' => 1
        ]);
    }
}
