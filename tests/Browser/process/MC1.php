<?php

namespace Tests\Browser\process;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Browser\Pages\ProcessPage;
use App\Models\Wine;

class MC1 extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function testExample(): void
    {

        $this->browse(function (Browser $browser) {

            try {

                $wines = Wine::getWineByIsNullName(1001,1650);

                ProcessPage::processHandle($browser,$wines);

            }catch (\Exception $e){
                dump($e->getMessage());
            }

        });
    }
}
