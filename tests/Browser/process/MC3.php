<?php

namespace Tests\Browser\process;

use App\Models\Wine;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\ProcessPage;
use Tests\DuskTestCase;

class MC3 extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function testExample(): void
    {
        $this->browse(function (Browser $browser) {

            try {

                $wines = Wine::getWineByIsNullName(2001,3000);

                ProcessPage::processHandle($browser,$wines);

            }catch (\Exception $e){
                dump($e->getMessage());
            }

        });
    }
}
