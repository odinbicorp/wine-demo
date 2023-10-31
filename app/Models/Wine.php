<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Review;

class Wine extends Model
{
    use HasFactory;
    protected $fillable = ['origin_name','new_name'];

    public static function getEmptyNewName()
    {
        return self::whereNull('new_name')->get();
    }

    public static function updateNewAttr($id, $name, $region, $type, $rating, $score, $sweetness, $content,
                                         $contentDetail, $blend, $maturation, $oakType, $closureType, $vineyardNote,
                                         $wineMaking, $ageing)
    {
        $attributes = [
            'new_name' => $name,
            'region' => $region,
            'type' => $type,
            'rating' => $rating,
            'score' => $score,
            'sweetness' => $sweetness,
            'content' => $content,
            'content_detail' => $contentDetail,
            'maturation' => $maturation,
            'oak_type' => $oakType,
            'closure_type' => $closureType,
            'vineyard_note' => $vineyardNote,
            'wine_making' => $wineMaking,
            'ageing' => $ageing

        ];

        self::where('id', $id)->update($attributes);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
