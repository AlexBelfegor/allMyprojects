<?php

/**
 * Earnberry API client implementation
 *
 * @author Mykhail Diordienko <infra@earnberry.net>
 * @api
 */
class Earnberry
{
    protected static $instances = array(

    );
    protected $apiKey = false;
    protected $id = false;
    protected $apiUrl = 'https://earnberry.net/';
    protected $version = '1.1.6beta';

    public static $encoding = false;

    /**
     * Creates new instance of client with given id and api_key.
     *
     * @param string $id Earnberry platform id.
     * @param string $apiKey Earnberry platform api key.
     *
     * @return Earnberry Earnberry client object
     */
    public static function createInstance($id, $apiKey)
    {
        self::$instances[$id] = new Earnberry($id, $apiKey);
        return self::$instances[$id];
    }

    /**
     *
     * Gets previously created instanse.     
     * 
     * @param string $id Earnberry platform id.
     *
     * @return Earnberry Earnberry client object.
     */
    public static function getInstance($id = false)
    {
        if (sizeof(self::$instances))
        {
            if ($id && !array_key_exists($id, self::$instances))
            {
                $e = new EarnberryException('Sorry no such earnberry instance...');
                throw $e;
            }
            $i = $id  ? self::$instances[$id] : self::$instances[key(self::$instances)];
        }
        else
        {
            $e = new EarnberryException('You didn\'t initialize any Earnberry instance.');
            $e->setIssueType(EarnberryException::ISSUE_TYPE_LOCAL_VALIDATION);
            throw $e;
        }

        return $i;
    }

    /**
     * Sets encoding used to convert text to UTF-8 before sending them to Earnberry API.
     *
     * @param string $encoding Earnberry platform id.
     *
     * @throws EarnberryException if encoding is not supported
     */
    public static function setEncoding($encoding)
    {
        $check = @iconv($encoding, 'UTF-8', 'test_encoding');
        if (!$check)
        {
            $e = new EarnberryException('You set incorrect encoding: '. $encoding);
            $e->setIssueType(EarnberryException::ISSUE_TYPE_LOCAL_VALIDATION);
            throw $e;
        }
        self::$encoding = $encoding;
    }

    /**
     * Gets encoding. 
     *
     * @return string FALSE for default encoding (UTF-8)
     */
    public static function getEncoding()
    {
        return self::$encoding;
    }

    /**
     * Convert characters from setted encoding to UTF-8.
     *
     * @return string UTF-8 chararacters
     */
    public static function _charactersConvert($characters)
    {
        if (self::$encoding)
        {
            return iconv(self::$encoding, 'UTF-8//IGNORE', $characters);
        }
        return $characters;
    }
    private function __construct($id, $apiKey)
    {
        $this->apiKey = $apiKey;
        $this->id = $id;
    }

    /**
     * Sets API url. Can be used for communication with test/debug API server.
     *
     * @return Earnberry Self
     */
    public function setApiUrl($url)
    {
        $this->apiUrl = $url;
        return $this;
    }
    protected function _namespacedCookie($key)
    {
        $key = 'earnberry_'.$this->id.'_'.$key;
        return $_COOKIE[$key];
    }

    /**
     * Gets tracking id from user's cookie. Tracking id used to connect user's order with source.
     *
     * @return Earnberry Self
     */
    public function getTrackingId()
    {
        return $this->_namespacedCookie('trackingId');
    }

