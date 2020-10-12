<?php

namespace App\Models\model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class theme extends Model
{
    use HasFactory;

    protected $table = 'themes';

    public static function getThemes(){

        return themes::get();

    }

    public static function getThemeId($data){
        return $query = DB::connection('mysql')
        ->table ('themes')
        ->select(
            'themes.name'
        )->get();
        
    }
}
