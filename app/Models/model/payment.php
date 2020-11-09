<?php

namespace App\Models\model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class payment extends Model
{
    use HasFactory;

    protected $table = 'payment_table';

    public static function getPayment(){
        return payment_table::get();
    }

    public function editPayment($data){
        return payment::where('id', $data->id)
        ->update([
            'is_paid' => 1
        ]);
    }
}