    /**
     * Core method for calling api at Earnberry server. Methods provided on for concrete API call are much handy to use.
     *
     * @return boolean|array Result data for API call if all is ok.
     *
     * @throws EarnberryException if call not passed any kind of validation.
     *
     */
    public function _apiCall($action, $requestMethod, $params)
    {
        $ch = curl_init();
        $url = $this->apiUrl.'api/'.$action;
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-Earnberry-API-Key: '.$this->apiKey));
        $params['_api_client_encoding'] = self::$encoding;
        if ('GET' == $requestMethod)
        {
            $url = $url.'?'.http_build_query($params);
        }
        elseif ('POST' == $requestMethod)
        {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        }
        else
        {
            $e = new EarnberryException('Sorry, but supported method\'s at this moment are GET, POST');
            $e->setIssueType(EarnberryException::ISSUE_TYPE_LOCAL_VALIDATION);
            throw $e;
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Earnberry API Client version '.$this->version);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);

        $data = curl_exec($ch);

        if ($connectionError = curl_errno($ch))
        {
            curl_close($ch);
            $errorMessage = 'Sorry connection problems. Curl error code: '. $connectionError;
            if ($connectionError == 6)
            {
                $errorMessage = 'It seems you place incorrect apiURL. Current is: '.$this->apiUrl.' or DNS server cannot resolve it';
            }
            $e = new EarnberryException($errorMessage);
            $e->setIssueType(EarnberryException::ISSUE_TYPE_NETWORK_PROBLEM);
            throw $e;
        }

        $httpCode =  curl_getinfo ($ch, CURLINFO_HTTP_CODE);
        if ($httpCode != 200)
        {
            $errorMessage = 'Strange, but your api url ('.$this->apiUrl.') responses with incorrect http code '.$httpCode;
            $e = new EarnberryException($errorMessage);
            $e->setIssueType(EarnberryException::ISSUE_TYPE_NETWORK_PROBLEM);
            throw $e;
        }

        curl_close($ch);
        return $data ? json_decode($data, true) : false;
    }

    /**
     * API call method for pushing conversion.
     *
     * @param EarnberryTransaction Transaction data container object.
     *
     * @see EarnberryTransaction
     *
     * @return array Result.
     *
     * @throws EarnberryException if call not passed any kind of validation.
     *
     */
    public function conversionPush(EarnberryTransaction $t)
    {
        if (!$t->validate())
        {
            $e = new EarnberryException('Cannt execute conversion push api call, because data params failed with validation errors: '. join(',', $t->getValidationErrors()));
            $e->setIssueType(EarnberryException::ISSUE_TYPE_LOCAL_VALIDATION);
            throw $e;
        }
        $params = $t->getAPIReadyData();
        $result = $this->_apiCall('conversion_push', 'POST', $params);
        if (!$result)
        {
            $e = new EarnberryException('Api call failed with incorrect response. Params: '.json_encode($params));
            $e->setIssueType(EarnberryException::ISSUE_TYPE_NETWORK_PROBLEM);
            throw $e;
        }
        elseif ($result['result'] == 'OK')
        {
            return $result;
        }
        else
        {
            $e = new EarnberryException('Api conversion push failed with backend errors: '.$result['errors']);
            $e->setIssueType(EarnberryException::ISSUE_TYPE_BACKEND_VALIDATION);
            throw $e;
        }
    }

    /**
     * API call method for updating total and/or grossprofit of the transaction.
     *
     * @see http://www.iso.org/iso/home/standards/currency_codes.htm
     * @see https://developers.google.com/analytics/devguides/platform/currencies
     *
     * @param string $orderId Transaction order identifier.
     * @param float|false $revenue New transaction revenue. Not required.
     * @param float|false $grossprofit New transaction grossprofit amount. Not required.
     * @param string|false $currency Currency ISO code from supported in Google Analytics.
     *
     *
     * @link EarnberryTransaction
     *
     * @return boolean Result.
     *
     * @throws EarnberryException if call not passed any kind of validation.
     *
     */
    public function conversionUpdateTotal($orderId, $revenue = false, $grossprofit = false, $currency = false)
    {
        if (!is_numeric($revenue) && !is_numeric($grossprofit))
        {
            $e = new EarnberryException('Conversion update total should have numeric value at least revenue or grossprofit.');
            $e->setIssueType(EarnberryException::ISSUE_TYPE_LOCAL_VALIDATION);
            throw $e;
        }
        $data = array('orderId' => $orderId, 'total' => $revenue, 'grossprofit' => $grossprofit);
        if ($currency && is_string($currency))
        {
            $data['currency'] = $currency;
        }
        $responseParsed = $this->_apiCall('conversion_update_total', 'POST', $data);
        if ($responseParsed)
        {
            if ($responseParsed['result'] == 'OK')
            {
                return true;
            }
            else
            {
                $e = new EarnberryException($responseParsed['errors']);
                $e->setIssueType(EarnberryException::ISSUE_TYPE_BACKEND_VALIDATION);
                throw $e;
            }
        }
        return $responseParsed;
    }

    /**
     * API call method for receiving data of the previously pushed conversion.
     *
     * @param string $orderId Transaction order identifier.
     *
     * @return array Result.
     *
     * @throws EarnberryException if call not passed any kind of validation.
     *
     */
    public function conversionInfo($orderId)
    {
        $data = array('orderId' => $orderId);
        $responseParsed = $this->_apiCall('conversion_info', 'GET', $data);
        if ($responseParsed)
        {
            if ($responseParsed['result'] == 'OK')
            {
                return $responseParsed['data'];
            }
            else
            {
                $e = new EarnberryException($responseParsed['errors']);
                $e->setIssueType(EarnberryException::ISSUE_TYPE_BACKEND_VALIDATION);
                throw $e;
            }
        }
        return $responseParsed;
    }

    /**
     * API call method for receiving platform data (at this moment - only some settings.)
     *
     * @param string $resource String identifier of platform resource. Possible resources are - platform.markers , platform.id.
     *
     * @return array Result.
     *
     * @throws EarnberryException if call not passed any kind of validation.
     *
     */
    public function meta($resource)
    {
        $data = array('resource' => $resource);
        $responseParsed = $this->_apiCall('platform_meta', 'GET', $data);
        if ($responseParsed)
        {
            if ($responseParsed['result'] == 'OK')
            {
                return $responseParsed['data'];
            }
            else
            {
                $e = new EarnberryException($responseParsed['errors']);
                $e->setIssueType(EarnberryException::ISSUE_TYPE_BACKEND_VALIDATION);
                throw $e;
            }
        }
        return $responseParsed;
    }

}

/**
 * Earnberry order transaction construction class.
 *
 * @author Mykhail Diordienko <infra@earnberry.net>
 * @api
 */
class EarnberryTransaction
{
    protected $trackingId = '';
    protected $orderId = '';
    protected $total = 0;
    protected $productTotal = 0;
    protected $products = array();
    protected $tax = 0;
    protected $city = '';
    protected $state = '';
    protected $country = '';
    protected $currency = false;
    protected $shippingCost = 0;
    protected $dryRun = false;
    protected $force = false;
    protected $marker = false;
    protected $grossprofit = false;
    protected $dimensions = array();
    protected $metrics = array();

