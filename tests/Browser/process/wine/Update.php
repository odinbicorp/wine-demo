<?php

namespace Tests\Browser\process\wine;

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

            $wines = Wine::getWineWithLog();

            if ($wines->count() > 0){

                try {
                    ProcessUpdatePage::processHandle($browser,$wines);

                }catch (\Exception $e){
                    dump($e->getMessage());
                }
            }

        });
    }
}
