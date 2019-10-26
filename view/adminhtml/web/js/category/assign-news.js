/* global $, $H */
define([
    'mage/adminhtml/grid'
], function () {
    'use strict';

    return function (config) {
        var selectedNews = config.selectedNews,
            categoryNews = $H(selectedNews),
            gridJsObject = window[config.gridJsObjectName],
            tabIndex = 1000;

        $('in_category_news').value = Object.toJSON(categoryNews);

        /**
         * Register Category News
         *
         * @param {Object} grid
         * @param {Object} element
         * @param {Boolean} checked
         */
        function registerCategoryNews(grid, element, checked) {
            if (checked) {
                if (element.positionElement) {
                    element.positionElement.disabled = false;
                    categoryNews.set(element.value, element.positionElement.value);
                }
            } else {
                if (element.positionElement) {
                    element.positionElement.disabled = true;
                }
                categoryNews.unset(element.value);
            }
            $('in_category_news').value = Object.toJSON(categoryNews);
            grid.reloadParams = {
                'selected_news[]': categoryNews.keys()
            };
        }

        /**
         * Click on news row
         *
         * @param {Object} grid
         * @param {String} event
         */
        function categoryNewsRowClick(grid, event) {
            var trElement = Event.findElement(event, 'tr'),
                isInput = Event.element(event).tagName === 'INPUT',
                checked = false,
                checkbox = null;

            if (trElement) {
                checkbox = Element.getElementsBySelector(trElement, 'input');

                if (checkbox[0]) {
                    checked = isInput ? checkbox[0].checked : !checkbox[0].checked;
                    gridJsObject.setCheckboxChecked(checkbox[0], checked);
                }
            }
        }

        /**
         * Change news position
         *
         * @param {String} event
         */
        function positionChange(event) {
            var element = Event.element(event);

            if (element && element.checkboxElement && element.checkboxElement.checked) {
                categoryNews.set(element.checkboxElement.value, element.value);
                $('in_category_news').value = Object.toJSON(categoryNews);
            }
        }

        /**
         * Initialize category news row
         *
         * @param {Object} grid
         * @param {String} row
         */
        function categoryNewsRowInit(grid, row) {
            var checkbox = $(row).getElementsByClassName('checkbox')[0],
                position = $(row).getElementsByClassName('input-text')[0];

            if (checkbox && position) {
                checkbox.positionElement = position;
                position.checkboxElement = checkbox;
                position.disabled = !checkbox.checked;
                position.tabIndex = tabIndex++;
                Event.observe(position, 'keyup', positionChange);
            }
        }

        gridJsObject.rowClickCallback = categoryNewsRowClick;
        gridJsObject.initRowCallback = categoryNewsRowInit;
        gridJsObject.checkboxCheckCallback = registerCategoryNews;

        if (gridJsObject.rows) {
            gridJsObject.rows.each(function (row) {
                categoryNewsRowInit(gridJsObject, row);
            });
        }
    };
});
