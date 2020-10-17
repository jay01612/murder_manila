<?php

namespace App\Models\model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use DB;
use Hash;

class admin extends Model
{
    use HasFactory, HasApiTokens, Notifiable, softDeletes;

    protected $table = 'admin_table';

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

    public static function getPositionId($data){
        return $query = DB::connection('mysql')
        ->table ('access_levels')
        ->select(
            'access_levels.id',
            'access_levels.access_name'
        )->get();
        
    }

    public static function addAdmin($data){
        $password = str_random($lenght);
        return admin::insert([
            'fname'         =>  $data->fname,
            'lname'         =>  $data->lname,
            'username'      =>  $data->username,
            'password'      =>  Hash::make($password),
            'email'         =>  $data->email,
            'position_id'   =>  $data->position_id
            
        ]);
    }


}
