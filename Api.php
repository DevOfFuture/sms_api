<?php
/**
* This class controls everything
* @author: Onwuka Gideon <dongidomed@gmail.com> <dongido>
* @package: HostNowNow.com Bulk Sms Api
* @idea: This was burn Out just to make bulk sms sending easy ...
* I plan to expand this to be more Dynamic if i have time,,,,
**/

class Api{

    public $cred = array("dongido", "dongidoB00s"); //this is hard-codded Api password and must be changed!!!

	  protected $config; //message config

  	protected $urlencode = true; //encodes data to be sent via url

    public $default =  [
            "msg" => "This is dongido))",
            "from" => "dongido",
            "to" =>  "08059794251",
            "token" => "gdfgdfgdfgdfgdfg34534534523ffgdfgdfg4t3tert",
            "url" => "http://txtconnect.co/api/send/",
          ];

    //this the message of the response from the API
    public $smsMessage = array(
        'X000' => "Token is missing [Client error]",
        'X002' => "Invalid token id [Client error]",
        'X003' => "Voucher code or pin missing [Client error]",
        'X004' => "Invalid voucher code or pin [Client error]",
        'X005' => "Something went wrong recharging your account [Server error]",
        'X006' => "Invalid date specified [Client error]",
        'X007' => "Something went wrong fetching sms history [Server error]",
        'X008' => "Message id is missing [Client error]",
        'X009' => "Incomplete send parameters [Client error]",
        'X010' => "Incomplete or Invalid send parameters [Client error]",
        'X011' => "Something went wrong fetching sending your sms [Server error]",
        'X012' => "Insufficient sms balance [Client error]",
        'X013' => "Incomplete or Invalid schedule parameters [Client error]",
        'X014' => "Something went wrong scheduling your sms [Server error]",
        'X015' => "Incomplete or Invaelid stop parameters [Client error]",
        'X016' => "Something went wrong stopping your schedule [Server error]",
        'X017' => "Invalid schedule id [Client error]",
        'X001' => "Something went wrong verifying your token id [Server error]",
        '0'     => "sent",
      );

    /**
    * @param: file name (Optional)
    * @return: array
    * Gets config from a file
    */
	public function getConfig($pointer = 'config.txt'){
        if(file_exists($pointer)){
             return json_decode(file_get_contents($pointer), true);
        }
        else{
        	return [];
        }
	}

   /**
   * ecodes url
   * @author: Onwuka Gideon <dongidomed@gmail.com> <dongido>
   * @version: 1.0.0
   * @return : Text
   **/
   public function useUrlEncode($text, $value = true){
       return ($value) ? urlencode($text) : $text ;
   }

   /**
   *
   * @return: nothing
   * stops url encoding ...
   */
   public function stopUrlencodding(){
   	   $this->urlencode = false;
   }

   public function prepare(){
   	   $this->config  = array_merge($this->smsMessage, $this->getConfig($config_file));
   	   $this->config  = array_merge($this->smsMessage, $this->prepareMessage());
   }


    public function prepareMessage(){
        $fields = $this->getValues();

        if( isset($fields['msg']) AND isset($fields['from']) AND isset($fields['to'])){
                return array(
                      "msg" => $fields['msg'],
                      "from" => $fields['from'],
                      "to" => $fields['to'],
                	);
        }
        die( "Url request is incomplete" );
    }

   public function getValues(){ // Noramlly from the Url
        return ($_GET);
   }

   /**
   *
   * @return: nothing
   * checks if username and password is correct ...
   */
   public function checkUser(){
   	   $detail = $this->getValues();
   	   if (@$detail['username'] !== $this->cred[0] OR @$detail['password'] !== $this->cred[1]){
            die(" Your Access to this Api is forbidden!!!");
   	   }
   }

   /**
   *
   * @return: string
   * merge request withe the stored one already ...
   */
   public function prepareRequest($fields){
	   $fields = array_merge($fields, $this->prepareMessage());
	   //var_dump($fields); die();
   	    $postvars = "";
        foreach($fields as $key=>$value) {
              $postvars .= $key . "=" . $value . "&";
        }
        return $postvars;
   }


       /**
       *
       * @return: string
       * Make the request to the API
       */
   public function getResult($url, $fields){
      $this->checkUser();
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
      curl_setopt($ch, CURLOPT_POST, count($fields));
      curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      $result = curl_exec($ch);
      curl_close($ch);
      $data = json_decode($result, true);

      if( (isset($data["error"]) or array_key_exists($data["error"], $data))
      	                              AND
      	   (isset($data["response"]) or array_key_exists($data["response"], $data))){
               return $this->smsMessage[ $data["error"] ];
         } else{
         	 return "Complete Error was encountered!!, you must contact the developer :)";
         }

   }

}
?>
