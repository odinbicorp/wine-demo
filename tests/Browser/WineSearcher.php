<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\Wine;

class WineSearcher extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function testExample(): void
    {
        $wines = Wine::getEmptyNewName();

        $this->browse(function (Browser $browser) use ($wines){

            if ($wines->count() > 0){

                $browser->visit('https://www.wine-searcher.com/');

                foreach ($wines as $index => $wine){

                    $id = $wine->id;
                    $originName = $wine->origin_name;

                    if ($index < 5){

                        dump("Origin name: ".$originName);

                        $browser->waitFor('[name="Xwinename"]',60)->type('Xwinename', $originName)
                            ->waitFor('.tt-suggestion',60)
                            ->pause(1000);

                        $browser->elements('.tt-suggestion')[0]->click();

                        $wineName  = $browser->waitFor('.product-details__container-right',60)
                            ->text('.product-details__container-right > li > h1');

                        Wine::updateNewName($id,$wineName);

                        $browser->clear('Xwinename');

                        $browser->pause(1200);


                        dump($wineName);

                        $browser->pause(1200);
                    }
                }

            }

        });
    }
}
