<?php

namespace App\Models\model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class payment extends Model
{
    use HasFactory;

    protected $table = 'payment_table';

    protected $fillable = [
        'id',
        'reference_id',
        'client_id',
        'is_paid',
        'paid_time',
        'paid_date',
        'initial_payment',
        'amount',
    
    ];

    public static function getPayment(){
        return payment_table::get();
    }

    public static function InitialPayment($data){
        return $query = DB::connection ('mysql')
        ->table('payment_table')
        ->insertGetId([
            'reference_id'          =>$data->reference_id,
            'client_id'             =>$data->client_id,
            'paid_time'             =>DB::raw("NOW()"),
            'paid_date'             =>DB::raw("NOW()"),
            'initial_payment'       =>$data->initial_payment,
            'amount'                =>$data->$amount,
        ]);
    }

    public function editPayment($data){
        return payment::where('id', $data->id)
        ->update([
            'is_paid' => 1
        ]);
    }
}
