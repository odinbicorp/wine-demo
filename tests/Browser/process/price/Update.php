<?php

namespace Tests\Browser\process\price;

use App\Models\Wine;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\ProcessPriceUpdate;
use Tests\DuskTestCase;

class Update extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function testExample(): void
    {
        $this->browse(function (Browser $browser) {

            while (true){

                $wine = Wine::getWineWithPriceLog();

                if ($wine->count() > 0){
                    try {

                        ProcessPriceUpdate::processHandle($browser,$wine);

                    }catch (\Exception $e){
                        dump($e->getMessage());
                    }
                }
            }


        });
    }
}
