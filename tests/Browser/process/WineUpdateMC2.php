<?php

namespace Tests\Browser\process;

use App\Models\Wine;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\ProcessUpdatePage;
use Tests\DuskTestCase;

class WineUpdateMC2 extends DuskTestCase
{
    public function testWine(): void
    {

        $this->browse(function (Browser $browser) {

            try {

                $wine = Wine::getWineBetweenWithLog(2001,4000);
                ProcessUpdatePage::processHandle($browser,$wine);

            }catch (\Exception $e){
                dump($e->getMessage());
            }
        });
    }
}
