<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wine extends Model
{
    use HasFactory;
    protected $fillable = ['origin_name','new_name'];

    public static function getEmptyNewName()
    {
        return self::whereNull('new_name')->get();
    }

    public static function updateNewName($id, $name)
    {
        $attributes = ['new_name' => $name];

//        if (!is_null($status)) {
//            $attributes['TrangThai'] = $status;
//        }
//
//        if (!is_null($faill)) {
//            $attributes['MaLoi'] = $faill;
//        }

        self::where('id', $id)->update($attributes);
    }
}
