<?php

namespace Tests\Browser\process\review;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Support\Facades\Log;
use App\Models\Review;
use Tests\Browser\Pages\ReviewTranslation;

class Translation extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function testExample(): void
    {
        $this->browse(function (Browser $browser) {

            $reviews = Review::unfinishedBetween(1,9);

            if ($reviews->count() > 0){
                try {
                    ReviewTranslation::processHandle($browser,$reviews);

                }catch (\Exception $e){
                    dump($e->getMessage());
                }
            }
        });
    }
}
