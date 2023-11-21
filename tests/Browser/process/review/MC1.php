<?php

namespace Tests\Browser\process\review;

use App\Models\Wine;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\WineReivewProcess;
use Tests\DuskTestCase;

class MC1 extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function testExample(): void
    {
        $this->browse(function (Browser $browser) {

            $wines = Wine::unfinishedBetween(1,2000);

            if ($wines->count() > 0){

                try {
                    WineReivewProcess::processHandle($browser,$wines);

                }catch (\Exception $e){
                    dump($e->getMessage());
                }
            }

        });
    }
}
