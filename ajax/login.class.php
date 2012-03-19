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

                  $params = array('http' => array('method'=>'POST', 'content'=> $data));

                  $ctx = stream_context_create($params);

                  $fp = fopen($url, 'rb', false, $ctx);

                  if($fp) {

                    return stream_get_contents($fp);

                  } else {

                    return false; 
                  }
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

                 $result = $this->_requestPOST(self::endpoint, $params);

                 $output = json_decode($result,true);

                 //for debug
                 #print_r($output);

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