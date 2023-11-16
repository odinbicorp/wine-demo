<?php

namespace Tests\Browser\Pages;

use App\Models\Review;
use App\Models\Wine;
use Illuminate\Support\Facades\Log;
use Laravel\Dusk\Browser;
use Tests\Browser\Helpers\WineHelper;

class ProcessUpdatePage
{
    public  static function extractElementInfo(Browser $browser, $selector)
    {

        $elmText = '';

        if (count($browser->elements($selector)) > 0) {
            $elmText = $browser->text($selector);
        }

        return $elmText;
    }

    public static function getTextFromElements($browser, $selector, $index)
    {
        $elements = $browser->elements($selector);

        if (count($elements) > $index) {
            $element = $elements[$index];
            return $element->getText();
        }

        return '';
    }

    public static function getTextFromElements2($browser, $index)
    {
        // Assuming the selector for the container is '.card-body.text-link-b'
        $containerSelector = '.card-body.text-link-b';
        $itemSelector = '.info-card__item';

        $elements = $browser->elements($containerSelector . ' ' . $itemSelector);

        if (count($elements) > $index) {

            $element = $elements[$index];
            // Get the text within the specified div excluding elements with class 'small'
            $innerText = $browser->text($containerSelector . ' ' . $itemSelector . ':nth-child(' . ($index + 1) . ') .pt-2:not(.small)');

            return $innerText;
        }

        return '';
    }

    public static function processHandle($browser,$wines)
    {
        if ($wines->count() > 0) {

            $browser->visit('https://www.wine-searcher.com/');

            foreach ($wines as $index => $wine) {

                dump($wine->id);

                Wine::updateLog($wine->id,null);

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
                    $findTabInfo = $browser->elements('#find-tab-info');

                    if (!$findTabInfo){

                        Wine::updateLog($id,"No result found");
                        $browser->visit('https://www.wine-searcher.com/');
                        $browser->pause(1200);

                        continue;
                    }

                    $browser->click('#find-tab-info');

                    $browser->pause(500);


                    $result = WineHelper::getWineProfile($browser);
                    $alcohol = $result['alcohol'];

                    $wineName = $browser->waitFor('.product-details__container-right', 60)
                        ->text('.product-details__container-right > li > h1');
                    $region =self::extractElementInfo($browser, '.product-details__region-name > span');
                    $type = self::extractElementInfo($browser, '.product-details__styles > span');
                    $rating = self::extractElementInfo($browser, '.ml-2A > span');
                    $score = self::extractElementInfo($browser, '.product-details__score > span');

                    $content = self::extractElementInfo($browser, '.product-details__description > p');
                    $contentDetail = '';
                    $contentDetailElm = $browser->elements('.js-show-hide.d-block.text-decoration-none.btn-link-primary.smaller.pl-1A.collapsed.cursor-pointer');

                    if ($contentDetailElm) {
                        $browser->click('.js-show-hide.d-block.text-decoration-none.btn-link-primary.smaller.pl-1A.collapsed.cursor-pointer');
                        $contentDetail = $browser->text('.product-details__description .productDesc');
                    }


//                    Wine::updateNewAttr($id, $wineName, $region, $type, $rating, $score, $sweetness, $content,
//                        $contentDetail, $blend, $maturation, $oakType, $closureType, $vineyardNotes,
//                        $wineMaking, $ageing, $alcoholABV);

                    $browser->waitFor('#find-tab-reviews', 10)->click('#find-tab-reviews');
                    $browser->pause(500);

                    $infoCardItems = $browser->elements('.text-primary.info-card__item-link-text.font-weight-bold');

                    if (count($infoCardItems) > 0) {

                        $moreCritics = $browser->elements('a[href="#moreCritics"]');

                        if ($moreCritics) {
                            $browser->driver->executeScript("window.scrollTo(0, window.innerHeight );");
                            $browser->pause(1000);
                            $browser->click('a[href="#moreCritics"] .show-more');

                        }

                        foreach ($infoCardItems as $index => $infoCardItem) {
                            dump("Loading.... {$index}");
                            $user = self::getTextFromElements($browser, '.text-primary.info-card__item-link-text.font-weight-bold', $index);
                            $ratingScore = self::getTextFromElements($browser, '.btn.d-inline-flex.align-self-center.info-card__critic-score.mr-3', $index);
                            $ratingDate = self::getTextFromElements($browser, '.text-muted.pr-3', $index);
                            $ratingContent = self::getTextFromElements2($browser, $index );

                            dump("User {$index} : {$user}");
                            dump("Score {$index} : {$ratingScore}");
                            dump("Date {$index} : {$ratingDate}");
                            dump("Content {$index} : {$ratingContent}");

                            $review = new Review();
                            $review->user = $user;
                            $review->content = $ratingContent;
                            $review->date = $ratingDate;
                            $review->score = $ratingScore;
                            $review->wine_id = $id;
                            $review->save();

                        }

                        $browser->pause(60000);

                    }

                    $browser->visit('https://www.wine-searcher.com/');

                    $browser->pause(1200);

                    dump("DONE: " . $id);

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
}
