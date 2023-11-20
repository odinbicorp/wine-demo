<?php

namespace Tests\Browser\process;

use App\Models\Wine;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\ProcessUpdatePage;
use Tests\DuskTestCase;
use Illuminate\Support\Facades\Http;
use Spatie\Async\Pool;

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
        try {
            $wine = Wine::getWineBetweenWithLog(1, 1000);
            $wine2 = Wine::getWineBetweenWithLog(1001, 2000);

            // Sử dụng spatie/async để thực hiện đồng thời
            $this->runAsync(function () use ($wine, $wine2) {
                $this->asyncProcessHandle($wine);
                $this->asyncProcessHandle($wine2);
            });

        } catch (\Exception $e) {
            dump($e->getMessage());
        }
    }

    private function asyncProcessHandle($wine)
    {
        $this->runAsync(function () use ($wine) {
            $browser = new Browser();
            ProcessUpdatePage::processHandle($browser, $wine);
        });
    }

    private function runAsync(callable $callback)
    {
        $pool = Pool::create()
            ->concurrent(2) // Set the number of parallel executions
            ->timeout(30) // Set a timeout if needed
            ->output(new ParallelProgressOutput());

        $pool->add($callback);
        $pool->wait();
    }

}
