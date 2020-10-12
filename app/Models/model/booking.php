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

    public static function addref($data, $referenceNumber){
         return $query = DB::connection('mysql')
        ->table('booking_table')
        ->insert([
            'reference_number'  =>  $referenceNumber,    
        ]);  
    }

    public static function bookingInfo($data){
        return $query = DB::connection('mysql')
        ->table('booking_table')
        ->insertGetId([
            'reference_number'  =>  $data->referenceNumber,
            'book_date'         =>  $data->date,
            'book_time'         =>  $data->time,
            'theme_id'          =>  $data->theme,
            'discount_id'       =>  $data->discount,
            'maxpax'            =>  $data->pax,
            'venue'             =>  $data->venue    
        ]);  
    }
    

    public static function checkAvailableBooking($data){
        return $query = DB::connection('mysql')
        ->table('booking_table')
        ->select('*')
        ->where('book_date', $data->date)
        ->where('book_time', $data->time)
        ->where('theme_id', $data->theme)
        ->get()->first();
    }
   
}
