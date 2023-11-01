<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Review;

class Wine extends Model
{
    use HasFactory;
    protected $fillable = [
        'origin_name',
        'new_name',
        'region',
        'type',
        'rating',
        'score',
        'sweetness',
        'content',
        'content_detail',
        'blend',
        'maturation',
        'oak_type',
        'closure_type',
        'vineyard_note',
        'wine_making',
        'ageing',
        'alcohol',
        'logs'
    ];

    public static function getEmptyNewName()
    {
        return self::whereNull('new_name')
            ->whereBetween('id', [1, 5000])
            ->get();
    }


    public static function getEmptyNewNameMCSecond()
    {
        return self::whereNull('new_name')
            ->whereBetween('id', [5001, 10000])
            ->get();
    }

    public static function getEmptyNewNameMCThird()
    {
        return self::whereNull('new_name')
            ->whereBetween('id', [10001, 16000])
            ->get();
    }

    public static function getMcRestart()
    {
        return self::whereNull('new_name')
            ->whereNotNull('logs')
            ->get();
    }
    public static function updateNewAttr($id, $name, $region, $type, $rating, $score, $sweetness, $content,
                                         $contentDetail, $blend, $maturation, $oakType, $closureType, $vineyardNote,
                                         $wineMaking, $ageing,$alcohol)
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
            'blend' => $blend,
            'maturation' => $maturation,
            'oak_type' => $oakType,
            'closure_type' => $closureType,
            'vineyard_note' => $vineyardNote,
            'wine_making' => $wineMaking,
            'ageing' => $ageing,
            'alcohol' => $alcohol

        ];

        self::where('id', $id)->update($attributes);
    }

    public static function updateLog($id, $logs)
    {
        $attributes = [
            'logs' => $logs
        ];

        self::where('id', $id)->update($attributes);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