    public static $supportedCurrencies = array(
        'USD' =>	'US Dollars',
        'AED' =>	'United Arab Emirates Dirham',
        'ARS' =>	'Argentine Pesos',
        'AUD' =>	'Australian Dollars',
        'BGN' =>	'Bulgarian Lev',
        'BOB' =>	'Bolivian Boliviano',
        'BRL' =>	'Brazilian Real',
        'CAD' =>	'Canadian Dollars',
        'CHF' =>	'Swiss Francs',
        'CLP' =>	'Chilean Peso',
        'CNY' =>	'Yuan Renminbi',
        'COP' =>	'Colombian Peso',
        'CZK' =>	'Czech Koruna',
        'DKK' =>	'Denmark Kroner',
        'EGP' =>	'Egyptian Pound',
        'EUR' =>	'Euros',
        'FRF' =>	'French Francs',
        'GBP' =>	'British Pounds',
        'HKD' =>	'Hong Kong Dollars',
        'HRK' =>	'Croatian Kuna',
        'HUF' =>	'Hungarian Forint',
        'IDR' =>	'Indonesian Rupiah',
        'ILS' =>	'Israeli Shekel',
        'INR' =>	'Indian Rupee',
        'JPY' =>	'Japanese Yen',
        'KRW' =>	'South Korean Won',
        'LTL' =>	'Lithuanian Litas',
        'MAD' =>	'Moroccan Dirham',
        'MXN' =>	'Mexican Peso',
        'MYR' =>	'Malaysian Ringgit',
        'NOK' =>	'Norway Kroner',
        'NZD' =>	'New Zealand Dollars',
        'PEN' =>	'Peruvian Nuevo Sol',
        'PHP' =>	'Philippine Peso',
        'PKR' =>	'Pakistan Rupee',
        'PLN' =>	'Polish New Zloty',
        'RON' =>	'New Romanian Leu',
        'RSD' =>	'Serbian Dinar',
        'RUB' =>	'Russian Ruble',
        'SAR' =>	'Saudi Riyal',
        'SEK' =>	'Sweden Kronor',
        'SGD' =>	'Singapore Dollars',
        'THB' =>	'Thai Baht',
        'TRL' =>	'Turkish Lira',
        'TWD' =>	'New Taiwan Dollar',
        'UAH' =>	'Ukrainian Hryvnia',
        'VEF' =>	'Venezuela Bolivar Fuerte',
        'VND' =>	'Vietnamese Dong',
        'ZAR' =>    'South African Rand'
    );

