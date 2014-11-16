<?php

ini_set("memory_limit","32M"); //enforce the memory constraint for this script
set_time_limit(0);

class Tracker {

    private $url;
    private $result;

    public function __construct($url) {
    	$this->url = $url;
    }

    public function fetch() {
		$fp = fopen ('temp.xml', 'w+');
		$curl = curl_init($this->url);
		curl_setopt($curl, CURLOPT_FILE, $fp);
		curl_exec($curl);
		curl_close($curl);
		fclose($fp);

		$this->parse();
    }

    public function parse() {

    	$count = 0;

		$parser = new XMLReader;
		$parser->open('temp.xml');

		// move to the first product node
		while ($parser->read() && $parser->name !== 'product');

		// iterate each product
		while ($parser->name === 'product')
		{
		    $item = new SimpleXMLElement($parser->readOuterXML());

		    $id = $item->productID;
		    $name = $item->name;
			$description = $item->description;
			$price = $item->price;
			$currency = $item->price['currency'];
			$url = $item->productURL;
			$categories = [];

			foreach($item->categories as $category) {
				array_push($categories, $category->category);
			}

			/*
				Product: [PRODUCT_NAME] ([PRODUCT_ID])
				Description: [PRODUCT_DESCRIPTION]
				Price: [PRODUCT_CURRENCY] [PRODUCT_PRICE]
				Categories: [PRODUCT_CATEGORY][,][PRODUCT_CATEGORY][...]
				URL: [PRODUCT_URL]
			*/

			$content = 'Product: '.$name.' ('.$id.')'.PHP_EOL;
			$content .= 'Description: '.$description.PHP_EOL;
			$content .= 'Price: '.$currency.' '.$price.PHP_EOL;
			$content .= 'Categories: ';
			foreach ($categories as $category) {
				$content .= $category.', ';
			}
			//$content = rtrim($content, ",");
			$content .= PHP_EOL;
			$content .= 'URL: '.$url.PHP_EOL;

		    // go to next product
		    $parser->next('product');
		    $count++;

		    file_put_contents('products/'.$id.'.txt', $content);
		}

		$this->result = $count;
    }

    public function writeFile($file, $content) {
		
    }

    public function getResult() {
    	return $this->result; 
    }
}

//echo $_GET["url"];
$url = 'http://pf.tradetracker.net/?aid=1&type=xml&encoding=utf-8&fid=567342&categoryType=2&additionalType=2&limit=10'; //&limit=10
$tracker = new Tracker($url);
$tracker->fetch();

echo $tracker->getResult();