<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $table = 'review_crawls';
    protected $fillable = ['user','content','content_vi','date','score','wine_id','logs'];

    public function scopeUnfinished($query)
    {
        return $query->whereNotNull('content')
            ->where('content','<>','')
            ->get();
    }

    public function scopeUnfinishedBetween($query,$from,$to)
    {
        return $query->whereNotNull('content')
            ->where('logs','<>','DONE')
            ->where('content','<>','')
            ->whereBetween('id',[$from,$to])
            ->get();
    }

    public static function updateLog($id, $logs)
    {
        $attributes = [
            'logs' => $logs
        ];

        self::where('id', $id)->update($attributes);
    }

    public static function reviewUpdate(int $id, array $attributes)
    {
        self::find($id)->update($attributes);
    }


}
