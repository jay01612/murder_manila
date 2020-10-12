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
        $verifiacation = rand();
        return $query = DB::connection('mysql')
        ->table('client_info')
        ->insertGetId([
            'game_id'               =>  $data->refId,
            'fname'                 =>  $data->firstname,
            'lname'                 =>  $data->lastname,
            'mobile_number'         =>  $data->mobilenumber,
            'verification_number'   =>  $verification,
            'email'                 =>  $data->email,
            'is_emailed'            =>  0,
            'is_verified'           =>  0,
            'created_at'            =>  DB::raw("NOW()")
        ]);
    }

    public static function getInformationClient ($data){
        
        $getInformation = where('game_id', $gameId)->get();

        $data = [];

        foreach($getInformation as $out){
            $data[] = [
                'booking'   =>  DB::connection('mysql')
                            ->  table('booking_table')
                            ->  where('id', $out->id)
                            ->  get(),
                
                'client'    =>  DB::connection('mysql')
                            ->  table('client_info')
                            ->  where('id', $out->id)
                            ->  get()

            ];

            return $data;
        }
    }
}




