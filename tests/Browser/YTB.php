<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class YTB extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function testExample(): void
    {
        $this->browse(function (Browser $browser) {
            try {
                $browser->visit('https://www.youtube.com/watch?v=0QWQPnO_mQU&ab_channel=DuyRNT')
                    ->keys('#movie_player', 'k')
                    ->pause(50000);
            }catch (\Exception $e){

                dump($e->getMessage());
                $browser->pause(30000);

            }
        });
    }
}
