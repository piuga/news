Piuga - News
=========================

News module for Magento 2

Facts
-----

-  version: 1.7.0
-  extension key: Piuga\_News

Description
-----------

This module will make possible to create news pages, group them in categories, or add widgets with list of news.

Features
-----------

- create a news page
- create news category
- show news in an overview listing page, category listing page, detail page
- widget with latest news
- settings for general listing

Requirements
------------

-  PHP >= 7.1.0 
-  Magento\_Framework
-  Magento\_Ui
-  Magento\_Catalog
-  Magento\_Cms
-  Magento\_Customer

Compatibility
-------------

-  Magento >= 2.3.0

Installation Instructions
-------------------------

From Magento 2 console run: 

 - `composer config repositories.piuga-news git git@github.com:piuga/news.git`
 - `composer require piuga/news:dev-master`
 - `php bin/magento module:enable Piuga_News`
 - `php bin/magento setup:upgrade`

Uninstallation
--------------

From Magento 2 console run: 

 - `php bin/magento module:uninstall Piuga_News`

Developer
---------

Petru Iuga - iugapetru@yahoo.com

Copyright
---------

|copy| 2019 Petru Iuga