    /**
     * Sets custom id of the transaction If not called will be generated automatically.
     *
     * @param string $orderId Transaction orderId.
     *
     * @return EarnberryTransaction
     *
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
        return $this;
    }

    /**
     * Sets custom id of the transaction If not called will be generated automatically.
     *
     * @param string $orderId Transaction orderId.
     *
     * @return EarnberryTransaction
     *
     */
    public function setTrackingId($id)
    {
        $this->trackingId = $id;
        return $this;
    }

    /**
     * Sets city and state for the transaction. Directly inserted in GA transation. Not required.
     *
     * @param string $city City
     * @param string $state State
     *
     * @return EarnberryTransaction
     *
     */
    public function setCityState($city, $state)
    {
        $this->city = Earnberry::_charactersConvert($city);
        $this->state = Earnberry::_charactersConvert($state);
        return $this;
    }

    /**
     * Sets country for the transaction. Directly inserted in GA transation. Not required.
     *
     * @param string $country country
     *
     * @return EarnberryTransaction
     *
     */
    public function setCountry($country)
    {
        $this->country = Earnberry::_charactersConvert($country);
        return $this;
    }

    /**
     * Sets transaction revenue amount.If not set revenue calculated on the products prices sum. Recommended to be calculated. Not required.
     *
     * @param float $total revenue amount
     *
     * @return EarnberryTransaction
     *
     */
    public function setTotal($total)
    {
        $this->total = $total;
        return $this;
    }

    /**
     * Sets transaction currency. Recommended to be set. If not set taked from the platform settings. Not required.
     *
     * @see http://www.iso.org/iso/home/standards/currency_codes.htm
     * @see https://developers.google.com/analytics/devguides/platform/currencies
     *
     * @param string $isoCode Currency ISO code.
     *
     * @return EarnberryTransaction
     *
     */
    public function setCurrency($isoCode)
    {
        $this->currency = $isoCode;
        return $this;
    }

    /**
     * Sets transaction gross profit amount. Recommended to be calculated. Not required.
     *
     * @param float $grossprofit gross profit amount
     *
     * @return EarnberryTransaction
     *
     */
    public function setGrossProfit($gp)
    {
        $this->grossprofit = $gp;
        return $this;
    }

    /**
     * Sets transaction shipping cost. At this moment not used for calculation - directly passed to GA transaction. Not required.
     *
     * @param float $cost Shipping cost.
     *
     * @return EarnberryTransaction
     *
     */
    public function setShipping($cost)
    {
        $this->shippingCost = $cost;
        return $this;
    }

    /**
     * Adds product to the order cheque.
     *
     * @param string $sku Product sku
     * @param string $name Product name
     * @param string $category Product category name
     * @param float $price Product 1 position price
     * @param integer $quantity Product position quantity
     * @param array $dimensions Custom dimenstion for Google Analytics (universal analytics only)
     * @param array $metrics Custom metrics for Google Analytics (universal analytics only)
     *
     * @return EarnberryTransaction
     *
     */
    public function addProduct($sku, $name, $category = '', $price = 0, $quantity = 1, $dimensions = array(), $metrics = array())
    {
        $product = array('sku' => Earnberry::_charactersConvert($sku), 'name' => Earnberry::_charactersConvert($name), 'category' => $category ? Earnberry::_charactersConvert($category) : '', 'price' => $price, 'quantity' => $quantity);
        if ($dimensions)
        {
            $product['dimensions'] = $dimensions;
        }
        if ($metrics)
        {
            $product['metrics'] = $metrics;
        }
        $this->productTotal = $this->productTotal + $price;
        $this->products[] = $product;
        return $this;
    }

    /**
     * Adds custom dimension to order Google Analytics hit (supported only for universal analytics)
     *
     * @param integer $index Custom dimension index. Starts from 1.
     * @param string $value Custom dimension value
     *
     * @return EarnberryTransaction.
     *
     */
    public function addCustomDimension($index, $value)
    {
        $this->dimensions[$index] = $value;
        return $this;
    }

