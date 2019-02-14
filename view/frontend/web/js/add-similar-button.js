define([
    'jquery',
    'mage/translate'
], function ($) {
    'use strict';

    $.widget('mage.addSimilarButton', {

        _create: function () {
            $('[data-product-id]').each(function () {
                let id = this.dataset.productId;
                let container = jQuery(this).closest('.product-item-details').find('[data-role=add-to-links]');
                let elem = document.createElement("a");
                elem.classList.add("action");
                elem.classList.add("find-similar");
                elem.title = $.mage.__('Find Similar');
                let urlParams = new URLSearchParams(window.location.search);
                urlParams.set('similar', id);
                let query = '?' + urlParams.toString();
                elem.href = window.location.origin + window.location.pathname + query;
                container.append(elem);
            })
        }
    });
    return $.mage.addSimilarButton;
});