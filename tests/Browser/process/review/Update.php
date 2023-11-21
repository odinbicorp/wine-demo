<?php

namespace Tests\Browser\process\review;

use App\Models\Wine;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\WineReivewProcess;
use Tests\DuskTestCase;

class Update extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function testExample(): void
    {
        $this->browse(function (Browser $browser) {

            $wines = Wine::withLogUnfinished();

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