    /**
     * Adds custom metric to order Google Analytics hit (supported only for universal analytics)
     *
     * @param integer $index Custom dimension index. Starts from 1.
     * @param integer $value Custom dimension value
     *
     * @return EarnberryTransaction
     *
     */
    public function addCustomMetric($index, $value)
    {
        $this->dimensions[$index] = $value;
        return $this;
    }

    /**
     * Gets summary for prices for all products skus in order cheque counting the quantity.
     *
     * @return float Money amount.
     *
     */
    public function getProductTotal()
    {
        return $this->productTotal;
    }

    /**
     *
     * Sets tax amount. Not used in calculation - directly passed to GA transaction. Not required.
     *
     * @param float $tax Tax amount.
     * @return EarnberryTransaction
     */
    public function setTax($tax)
    {
        $this->tax = $tax;
        return $this;
    }

    /**
     *
     * Sets dry run flag. If set to true transaction doesn't passed to GA and not appears in stats and logs in Earnberry. This flag can be used to test API server validation.
     *
     * @param boolean $onOff Flag on/off.
     * @return EarnberryTransaction
     */
    public function setDryRun($onOff)
    {
        $this->dryRun = (boolean) $onOff;
        return $this;
    }

    /**
     *
     * Sets force flag. Not recomended to use. This flag is used to force to ignore such API server validation failures: duplication of transaction order id.
     *
     * @param boolean $onOff Flag on/off.
     * @return EarnberryTransaction
     */
    public function setForce($onOff)
    {
        $this->force = (boolean) $onOff;
        return $this;
    }

    /**
     *
     * Gets constructed to array all data setted using this class setters.
     *
     * @return array Transaction data in array projection.
     */
    public function getAPIReadyData()
    {
        $apiParams =  array(
            'trackingId' => $this->trackingId,
            'cart' => $this->products,
            'dry_run' => $this->dryRun,
            'dimensions' => $this->dimensions,
            'metrics' => $this->metrics
        );

        if ($this->marker)
        {
            $apiParams['marker'] = $this->marker;
        }

        if ($this->total)
        {
            $apiParams['total'] = $this->total;
        }

        if ($this->orderId)
        {
            $apiParams['orderId'] = $this->orderId;
        }
        if ($this->force)
        {
            $apiParams['force'] = $this->force;
        }
        if ($this->tax > 0)
        {
            $apiParams['tax'] = $this->tax;
        }
        if ($this->city)
        {
            $apiParams['city'] = $this->city;
        }
        if ($this->state)
        {
            $apiParams['state'] = $this->state;
        }
        if ($this->shippingCost)
        {
            $apiParams['shipping'] = $this->shippingCost;
        }
        if (is_numeric($this->total))
        {
            $apiParams['total'] = $this->total;
        }
        if ($this->country)
        {
            $apiParams['country'] = $this->country;
        }
        if ($this->currency)
        {
            $apiParams['currency'] = $this->currency;
        }
        if (is_numeric($this->grossprofit))
        {
            $apiParams['grossprofit'] = $this->grossprofit;
        }
        return $apiParams;
    }

    /**
     *
     * Executes local data validation.
     *
     * @return bool Validation result true if all is ok.
     */
    public function validate()
    {
        $errors = array();
        $data = $this->getAPIReadyData();
        //check required params

        if (sizeof($data['cart']) == 0)
        {
            $errors[] = 'You have 0 products in cart. User ->addProduct($sku, $name, $category, $price, $quantity) method';
        }
        /**if (array_key_exists('currency', $data) && !array_key_exists($data['currency'], EarnberryTransaction::$supportedCurrencies))
        {
            $errors[] = 'Currency with isocode '.$data['currency'].' doesnot supported by GA';
        }*/
        else
        {
            foreach ($data['cart'] as $k => $product)
            {
                $requiredProductFields = array('sku' => 1, 'name' => 1, 'price' => 1, 'quantity' => 1);
                $diff = array_diff_key($requiredProductFields, $product);
                if (sizeof($diff) > 0)
                {
                    $errors[] = 'cart product with index '.$k.' missed with params '.join(',' , array_keys($diff));
                }
            }
        }
        $this->errors = $errors;
        return $errors ? false : true;
    }

    /**
     *
     * Returns errors caused during local validation.
     *
     * @return array Index-based array where every element is text of the error (english messages).
     */
    public function getValidationErrors()
    {
        return $this->errors;
    }

