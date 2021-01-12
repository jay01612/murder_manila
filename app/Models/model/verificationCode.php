<?php

namespace App\Models\model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class verificationCode extends Model
{
    use HasFactory;

    protected $table = "verification_codes";

    protected $fillable = [
        'id',
        'verificationCode',
        'is_active',
        'expiry_date',
        'created_at',
        'updated_at',
    ];

   


}
