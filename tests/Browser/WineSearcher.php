<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Log;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\Wine;

class WineSearcher extends DuskTestCase
{

    /**
     * Get text from a Dusk element if it exists.
     *
     * @param \Laravel\Dusk\Browser $browser
     * @param string $selector
     * @return string
     */

    private function extractElementInfo(Browser $browser, $selector) {

        $elmText = '';

        if (count($browser->elements($selector)) > 0) {
            $elmText = $browser->text($selector);
        }

        return $elmText;
    }

    private function getTextFromElements($browser, $selector, $index) {

        $elements = $browser->elements($selector);

        if (count($elements) > $index) {
            return $elements[$index]->getText();
        }

        return '';
    }

    public function testExample(): void
    {
        $wines = Wine::getEmptyNewName();

        $this->browse(function (Browser $browser) use ($wines){

            if ($wines->count() > 0){

                $browser->visit('https://www.wine-searcher.com/');

                foreach ($wines as $index => $wine){
                    try {

                        $id = $wine->id;
                        $originName = $wine->origin_name;

                        if ($index < 3){

                            dump("Origin name: ".$originName);

                            $captChar = $browser->elements('.px-captcha-message');

                            //$cokieName = $browser->driver->manage()->getCookies();
                            //dump($cokieName);

                            if ($captChar){
                                dump("Ok");
                                $browser->visit('https://www.wine-searcher.com/');
                                //$browser->refresh();
                                $browser->driver->manage()->deleteAllCookies();
                            }

                            $browser->keys('#Xwinename',  ['{CONTROL}', 'a']);

                            $browser->keys('#Xwinename', ['{delete}']);

                            $browser->waitFor('[name="Xwinename"]',60)->typeSlowly('Xwinename', 'Varvaglione 12 e Mezzo Primitivo del Salento IGP',1)
                                ->waitFor('.tt-suggestion',60)
                                ->pause(1000);

                            $browser->elements('.tt-suggestion')[0]->click();

                            $browser->waitFor('#find-tab-info',10)->click('#find-tab-info');
                            $browser->pause(1000);

                            $wineName  = $browser->waitFor('.product-details__container-right',60)
                                ->text('.product-details__container-right > li > h1');
                            $region = $this->extractElementInfo($browser, '.product-details__region-name > span');
                            $type = $this->extractElementInfo($browser, '.product-details__styles > span');
                            $rating = $this->extractElementInfo($browser, '.ml-2A > span');
                            $score = $this->extractElementInfo($browser, '.product-details__score > span');
                            $sweetness = $this->extractElementInfo($browser, '.text-more-line');
                            $content = $this->extractElementInfo($browser, '.product-details__description > p');
                            $contentDetail = $this->extractElementInfo($browser, '.productDesc');

                            if (count($browser->elements('.productDesc')) > 0) {
                                dump("YES");
                                dump($browser->elements('.productDesc')[0]->getText());

                            }else{
                                dump("NO");
                            }

                            $blend = $this->getTextFromElements($browser, '.text-more-line', 1);
                            $maturation = $this->getTextFromElements($browser, '.text-more-line', 2);
                            $oakType = $this->getTextFromElements($browser, '.text-more-line', 3);
                            $closureType = $this->getTextFromElements($browser, '.text-more-line', 4);
                            $vineyardNotes = $this->getTextFromElements($browser, '.text-truncate-more-line', 0);
                            $wineMaking = $this->getTextFromElements($browser, '.text-truncate-more-line', 1);
                            $ageing = $this->getTextFromElements($browser, '.text-truncate-more-line', 2);

                            dump("Region: ". $region);
                            dump("Type: ". $type);
                            dump("Rating: ". $rating);
                            dump("Score: ". $score);
                            dump("Content: ". $content);
                            dump("Content Detail: ". $contentDetail);
                            dump("Weetness: ". $sweetness);
                            dump("Blend: ". $blend);
                            dump("Maturation: ". $maturation);
                            dump("Oak Type: ". $oakType);
                            dump("Closure Type: ". $closureType);
                            dump("Note: ". $vineyardNotes);
                            dump("Making: ". $wineMaking);
                            dump("Ageing: ". $ageing);

                            //$browser->pause(30000);

                             Wine::updateNewAttr($id,$wineName,$region, $type, $rating, $score, $sweetness,
                                $content,
                                $contentDetail, $blend, $maturation, $oakType, $closureType, $vineyardNotes,
                                $wineMaking, $ageing);

                            $browser->waitFor('#find-tab-reviews',10)->click('#find-tab-reviews');
                            $browser->pause(1000);

                            $infoCardItems = $browser->elements('.text-primary.info-card__item-link-text.font-weight-bold');

                            if (count($infoCardItems) > 0) {

                                dump("EXISTS");

                                $seeMoreElement = $browser->elements('.info-card__link-toggle.collapsed.d-block.text-center.btn-link-primary');

                                if ($seeMoreElement){
                                    dump("Count: ".count($seeMoreElement));
                                    $seeMoreElement[2]->click();
                                }

                                foreach ($infoCardItems as $index => $infoCardItem){

                                    $user = $this->getTextFromElements($browser, '.text-primary.info-card__item-link-text.font-weight-bold',$index);
                                    $userRating = $this->getTextFromElements($browser,'.btn.d-inline-flex.align-self-center.info-card__critic-score.mr-3',$index);

                                    dump( "User index: ".$index);
                                    dump( $user);
                                    dump( "Rating score: ".$userRating);
                                }

                            }else{
                                dump("NOT EXISTS");
                            }


                            //$ratingContent = $this->extractElementInfo($browser, '.text-muted .pr-3');


                            $browser->pause(90000);

                            $browser->visit('https://www.wine-searcher.com/');


                            $browser->pause(1200);

                            dump($wineName);


                        }
                    }catch (\Exception $e){
                        Log::error($e->getMessage());
                        dump($e->getMessage());
                        //Wine::updateNewAttr($id,"Error");
                        continue;
                    }
                }
            }

        });
    }
}
