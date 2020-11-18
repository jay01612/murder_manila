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
        
        $verification = rand();
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
   
}




