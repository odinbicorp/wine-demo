<?php

namespace Tests\Browser\process\price;

use App\Models\Wine;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\ProcessUpdatePage;
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

                $wine = Wine::getWineWithLog();

                if ($wine->count() > 0){
                    try {

                        ProcessUpdatePage::processHandle($browser,$wine);

                    }catch (\Exception $e){
                        dump($e->getMessage());
                    }
                }
            }


        });
    }
}
