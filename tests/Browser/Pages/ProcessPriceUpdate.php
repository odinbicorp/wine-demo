<?php

namespace Tests\Browser\Pages;

use App\Models\Review;
use App\Models\Wine;
use Illuminate\Support\Facades\Log;
use Tests\Browser\Helpers\WineHelper;
use Illuminate\Support\Str;

class ProcessPriceUpdate
{
    public static function processHandle($browser,$wines)
    {
        if ($wines->count() > 0) {

            $browser->visit('https://www.wine-searcher.com/');

            foreach ($wines as $index => $wine) {

                Wine::updatePriceLog($wine->id,null);

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
                    $browser->pause(1000);

                    $price = WineHelper::extractElementInfo($browser, '.price.text-nowrap  .font-light-bold');
                    $currency = WineHelper::extractElementInfo($browser, '.price.text-nowrap');


                    if ($price){

                        $price = str_replace(",","",$price);
                        $containsChar = Str::contains($currency, '₫');

                        if ($containsChar) {
                            $currency = 'VND';
                        } else {
                            $currency = 'USD';
                        }
                    }

                    $fillableAttributes = [
                        'price' => $price,
                        'currency' => $currency
                    ];

                    Wine::wineUpdate($id,$fillableAttributes);

                    $browser->visit('https://www.wine-searcher.com/');
                    Wine::updatePriceLog($id, "DONE");
                    $browser->pause(1200);

                } catch (\Exception $e) {

                    Log::error($e->getMessage());
                    dump($e->getMessage());
                    Wine::updatePriceLog($id, $e->getMessage());
                    $browser->visit('https://www.wine-searcher.com/');
                    continue;
                }
            }

            dump("Completed");
        }
    }

}
