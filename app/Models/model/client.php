<?php

namespace App\Models\model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class client extends Model
{
    use HasFactory;

    protected $table = 'client_table';

    protected $fillable = [
        'game_id',
        'fname',
        'lname',
        'mobile_number',
        'verification_number',
        'email',
        'created_at'
    ];

    public static function saveClientInfo ($data){
        
        $verification = rand(1000, 9999);
        return $query = DB::connection('mysql')
        ->table('client_info')
        ->insertGetId([
            'game_id'               =>  $data->game_id,
            'fname'                 =>  $data->fname,
            'lname'                 =>  $data->lname,
            'mobile_number'         =>  $data->mobile_number,
            'verification_number'   =>  $verification,
            'email'                 =>  $data->email,
            'is_emailed'            =>  0,
            'is_verified'           =>  0,
            'created_at'            =>  DB::raw("NOW()")
        ]);
    }

    public static function getVerificationCode($data){
        return $query = DB::connection('mysql')
        ->table('client_info')
        ->select('*')
        ->where('id', $data)
        ->get()->first();
    }
   
    public static function verifyClient($data){

        return $query = DB::connection('mysql')
        ->table('client_info')
        ->where('id', $data->id)
        ->where('verification_number', $data->verification_number)
        ->where('is_verified', 0)
        ->get();
    }

    public static function updateVerified($data){
        return $query = DB::connectioin('mysql')
        ->table('client_info')
        ->where('id', $data->id)
        ->update([
            'is_verified' => 1
        ]);
    }
}




