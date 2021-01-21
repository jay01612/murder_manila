<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use DB;
use Hash;

class User extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable, softDeletes;

    protected $table = 'users';

    protected $fillable = [
        'fname',
        'lname',
        'username',
        'password',
        'email',
        'position_id',
        'is_active',
        'created_by',
        'updated_by',  
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    public static function getPositionId($data){
        return $query = DB::connection('mysql')
        ->table ('access_levels')
        ->select(
            'access_levels.id',
            'access_levels.access_name'
        )->get();
        
    }


    public static function addAdmin($data){
        return User::insert([
            'fname'         =>  $data->fname,
            'lname'         =>  $data->lname,
            'username'      =>  $data->username,
            'password'      =>  'bycrypt'($data->password),
            'email'         =>  $data->email,
            'position_id'   =>  $data->position_id,
            'created_at'    =>  DB::raw("NOW()")
        ]);
    }

    public static function getAdmins($data){
        return $query = DB::connection('mysql')
        ->table('users')
        ->select(
            'users.id',
            'users.fname',
            'users.lname',
            'users.username',
            'users.password',
            'users.email',

            'access.access_name'
        )
        ->join('access_levels as access', 'users.position_id', '=', 'access.id')
        ->get();
    }

    public static function DeleteAdmin($data){
        return User::where('id', $data->id)
        ->update([
            'is_active'     =>  0,
        ]);
    }

    public static function getPendingBookings($data){
        return $query = DB::connection('mysql')
        ->table('booking_table as booking')
        ->select(
            'booking.id as id',

            'booking.reference_number as Reference_Number',

            DB::raw("CONCAT(booking.fname,' ',booking.lname) as Name"),
            'booking.email as email',

            'theme.name as game',
          
            DB::raw("DATE_FORMAT(booking.book_date, '%M %d %Y') as date"),
            DB::raw("TIME_FORMAT(booking.book_time, '%h:%i %p') as time"),
            DB::raw("TIME_FORMAT(booking.end_time, '%h:%i %p') as end_time"),
            DB::raw("DATE_FORMAT(booking.expiration_date, '%M %d %Y') as expiration_date"),
            'booking.venue as venue',
            'booking.maxpax as maxpax',

            'booking.initial_payment as Downpayment',
            'booking.total_amount as Total_Amount',
            'booking.is_paid as is_paid'

        )
        ->leftjoin('themes as theme', 'booking.theme_id', '=', 'theme.id')
        ->where('booking.is_booked', '=', 0)
        ->where('booking.is_cancelled', '=', 0)
        ->get();

    }

    public static function PaidBookings($data){
        return $query = DB::connection('mysql')
        ->table('booking_table as booking')
        ->select(
            'booking.id as id',

            'booking.reference_number as Reference_Number',

            DB::raw("CONCAT(booking.fname,' ',booking.lname) as Name"),
            'booking.email as email',

            'theme.name as game',
          
            DB::raw("DATE_FORMAT(booking.book_date, '%M %d %Y') as date"),
            DB::raw("TIME_FORMAT(booking.book_time, '%h:%i %p') as time"),
            DB::raw("DATE_FORMAT(booking.expiration_date, '%M %d %Y') as expiration_date"),
            'booking.venue as venue',
            'booking.maxpax as maxpax',

            'booking.initial_payment as Downpayment',
            'booking.total_amount as Total_Amount',
            'booking.is_paid as is_paid'

        )
        ->leftjoin('themes as theme', 'booking.theme_id', '=', 'theme.id')
        ->where('booking.is_booked', '=', 1)
        ->get();
    }

    public static function cancelBookings($data){
        return $query = DB::connection('mysql')
        
        ->table('booking_table as booking')
        ->select(
            'booking.id as id',

            'booking.reference_number as Reference_Number',

            DB::raw("CONCAT(booking.fname,' ',booking.lname) as Name"),
            'booking.email as email',

            'theme.name as game',
          
            DB::raw("DATE_FORMAT(booking.book_date, '%M %d %Y') as date"),
            DB::raw("TIME_FORMAT(booking.book_time, '%h:%i %p') as time"),
            DB::raw("DATE_FORMAT(booking.expiration_date, '%M %d %Y') as expiration_date"),
            'booking.venue as venue',
            'booking.maxpax as maxpax',

            'booking.initial_payment as Downpayment',
            'booking.total_amount as Total_Amount',

        )
        ->leftjoin('themes as theme', 'booking.theme_id', '=', 'theme.id')
        ->where('booking.is_cancelled', '=', 1)
        ->get();
    }
}
