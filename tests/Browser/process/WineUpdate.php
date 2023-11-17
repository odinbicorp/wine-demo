<?php

namespace Tests\Browser\process;

use App\Models\Wine;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\ProcessUpdatePage;
use Tests\DuskTestCase;

class WineUpdate extends DuskTestCase
{
    /**
     * A Dusk test example.
     */

    public function hanldeTest(): void
    {
        $mcQty = 5;

        $this->browse(function ($first, $second, $third, $fourth, $fifth) use ($mcQty) {
            $browsers = [$first, $second, $third, $fourth, $fifth];

            foreach ($browsers as $index => $browser) {
                $startId = $index * 20 + 1;
                $endId = ($index + 1) * 20;

                $wines = Wine::getWineBetween($startId, $endId);
                ProcessUpdatePage::processHandle($browser,$wines);
                if ($index < $mcQty - 1) {
                    // Open a new browser tab for each iteration (except the last one)
                    $browser->driver->executeScript('window.open()', null);
                    $browser->driver->switchTo()->window(end($browser->driver->getWindowHandles()));
                }


            }
        });
    }
    public function testWine(): void
    {

        $this->browse(function (Browser $browser) {

            try {

                $wine = Wine::getWineBetweenWithLog(20,2000);
                ProcessUpdatePage::processHandle($browser,$wine);

            }catch (\Exception $e){
                dump($e->getMessage());
            }
        });
    }


}
