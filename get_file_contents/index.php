<?php

    if(!empty($_POST)) {

        echo"<html><body>";

        $result = verify_assertion($_POST['assertion'],'https://browserid.org/verify');

        if($result->status == 'okay') {

          echo"<p>Logged In as : " . $result->email . "</p>"; 

        } else {

          echo"<p>Error: " . $result->reason . "</p>"; 
        }

        echo"<a href=\"index.php\">Back to login page</a>";
        echo"</body></html>";

    } else {
      
      display_login_form();  
    }


    function display_login_form() {

       echo <<<form
<html>
<head>
   <style type="text/css">
    h1,h2,h3,body { font-family:'gill sans','dejavu sans',verdana,sans-serif; }
    h1 { font-family:menlo,'dejavu sans mono',monospace; }
    h1 {
      font-weight:bold;
      font-size:43px;
      letter-spacing: -1px;
      color:#000;
      margin-bottom:0;
      position:relative;
    }
   button{border:none;background:transparent;margin:0 2em;}
   </style>
<script src="https://browserid.org/include.js"></script>
<script type="text/javascript">
var $ = function(id) {return document.getElementById(id);},
addEvent = function(elem,evType,fnHandler,useCapture){

             if(elem.addEventListener) { 
                return elem.addEventListener(evType,fnHandler,useCapture); 
             } else if(elem.attachEvent){
                return elem.attachEvent('on'+evType,fnHandler);
             } else {
                elem['on'+evType] = fnHandler;
             }
}
window.onload = function(){
    addEvent($('login'),'click',function(){
           navigator.id.get(function(assertion){

                     if(assertion) {

                        var assertion_field = $('assertion')
                            assertion_field.value = assertion;
                        var f = $('login-form');
                            f.submit();  
 
                     } else {
                       alert('I don\'t know you!');
                     }
           });
    },false);
}
</script>
</head>
<body>
<h1>BrowserID</h1>
<form id="login-form" method="POST" action="index.php">
<input id="assertion" type="hidden" name="assertion" value="">
</form>
<button id="login" type="submit"><img alt="sign in with browser ID" src="https://browserid.org/i/sign_in_blue.png"></button>
</body>
</html>
form;
    }

    /**
     * Verifies the given BrowserID assertion
     * @throws Exception onError
     *
     * @param String $assertion Assertion provided by navigator.id.getVerifiedEmail() 
     * @param String $audience Host name, defaults to $_SERVER['HTTP_HOST']
     * @param String $service URL of the verification service, defaults to 'https://browserid.org/verify'
     * @return Object the decoded JSON returned by the verify service. 
     */
    function verify_assertion($assertion, $service, $audience=NULL) {

            if(NULL == $audience) {
                    $audience = $_SERVER['HTTP_HOST'];
            }

            $params = http_build_query(array('audience'=>$audience,'assertion'=>$assertion));

            $options = array('http'=>array(
                     'content' => $params,
                     'header'  => 'Content-Type: application/x-www-form-urlencoded',
                     'method'  => 'POST'  
            )); 

            $ctx = stream_context_create($options);

            $result = file_get_contents($service, false, $ctx);

            if(!$result) {
                throw new Exception(sprintf('Verify Service %s did not return a response',$service));
            } 

            $json = json_decode($result);

            if(isset($json->status) && $json->status == 'failure') {

               throw new Exception(sprintf('The verify service %s returned an error: %s',$service,$json->reason));
            }   

        return $json; 
    }
?>