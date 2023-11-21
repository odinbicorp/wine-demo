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
        'logs',
        'volume',
        'review_total',
        'rating_total',
        'grape',
        'price',
        'currency',
        'price_logs'
    ];

    public static function getWineByIsNullName($from,$to)
    {
        return self::whereNull('new_name')
            ->where('logs','<>','No result found')
            ->whereBetween('id', [$from, $to])
            ->get();
    }

    public static function getFirstValue($id)
    {
        return self::where('id',$id)->first();
    }

    public function scopeWithoutReviews($query)
    {
        return $query->whereNotIn('id', function ($subquery) {
            $subquery->select('wine_id')->from('review_crawls');
        });
    }

    public function scopeWithLogUnfinished($query)
    {
        return $query->where('logs','<>','DONE')->get();
    }

    public function scopeUnfinishedBetween($query,$from,$to)
    {
        return $query->whereNotIn('id', function ($subquery) {
                $subquery->select('wine_id')->from('review_crawls');
            })->whereBetween('id',[$from,$to])->get();
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

    public static function wineUpdate(int $id, array $attributes)
    {
        self::find($id)->update($attributes);
    }

    public static function updateLog($id, $logs)
    {
        $attributes = [
            'logs' => $logs
        ];

        self::where('id', $id)->update($attributes);
    }

    public static function updatePriceLog($id, $logs)
    {
        $attributes = [
            'price_logs' => $logs
        ];

        self::where('id', $id)->update($attributes);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
