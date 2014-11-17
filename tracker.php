<?php

ini_set("memory_limit","32M"); //enforce the memory constraint for this script
set_time_limit(0);
libxml_use_internal_errors(true); //http://php.net/manual/en/simplexml.examples-errors.php

class Tracker {

    private $url;
    private $response = null;
    private $errors = [];

    public function __construct($url) {
        $this->url = $url;
    }

    public function fetch() {
        $fp = fopen ('temp.xml', 'w+');
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
        $parser->open('temp.xml');

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
        $content = rtrim($content, ", ");
        $content .= PHP_EOL;
        $content .= 'URL: '.$url.PHP_EOL;

        file_put_contents('products/'.$id.'.txt', $content);
    }

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