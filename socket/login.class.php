<?php
    /**
     *  Simple implementation of Mozilla BrowserID
     */

    class BrowserID {
          
          /**
           *  The browserID's assertion verification service endpoint 
           */
          const endpoint = 'https://browserid.org/verify';

          /**
           *  
           */
          private $assertion;

          /**
           *  The hostname and optional port of your site 
           */
          private $audience;

          /**
           *  The email address of the user
           */
          private $email;

          /**
           *  Expiration timestamp of the assertion
           */
          private $expires;

          /**
           *  The entity who issued the assertion
           */
          private $issuer;

          /**
           * The constructor of class
           * @public access
           */
          public function __construct($audience, $assertion) {
     
                 //init
                 $this->audience = $audience;
                 $this->assertion = $assertion;
          }  


          /**
           * Get email address of the user
           * @param None
           * @return String return email address
           * @public access
           */
          public function getEmail() {

                 return $this->email;
          }

          /**
           * Get expiration timestamp
           * @param None
           * @return integer expiration timestamp
           * @public access
           */
          public function getExpires() {

                 return $this->expires;
          }

          /**
           * Get the entity who issued the assertion 
           * @param None
           * @return String the entity who issued the assertion 
           * @public access
           */
          public function getIssuer() {

                 return $this->issuer;
          }


          /**
           * Makes an HTTP POST Request to verification endpoint
           * @param   String Endpoint
           * @param   Array the data to be sent to the endpoint
           * @return  Object returns an object verification response
           * @private access
           */
          private function _requestPOST($url, $data) {

                  $url = parse_url($url);

                  //extract host and path
                  $host = $url['host'];

                  $port = 80;

                  $path = $url['path']; 

                  $fp = fsockopen($host,$port,$errno,$errstr,30);

                  if($fp) {

                     fputs($fp, "POST $path HTTP/1.1\r\n");
                     fputs($fp, "HOST: $host\r\n");
                     fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
                     fputs($fp, "Content-length: ". strlen($data). "\r\n");
                     fputs($fp, "Connection: close\r\n\r\n:");
                     fputs($fp,$data);

                     $result = '';
                     while(!feof($fp)) {

                         $result .= fgets($fp, 128);
                     }
                  } else {
                     print_r(array('status'=> 'err', 'error'=> "$errstr($errno)"));
                  }

                  fclose($fp);

                  $result = explode("\r\n\r\n", $result, 2);
echo"<pre>";
print_r($result);
echo"</pre>";
          }

          /**
           * With this method you must verify the assertion is authentic and extract the email address from it.
           * @public access
           * @return Object - returns an object as response from service with the following attributes:
           *                  1)status   Okay 
           *                  2)email    mergesortv@gmail.com
           *                  3)audience https://mysite.com
           *                  4)expires  1308859352261
           *                  5)issuer   "browserid.org"
           */  
          public function verify_assertion() {
 
                 $params = http_build_query(array('assertion'=>$this->assertion,
                                                  'audience'=>$this->audience));  

                 $output = $this->_requestPOST(self::endpoint, $params);

                 //for debug
                 print_r($output);

                 if(isset($output['status']) && $output['status'] == 'okay') {

                    $this->email   = $output['email']; 
                    $this->expires = $output['expires'];
                    $this->issuer  = $output['issuer'];

                   return true;

                 } else {

                   return false;
                 }
           }
    }

?>