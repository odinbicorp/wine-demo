<?php

namespace Tests\Browser\Pages;

use App\Models\Review;
use App\Models\Wine;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Tests\Browser\Helpers\WineHelper;

class WineReivewProcess
{
    public static function processHandle($browser,$wines)
    {
        if ($wines->count() > 0) {

            $browser->visit('https://www.wine-searcher.com/');

            foreach ($wines as $index => $wine) {

                Wine::updateLog($wine->id,null);

                try {

                    $id = $wine->id;
                    $originName = $wine->origin_name;
                    $captChar = $browser->elements('.px-captcha-message');

                    //$cokieName = $browser->driver->manage()->getCookies();

                    if ($captChar) {

                        $browser->driver->manage()->deleteAllCookies();
                        $browser->visit('https://www.wine-searcher.com/');
                        dump("Đã giải Captcha");
                    }

                    $cokieELm = $browser->elements('.btn.btn-primary.smaller.float-right.cookie-accept');

                    if ($cokieELm){
                        $cokieELm[0]->click();
                    }

                    $browser->waitFor('[name="Xwinename"]', 15);
                    //$browser->waitFor('#Xwinename', 10);

                    $browser->keys('#Xwinename', ['{CONTROL}', 'a']);

                    $browser->keys('#Xwinename', ['{delete}']);

                    $browser->waitFor('[name="Xwinename"]', 5)->typeSlowly('Xwinename', $originName, 1)
                        ->waitFor('.tt-suggestion', 10)
                        ->pause(1000);

                    $browser->elements('.tt-suggestion')[0]->click();

                    //$browser->waitFor('#find-tab-info', 5);
                    $browser->pause(1000);

                    $findTabInfo = $browser->elements('#find-tab-info');

                    if (!$findTabInfo){

                        Wine::updateLog($id,"No result found");
                        $browser->visit('https://www.wine-searcher.com/');
                        $browser->pause(1200);
                        continue;
                    }

                    $review = Review::where('wine_id',$id)->first();

                    if (!$review){

                        $browser->waitFor('#find-tab-reviews', 10)->click('#find-tab-reviews');
                        $browser->pause(500);

                        $infoCardItems = $browser->elements('.card-body.text-link-b .info-card__item:not(.small)');

                        if (count($infoCardItems) > 0) {

                            $moreCritics = $browser->elements('a[href="#moreCritics"]');

                            if ($moreCritics) {
                                $browser->script("window.scrollTo(0, window.innerHeight / 0.75);");
                                //$browser->script("document.querySelector('.card.info-card.info_card__critic
                                //.rounded-0.corner-sm a[href=\"#moreCritics\"] .show-more').scrollIntoView();");
                                $browser->pause(1000);
                                try {
                                    $browser->click('.card.info-card.info_card__critic.rounded-0.corner-sm a[href="#moreCritics"] .show-more');
                                }catch (\Exception $e){
                                    $browser->script("window.scrollTo(0, window.innerHeight / 0.75);");
                                    $browser->click('.card.info-card.info_card__critic.rounded-0.corner-sm a[href="#moreCritics"] .show-more');
                                }

                            }

                            foreach ($infoCardItems as $index => $infoCardItem) {

                                $user = WineHelper::getTextFromElements($browser, '.text-primary.info-card__item-link-text.font-weight-bold', $index);
                                $ratingScore = WineHelper::getTextFromElements($browser, '.btn.d-inline-flex.align-self-center.info-card__critic-score.mr-3', $index);
                                $ratingDate = WineHelper::getTextFromCssSelector($infoCardItem,'.small.pt-2 .text-muted.pr-3');
                                $ratingContent = WineHelper::getTextFromCssSelector($infoCardItem,'.pt-2:not(.small)' );

                                $review = new Review();
                                $review->user = $user;
                                $review->content = $ratingContent;
                                $review->date = $ratingDate;
                                $review->score = $ratingScore;
                                $review->wine_id = $id;
                                $review->save();

                            }

                        }
                    }

                    $browser->driver->manage()->deleteAllCookies();
                    $browser->visit('https://www.wine-searcher.com/');

                    Wine::updateLog($id, "DONE");
                    $browser->pause(1200);

                } catch (\Exception $e) {

                    Log::error($e->getMessage());
                    dump($e->getMessage());
                    Wine::updateLog($id, $e->getMessage());
                    $browser->visit('https://www.wine-searcher.com/');
                    continue;
                }
            }

            dump("Completed");
        }
    }

}
