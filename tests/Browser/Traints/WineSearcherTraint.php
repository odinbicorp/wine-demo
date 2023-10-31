<?php

namespace Tests\Browser\Traits;
use Laravel\Dusk\Browser;

trait WineSearcherTraint
{
    /**
     * Get text from a Dusk element if it exists.
     *
     * @param \Laravel\Dusk\Browser $browser
     * @param string $selector
     * @return string
     */
    function getTextIfExists($browser, $selector)
    {
        return $browser->elementExists($selector) ? $browser->text($selector) : '';
    }

}
