<?php

namespace Tests\Browser\Pages;

use App\Models\Review;
use App\Models\Wine;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Tests\Browser\Helpers\WineHelper;

class ReviewTranslation
{
    public static function processHandle($browser,$reviews)
    {
        if ($reviews->count() > 0) {

            $browser->visit('https://translate.google.com/?hl=vi');

            foreach ($reviews as $index => $review) {

                $id = $review->id;
                $content= $review->content;
                Review::updateLog($id,1);

                try {

                    $contentElm = $browser->elements('textarea[jsname="BJE2fc"]');

                    if ($contentElm){

                        $browser->clear('textarea[jsname="BJE2fc"]')->type('textarea[jsname="BJE2fc"]', $content);
                        $browser->pause(1500);
                        $result = '';

                        while (Str::length($result) == 0){
                            $result = $browser->waitFor('span[jsname="W297wb"]')->text('span[jsname="W297wb"]');
                        }

                        $fillableAttributes = [
                            'content_vi' => $result,
                            'logs' => 'DONE',
                        ];

                        Review::reviewUpdate($id,$fillableAttributes);
                        $browser->pause(1000);
                        $browser->click('.VfPpkd-Bz112c-LgbsSe.VfPpkd-Bz112c-LgbsSe-OWXEXe-e5LLRc-SxQuSe.yHy1rc.eT1oJ.mN1ivc.ZihNHd.GA2I6e');
                        $browser->pause(1000);

                    }else{
                        Review::updateLog($id, "Failled");
                    }

                    $browser->pause(1200);


                } catch (\Exception $e) {

                    Log::error($e->getMessage());
                    dump($e->getMessage());
                    Review::updateLog($id, $e->getMessage());
                    $browser->visit('https://translate.google.com/?hl=vi');
                    continue;
                }
            }

            dump("Completed");
        }
    }

}
