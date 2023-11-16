<?php

namespace Tests\Browser\Helpers;

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

    public static function getWineReview($browser)
    {
        $script = <<<JS
                        function getSiblingContentByDtContent(siblingClass,index) {
                                var dtElements = document.querySelectorAll('.text-primary.info-card__item-link-text.font-weight-bold');
                                var contentValue = '';

                                for (var i = 0; i < dtElements.length; i++) {
                                    var parentElement = dtElements[i].closest('.info-card__item');
                                    var siblingDiv = parentElement.querySelector(siblingClass);

                                    if (siblingDivs.length > index && siblingDivs[index] !== undefined) {
                                        contentValue = siblingDivs[index].textContent;

                                    }
                                }

                                return contentValue;
                            }

                            var ratingContent = getSiblingContentByDtContent('.pt-2',1);
                            var ratingDate = getSiblingContentByDtContent('.text-muted.pr-3',0);

                            return {
                                ratingContent: ratingContent,
                                ratingDate: ratingDate
                            };

                    JS;

        return $browser->driver->executeScript($script);

    }


}
