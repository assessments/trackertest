<?php

error_reporting(E_STRICT); //enforce E_STRICT requirement
ini_set("memory_limit","32M"); //enforce the memory constraint requirement
set_time_limit(0); //allow long execution time for huge XML files
libxml_use_internal_errors(true); //suppress libXML errors so they can be handled explicitly

/**
 * Tracker class
 */

class Tracker {

    /**
     * Models
     */

    private $url;
    private $response = null;
    private $errors = [];

    //constructor
    public function __construct($url) {
        $this->url = $url;
    }

    /**
     * Controllers
     */ 

    public function fetch() {
    	//delete all existing product files in the products directory
    	array_map('unlink', glob("products/*"));

    	//download the requested url
        $fp = fopen ('download.xml', 'w+');
        $curl = curl_init($this->url);
        curl_setopt($curl, CURLOPT_FILE, $fp);
        curl_exec($curl);
        if (curl_errno($curl))
        {
            array_push($this->errors, curl_error($curl));
        }
        curl_close($curl);
        fclose($fp);
        $this->parse();
    }

    public function parse() {
        $count = 0;

        //use XMLReader API to read a continous stream rather than loading the entire XML tree into memory
        $parser = new XMLReader;
        $parser->open('download.xml');

        //move to the first product node
        while ($parser->read() && $parser->name !== 'product');

        //iterate each product
        while ($parser->name === 'product')
        {
            $product = $this->parseNode($parser->readOuterXML());
            $parser->next('product');
            $count++;
        }

        foreach(libxml_get_errors() as $error) {
            array_push($this->errors, $error->message);
        }

        $this->response = $count;
    }

    public function parseNode($node) {
        //use SimpleXML to parse each product node because the API is easier
        $item = new SimpleXMLElement($node);

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
        	Output file requirement:

            Product: [PRODUCT_NAME] ([PRODUCT_ID])
            Description: [PRODUCT_DESCRIPTION]
            Price: [PRODUCT_CURRENCY] [PRODUCT_PRICE]
            Categories: [PRODUCT_CATEGORY][,][PRODUCT_CATEGORY][...]
            URL: [PRODUCT_URL]
        */

        //construct the product file
        $content = 'Product: '.$name.' ('.$id.')'.PHP_EOL;
        $content .= 'Description: '.$description.PHP_EOL;
        $content .= 'Price: '.$currency.' '.$price.PHP_EOL;
        $content .= 'Categories: ';
        foreach ($categories as $category) {
            $content .= $category.', ';
        }
        $content = rtrim($content, ", ");
        $content .= PHP_EOL;
        $content .= 'URL: '.$url.PHP_EOL;

        //write the product file
        file_put_contents('products/'.$id.'.txt', $content);
    }

    /**
     * Views
     *
     * In this case, just JSON
     */

    public function response() {
        $response = (object)[
            'count' => $this->response,
            'errors' => $this->errors
        ];
        echo json_encode($response); 
    }
}

$url = urldecode($_POST["url"]);

$tracker = new Tracker($url);
$tracker->fetch();
$tracker->response();