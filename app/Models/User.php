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

    public static function addAdmin($data, $password){
        return User::insert([
            'fname'         =>  $data->fname,
            'lname'         =>  $data->lname,
            'username'      =>  $data->username,
            'password'      =>  $password,
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
            'is_active' =>  0
        ]);
    }

    public static function getPendingBookings($data){
        return $query = DB::connection('mysql')
        ->table('payment_table as payment')
        ->select(
            'payment.id as id',

            'payment.reference_id as reference id',

            DB::raw("CONCAT(client.lname,',',client.fname) as name"),
            'client.email as email',

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
        ->where('is_paid', 0)
        ->get();

    }

    public static function PaidBookings($data){
        return $query = DB::connection('mysql')
        ->table('payment_table as payment')
        ->select(
            'payment.id as id',

            'payment.reference_id as reference id',

            DB::raw("CONCAT(client.lname,',',client.fname) as name"),
            'client.email as email',

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
        ->where('is_paid', 1)
        ->get();
    }

    public static function cancelBookings($data){
        return $query = DB::connection('mysql')
        ->table('payment_table as payment')
        ->select(
            'payment.id as id',

            'payment.reference_id as reference id',

            DB::raw("CONCAT(client.lname,',',client.fname) as name"),
            'client.email as email',

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
        ->where('is_cancelled', 1)
        ->get();
    }

   

}
