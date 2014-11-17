Trackertest
===========

Requirements
------------

- Internet Explorer 9+ or any other modern browser
- JavaScript enabled
- tested on PHP 5.5.9 with Apache 2.4.7 on Ubuntu 14.04.1 LTS

Installation
------------

Install php5-curl:

	sudo apt-get install php5-curl

Grant ownership of the products directory:

	sudo chown -R www-data products

Example url to submit
---------------------

http://pf.tradetracker.net/?aid=1&type=xml&encoding=utf-8&fid=567342&categoryType=2&additionalType=2&limit=10

Concepts illustrated
--------------------

- pure JavaScript solution (no jQuery or other framework)
- separation of concerns through basic MVC structure (client-side and server-side)
- JSON response with client-side rendering
- URL encoding
- XML parsing
