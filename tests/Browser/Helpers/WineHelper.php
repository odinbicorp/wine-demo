<?php

namespace Tests\Browser\Helpers;

use Facebook\WebDriver\WebDriverBy;

class WineHelper
{
    public static function getWineProfile($browser)
    {
        $script = <<<JS
                        function getSiblingContentByDtContent(dtContent, siblingClass) {
                                var dtElements = document.querySelectorAll('.small.text-muted');
                                var contentValue = '';

                                for (var i = 0; i < dtElements.length; i++) {
                                    if (dtElements[i].textContent === dtContent) {
                                        var siblingDiv = dtElements[i].nextElementSibling.querySelector(siblingClass);
                                        if (siblingDiv) {
                                            contentValue = siblingDiv.textContent;
                                            break;
                                        }
                                    }
                                }

                                return contentValue;
                            }

                            var alcohol = getSiblingContentByDtContent('Alcohol ABV', '.text-more-line.js-content');
                            var sweetness = getSiblingContentByDtContent('Sweetness', '.text-more-line.js-content');
                            var blend = getSiblingContentByDtContent('Blend', '.text-more-line.js-content');
                            var maturation = getSiblingContentByDtContent('Maturation', '.text-more-line.js-content');
                            var oakType = getSiblingContentByDtContent('Oak Type', '.text-more-line.js-content');
                            var closureType = getSiblingContentByDtContent('Closure Type', '.text-more-line.js-content');
                            var vineyardNotes = getSiblingContentByDtContent('Vineyard Notes', '.text-truncate-more-line.js-content');
                            var winemaking = getSiblingContentByDtContent('Winemaking', '.text-truncate-more-line.js-content');
                            var ageing = getSiblingContentByDtContent('Ageing', '.text-truncate-more-line.js-content');

                            return {
                                alcohol:alcohol,
                                sweetness:sweetness,
                                blend:blend,
                                maturation:maturation,
                                oakType:oakType,
                                closureType:closureType,
                                vineyardNotes: vineyardNotes,
                                winemaking: winemaking,
                                ageing: ageing
                            };
                    JS;

        return $browser->driver->executeScript($script);

    }

    public  static function extractElementInfo($browser, $selector)
    {

        $elmText = null;

        if (count($browser->elements($selector)) > 0) {
            $elmText = $browser->text($selector);
        }

        return $elmText;
    }

    public static function getTextFromElements($browser, $selector, $index)
    {
        $elements = $browser->elements($selector);

        if (count($elements) > $index) {
            $element = $elements[$index];
            return $element->getText();
        }

        return '';
    }

    public static function getTextFromCssSelector($selector,$cssSelector)
    {
        $result = '';

        try {
            $findSelector = optional($selector->findElement(WebDriverBy::cssSelector($cssSelector)));

            if ($findSelector) {
                $result = $findSelector->getText();
            }

        }catch (\Exception $e){
            dump($e->getMessage());
        }

        return $result;
    }


}