    /**
     *
     * Sets transaction marker. Marker used to differentiate order type (e.g. telephonic order, cart order, one-click order).
     *
     * @param string $marker Marker string.
     */
    public function setMarker($marker)
    {
        $this->marker = $marker;
    }

    /**
     *
     * Send's transaction using API client instance.
     *
     * @param string $instanceId Not required. If no instaceId - used default instance.
     * @return array Transaction execution result.
     * @throws EarnberryException if local of server validation not passed.
     */
    public function send($instanceId = false)
    {
        $i = Earnberry::getInstance($instanceId);
        return $i->conversionPush($this);
    }

    /**
     *
     * Helper method used to generate javascript of Google Analytics transaction. Don't use it in base case. Usage case - when transaction failed with network issue (we have such flag in
     * exception throwed on problem).
     *
     * @return string Javascript code.
     */
    public function generate()
    {
        $code = array();

        if (!$this->orderId)
        {
            $this->orderId = 'earnberry_'.uniqid();
        }

        $code[] = 'var _gaq = _gaq || [];';
        $code[] = "_gaq.push(['_addTrans', '".$this->orderId."', '', '".($this->total > 0 ? $this->total : $this->productTotal)."', '".$this->tax."', '".$this->shippingCost."', '".$this->city."', '".$this->state."', '".$this->country."'])";

        foreach ($this->products as $p)
        {
            $code[] = "_gaq.push(['_addItem', '".$this->orderId."', '".$p['sku']."', '".$p['name']."', '".$p['category']."', '".$p['price']."', '".$p['quantity']."'])";
        }
        if ($this->currency)
        {
            $code[] = "_gaq.push(['_set', 'currencyCode', '".$this->currency."']);";
        }
        $code[] = "_gaq.push(['_trackTrans'])";

        $js = join(';'.PHP_EOL, $code);
        if (Earnberry::getEncoding())
        {
            $js = iconv('UTF-8//IGNORE', Earnberry::getEncoding().'//IGNORE', $js);
        }
        return $js;
    }
}

class EarnberryTransactionPurchase extends EarnberryTransaction
{

    protected $pageview = array('host' => false, 'page' => false, 'title' => false);
    protected $coupon = '';
    protected $affiliation = '';

    public function setPageview($host, $page, $title)
    {
        $this->pageview['host'] = $host;
        $this->pageview['page'] = $page;
        $this->pageview['title'] = $title;
        return $this;
    }
    public function setAffiliation($aff)
    {
        $this->affiliation = $aff;
        return $this;
    }
    public function addProduct($sku, $name, $category = '', $brand = '', $variant = '', $position = '', $price = false, $quantity = false)
    {
        $this->products[] = array(
            'id' => Earnberry::_charactersConvert($sku),
            'name' => Earnberry::_charactersConvert($name),
            'category' => Earnberry::_charactersConvert($category),
            'brand' => Earnberry::_charactersConvert($brand),
            'variant' => Earnberry::_charactersConvert($variant),
            'position' => intval($position),
            'price' => $price,
            'quantity' => $quantity
        );
        return $this;
    }
    public function getAPIReadyData()
    {
        $apiData = parent::getAPIReadyData();
        $apiData['type'] = 'purchase';
        $apiData['coupon'] = $this->coupon;
        $apiData['affiliation'] = $this->affiliation;
        $apiData['pageview'] = $this->pageview;
        return $apiData;
    }
    public function validate()
    {
        return true;
    }
    public function generate()
    {
        return '';
    }

}

/**
 * Exception throwed for every problem caused in Earnberry API client.
 *
 * @author Mykhail Diordienko <infra@earnberry.net>
 * @api
 */
class EarnberryException extends Exception
{
    /**
     * Validation executed on API server failed.
     */
    const ISSUE_TYPE_BACKEND_VALIDATION = '_backend_validation';
    /**
     * Validation executed inside client failed.
     */
    const ISSUE_TYPE_LOCAL_VALIDATION = '_local_validation';
    /**
     * Communication with API server failed with network issue - timeout passed, server offline, dns problem etc.
     */
    const ISSUE_TYPE_NETWORK_PROBLEM = '_network_issue';
    protected $issueType = false;
    public function setIssueType($issueType)
    {
        $this->issueType = $issueType;
    }

    /**
     *
     * Gets failure reason type.
     *
     * @return string See consts of this class.
     */
    public function getIssueType()
    {
        return $this->issueType;
    }
}