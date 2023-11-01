<?php

namespace Tests\Browser;

use App\Models\Review;
use App\Models\Wine;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Log;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class SecondMC extends DuskTestCase
{
    /**
     * Get text from a Dusk element if it exists.
     *
     * @param \Laravel\Dusk\Browser $browser
     * @param string $selector
     * @return string
     */

    private function extractElementInfo(Browser $browser, $selector)
    {

        $elmText = '';

        if (count($browser->elements($selector)) > 0) {
            $elmText = $browser->text($selector);
        }

        return $elmText;
    }

    private function getTextFromElements($browser, $selector, $index)
    {

        $elements = $browser->elements($selector);

        if (count($elements) > $index) {
            return $elements[$index]->getText();
        }

        return '';
    }


    private function handSecondMc()
    {
        $wines = Wine::getEmptyNewNameMCSecond();

        $this->browse(function (Browser $browser) use ($wines) {

            if ($wines->count() > 0) {

                $browser->visit('https://www.wine-searcher.com/');

                foreach ($wines as $index => $wine) {

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

                        $browser->keys('#Xwinename', ['{CONTROL}', 'a']);

                        $browser->keys('#Xwinename', ['{delete}']);

                        $browser->waitFor('[name="Xwinename"]', 15)->typeSlowly('Xwinename', $originName, 1)
                            ->waitFor('.tt-suggestion', 15)
                            ->pause(1000);

                        $browser->elements('.tt-suggestion')[0]->click();

                        $browser->waitFor('#find-tab-info', 10)->click('#find-tab-info');
                        $browser->pause(1000);

                        $scriptAlcoholValue = <<<JS
                                var dtElements = document.querySelectorAll('.small.text-muted');
                                var alcoholValue = '';

                                for (var i = 0; i < dtElements.length; i++) {
                                    if (dtElements[i].textContent === 'Alcohol ABV') {
                                        var siblingDiv = dtElements[i].nextElementSibling.querySelector('.text-more-line.js-content');
                                        if (siblingDiv) {
                                            alcoholValue = siblingDiv.textContent;
                                            break;
                                        }
                                    }
                                }

                                return alcoholValue;
                            JS;

                        $scriptSweetness = <<<JS
                                var dtElements = document.querySelectorAll('.small.text-muted');
                                var alcoholValue = '';

                                for (var i = 0; i < dtElements.length; i++) {
                                    if (dtElements[i].textContent === 'Sweetness') {
                                        var siblingDiv = dtElements[i].nextElementSibling.querySelector('.text-more-line.js-content');
                                        if (siblingDiv) {
                                            alcoholValue = siblingDiv.textContent;
                                            break;
                                        }
                                    }
                                }

                                return alcoholValue;
                            JS;

                        $scriptBlend = <<<JS
                                var dtElements = document.querySelectorAll('.small.text-muted');
                                var alcoholValue = '';

                                for (var i = 0; i < dtElements.length; i++) {
                                    if (dtElements[i].textContent === 'Blend') {
                                        var siblingDiv = dtElements[i].nextElementSibling.querySelector('.text-more-line.js-content');
                                        if (siblingDiv) {
                                            alcoholValue = siblingDiv.textContent;
                                            break;
                                        }
                                    }
                                }

                                return alcoholValue;
                            JS;

                        $scriptMaturation = <<<JS
                                var dtElements = document.querySelectorAll('.small.text-muted');
                                var alcoholValue = '';

                                for (var i = 0; i < dtElements.length; i++) {
                                    if (dtElements[i].textContent === 'Maturation') {
                                        var siblingDiv = dtElements[i].nextElementSibling.querySelector('.text-more-line.js-content');
                                        if (siblingDiv) {
                                            alcoholValue = siblingDiv.textContent;
                                            break;
                                        }
                                    }
                                }

                                return alcoholValue;
                            JS;

                        $scriptOakType = <<<JS
                                var dtElements = document.querySelectorAll('.small.text-muted');
                                var alcoholValue = '';

                                for (var i = 0; i < dtElements.length; i++) {
                                    if (dtElements[i].textContent === 'Oak Type') {
                                        var siblingDiv = dtElements[i].nextElementSibling.querySelector('.text-more-line.js-content');
                                        if (siblingDiv) {
                                            alcoholValue = siblingDiv.textContent;
                                            break;
                                        }
                                    }
                                }

                                return alcoholValue;
                            JS;

                        $scriptClosureType = <<<JS
                                var dtElements = document.querySelectorAll('.small.text-muted');
                                var alcoholValue = '';

                                for (var i = 0; i < dtElements.length; i++) {
                                    if (dtElements[i].textContent === 'Closure Type') {
                                        var siblingDiv = dtElements[i].nextElementSibling.querySelector('.text-more-line.js-content');
                                        if (siblingDiv) {
                                            alcoholValue = siblingDiv.textContent;
                                            break;
                                        }
                                    }
                                }

                                return alcoholValue;
                            JS;

                        $scriptVineyardNotes = <<<JS
                                var dtElements = document.querySelectorAll('.small.text-muted');
                                var alcoholValue = '';

                                for (var i = 0; i < dtElements.length; i++) {
                                    if (dtElements[i].textContent === 'Vineyard Notes') {
                                        var siblingDiv = dtElements[i].nextElementSibling.querySelector('.text-truncate-more-line.js-content');
                                        if (siblingDiv) {
                                            alcoholValue = siblingDiv.textContent;
                                            break;
                                        }
                                    }
                                }

                                return alcoholValue;
                            JS;

                        $scriptWinemaking = <<<JS
                                var dtElements = document.querySelectorAll('.small.text-muted');
                                var alcoholValue = '';

                                for (var i = 0; i < dtElements.length; i++) {
                                    if (dtElements[i].textContent === 'Winemaking') {
                                        var siblingDiv = dtElements[i].nextElementSibling.querySelector('.text-truncate-more-line.js-content');
                                        if (siblingDiv) {
                                            alcoholValue = siblingDiv.textContent;
                                            break;
                                        }
                                    }
                                }

                                return alcoholValue;
                            JS;

                        $scriptAgeing = <<<JS
                                var dtElements = document.querySelectorAll('.small.text-muted');
                                var alcoholValue = '';

                                for (var i = 0; i < dtElements.length; i++) {
                                    if (dtElements[i].textContent === 'Ageing') {
                                        var siblingDiv = dtElements[i].nextElementSibling.querySelector('.text-truncate-more-line.js-content');
                                        if (siblingDiv) {
                                            alcoholValue = siblingDiv.textContent;
                                            break;
                                        }
                                    }
                                }

                                return alcoholValue;
                            JS;

                        $wineName = $browser->waitFor('.product-details__container-right', 60)
                            ->text('.product-details__container-right > li > h1');
                        $region = $this->extractElementInfo($browser, '.product-details__region-name > span');
                        $type = $this->extractElementInfo($browser, '.product-details__styles > span');
                        $rating = $this->extractElementInfo($browser, '.ml-2A > span');
                        $score = $this->extractElementInfo($browser, '.product-details__score > span');

                        $content = $this->extractElementInfo($browser, '.product-details__description > p');
                        $contentDetail = '';
                        $contentDetailElm = $browser->elements('.js-show-hide.d-block.text-decoration-none.btn-link-primary.smaller.pl-1A.collapsed.cursor-pointer');

                        if ($contentDetailElm) {
                            $browser->click('.js-show-hide.d-block.text-decoration-none.btn-link-primary.smaller.pl-1A.collapsed.cursor-pointer');
                            $contentDetail = $browser->text('.product-details__description .productDesc');
                        }

                        $alcoholABV = $browser->driver->executeScript($scriptAlcoholValue);
                        $blend = $browser->driver->executeScript($scriptBlend);
                        $maturation = $browser->driver->executeScript($scriptMaturation);
                        $oakType = $browser->driver->executeScript($scriptOakType);
                        $closureType = $browser->driver->executeScript($scriptClosureType);
                        $vineyardNotes = $browser->driver->executeScript($scriptVineyardNotes);
                        $wineMaking = $browser->driver->executeScript($scriptWinemaking);
                        $ageing = $browser->driver->executeScript($scriptAgeing);
                        $sweetness = $browser->driver->executeScript($scriptSweetness);

                        Wine::updateNewAttr($id, $wineName, $region, $type, $rating, $score, $sweetness, $content,
                            $contentDetail, $blend, $maturation, $oakType, $closureType, $vineyardNotes,
                            $wineMaking, $ageing, $alcoholABV);

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
                                $user = $this->getTextFromElements($browser, '.text-primary.info-card__item-link-text.font-weight-bold', $index);
                                $ratingScore = $this->getTextFromElements($browser, '.btn.d-inline-flex.align-self-center.info-card__critic-score.mr-3', $index);
                                $ratingDate = $this->getTextFromElements($browser, '.text-muted.pr-3', $index);
                                $ratingContent = $this->getTextFromElements($browser, '.info-card__item .pt-2', $index);

                                $review = new Review();
                                $review->user = $user;
                                $review->content = $ratingContent;
                                $review->date = $ratingDate;
                                $review->score = $ratingScore;
                                $review->wine_id = $id;
                                $review->save();

                            }


                        }

                        $browser->visit('https://www.wine-searcher.com/');

                        $browser->pause(1200);

                        dump("DONE: " . $id);

                    } catch (\Exception $e) {
                        Log::error($e->getMessage());
                        dump($e->getMessage());
                        Wine::updateLog($id, $e->getMessage());
                        continue;
                    }
                }
            }

        });
    }

    public function testExample(): void
    {
        try {
            $this->handSecondMc();
        }catch (\Exception $e){
            Log::error($e->getMessage());
        }
    }
}
