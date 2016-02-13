<?php 

namespace MobileOptin\Classes;

class Apishoppingcart 
{
	protected $_merchantId = null;
	protected $_merchantKey = null;
	protected $_apiUri = null;
	protected $_apiParameters ;
	protected static $_instance = null;
	
	/** access restrictted to a constructor need to use getInstance() */
	function __construct($merchantId="", $merchantKey="", $apiUri="https://www.mcssl.com")
    {
        $this->_merchantId  = $merchantId;
		$this->_merchantKey = $merchantKey;
		$this->_apiUri      = $apiUri;
    }
	
	/** Allow to create a unique instance of Apishoppingcart */
	public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function setMerchandId($merchantId){
    	if(empty($merchantId)) return $this;
    	$this->_merchantId = $merchantId;
    	return $this;
    }
   
    public function setmerchantKey($merchantKey){
    	if(empty($merchantKey)) return $this;
    	$this->_merchantKey = $merchantKey;
    	return $this;
    }

    public function setapiUri($apiUri){
    	if(empty($apiUri)) return $this;
		$this->_apiUri = $apiUri;
		return $this;
    }

     public function getMerchandId(){
    	return  $this->_merchantId;
    }

     public function getmerchantKey(){
    	return $this->_merchantKey;
    }

	
    /** Allow to add parameter to a request */
	function AddApiParameter($parameterKey, $parameterValue)
	{
		if (@array_key_exists($parameterKey, $this->_apiParameters)) 
		{
			$this->_apiParameters[$parameterKey] = NULL;
		}                                      
		$this->_apiParameters[$parameterKey] = $parameterValue;
	}
    	
    
    /** Clear ApiPrameters */
	function ClearApiParameters()
	 {
		$this->_apiParameters = NULL;	
	 }
    
    /**
	 *	This method uses the curl object to make
     * 	a POST request to the api and return the response
     * 	from the API
	 */
	function SendHttpRequest($uri, $request_body) 
	{		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $uri);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $request_body);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-POST_DATA_FORMAT: xml'));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); # TODO - SET THIS TO true FOR PRODUCTION
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$data = curl_exec($ch);
		$err = curl_error($ch);
		curl_close($ch);
		if ($err) 
			return $err;
		return $data;
	}
	
    /**
	 * This method will call the SendHttpRequest method
     * after appending the proper information to the uri
     * and creating the request body
	 */
	function ApiRequest($path, $parameters = "") 
	{		
		$uri = $this->_apiUri."/API/".$this->_merchantId.$path;			
		$request_body = $this->CreateRequestString();		
		$result = $this->SendHttpRequest($uri, $request_body);
				
		return($result);		
	}
	
    /**
 	 * This method will take a properly formatted api uri
     * and create the response body then call the http request method
	 */
	function XLinkApiRequest($xlink, $parameters = "") 
	{				
		$request_body = $this->CreateRequestString();		
		$result = $this->SendHttpRequest($xlink, $request_body);		
		return($result);		
	}
	
	function CreateRequestString()
	{
		$request_body = "<Request><Key>".$this->_merchantKey."</Key>".$this->ParseApiParameters($this->_apiParameters)."</Request>";
		return $request_body;
	}
	
	function ParseApiParameters($parameters) 
	{		
		$request_payload = "";
		if ((!empty($parameters)) && (is_array($parameters))) 
		{
			foreach($parameters as $key => $value) 
			{
				if (!is_array($value)) 
				{
					$request_payload .= ("<".$key.">".$value."</".$key.">\r\n");
				}
				 else 
				 {
					$request_payload .= "<".$key.">\r\n";
					$request_payload .= $this->create_request($value);
					$request_payload .= "</".$key.">\r\n";
				}
			}
		}
		return $request_payload;
	}
	
	function GetOrdersList()
	{
		return($this->ApiRequest("/ORDERS/LIST"));
	}
		
	function GetOrderById($orderId)
	{
		return($this->ApiRequest("/ORDERS/" . $orderId . "/READ"));
	}

	function GetProductsList()
	{
		return($this->ApiRequest("/PRODUCTS/LIST"));
	}
	
	function GetProductById($productId)
	{
		return($this->ApiRequest("/PRODUCTS/" . $productId . "/READ"));		
	}
	
	function GetClientsList()
	{
		return($this->ApiRequest("/CLIENTS/LIST"));
	}
	 
	function GetClientById($clientId)
	{
		return($this->ApiRequest("/CLIENTS/". $clientId ."/READ"));
	}
	
	function GetErrorsList()
	{
		return($this->ApiRequest("/ERRORS/LIST"));
	}
	
	function GetAvailableApiMethods()
	{
		return($this->ApiRequest(""));
	}

	function NotificationTest()
	{
		return($this->ApiRequest("/Notifications/TEST"));
	}
}
?>