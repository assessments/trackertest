<?php

ini_set("memory_limit","32M"); //enforce the memory constraint for this script
set_time_limit(0);

class Tracker {

    private $url;
    private $response;

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

		//http://stackoverflow.com/questions/1835177/

		$parser = new XMLReader;
		$parser->open('temp.xml');

		// move to the first product node
		while ($parser->read() && $parser->name !== 'product');

		// iterate each product
		while ($parser->name === 'product')
		{
		    $item = new SimpleXMLElement($parser->readOuterXML());

		    echo('product id: '.$item->productID.'<br/>');

		    // go to next product
		    $parser->next('product');
		}
    }

    public function getResult() {
    	return $this->response; 
    }
}

//echo $_GET["url"];
$url = 'http://pf.tradetracker.net/?aid=1&type=xml&encoding=utf-8&fid=567342&categoryType=2&additionalType=2&limit=10'; //&limit=10
$tracker = new Tracker($url);
$tracker->fetch();