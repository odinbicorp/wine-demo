<?php

namespace Tests\Browser\Pages;

use App\Models\Review;
use App\Models\Wine;
use Illuminate\Support\Facades\Log;
use Tests\Browser\Helpers\WineHelper;

class ProcessUpdatePage
{
    public static function processHandle($browser,$wines)
    {
        if ($wines->count() > 0) {

            $browser->visit('https://www.wine-searcher.com/');

            foreach ($wines as $index => $wine) {

                //Wine::updateLog($wine->id,null);

                try {

                    $id = $wine->id;
                    $originName = $wine->origin_name;

                    dump("Origin name: " . $originName);

                    $captChar = $browser->elements('.px-captcha-message');

                    //$cokieName = $browser->driver->manage()->getCookies();
                    //dump($cokieName);

                    if ($captChar) {
                        dump("Ok");
                        $browser->visit('https://www.wine-searcher.com/');
                        //$browser->refresh();
                        $browser->driver->manage()->deleteAllCookies();
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

                    $currency = '';
                    $ratingTotal = WineHelper::extractElementInfo($browser, '.ml-2A > span.font-light-bold:nth-child(2)');
                    $reviewTotal = WineHelper::extractElementInfo($browser, '.product-details__score  .font-light-bold');
                    $price = WineHelper::extractElementInfo($browser, '.price.text-nowrap  .font-light-bold');
                    $volumn = WineHelper::extractElementInfo($browser, '.mb-2A.pl-0.card.product-details__avg-price-global .p-2 .small');

                    dump("Rating {$ratingTotal}");
                    dump("Review {$reviewTotal}");
                    dump("Price {$price}");
                    dump("Volumn {$volumn}");

                    if ($volumn){
                        $volumn = str_replace("/","",$volumn);
                        $volumn = str_replace(" ","",$volumn);
                    }

                    if ($price){

                        $price = str_replace(",","",$price);

                        if ($browser->assertSee('₫', '.price.text-nowrap')) {
                            $currency = 'VND';
                        }else{
                            $currency = 'USD';
                        }
                    }

                    $findTabInfo = $browser->elements('#find-tab-info');

                    if (!$findTabInfo){

                        Wine::updateLog($id,"No result found");
                        $browser->visit('https://www.wine-searcher.com/');
                        $browser->pause(1200);

                        continue;
                    }

                    $browser->click('#find-tab-info');
                    $browser->pause(500);

                    $grape = WineHelper::extractElementInfo($browser, '.font-light-bold.text-primary.info-card__item-link-text.text-underline');

                    $fillableAttributes = [
                        'volume' => $volumn,
                        'review_total' => $reviewTotal,
                        'rating_total' => $ratingTotal,
                        'grape' => $grape,
                        'price' => $price,
                        'currency' => $currency
                    ];

                    Wine::wineUpdate($id,$fillableAttributes);

                    $review = Review::where('wine_id',$id)->first();

                    if (!$review){

                        $browser->waitFor('#find-tab-reviews', 10)->click('#find-tab-reviews');
                        $browser->pause(500);

                        $infoCardItems = $browser->elements('.card-body.text-link-b .info-card__item:not(.small)');

                        if (count($infoCardItems) > 0) {

                            $moreCritics = $browser->elements('a[href="#moreCritics"]');

                            if ($moreCritics) {
                                $browser->driver->executeScript("window.scrollTo(0, window.innerHeight );");
                                $browser->pause(1000);
                                $browser->click('a[href="#moreCritics"] .show-more');

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

                    $browser->visit('https://www.wine-searcher.com/');
                    Wine::updateLog($id, "DONE");
                    $browser->pause(1200);

                } catch (\Exception $e) {

                    Log::error($e->getMessage());
                    dump($e->getMessage());
                    //$browser->pause(60000);
                    Wine::updateLog($id, $e->getMessage());
                    $browser->visit('https://www.wine-searcher.com/');
                    continue;
                }
            }

            dump("Completed");
        }
    }

    public static function updateWineLog($wines)
    {

        if ($wines->count() > 0) {

            foreach ($wines as $index => $wine) {
                dump($wine->origin_name);
                $id = $wine->id;
                $wine->update([
                    'logs'=>$id
                ]);
            }
        }

    }
}
