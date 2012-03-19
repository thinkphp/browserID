BrowserID
=========

BrowserID is a new way for users to log into web sites using their email address. It aims to provide a secure way of proving your identity to servers across the internet, 
without having to create separate usernames and passwords each time. Instead of a new username, it uses your email address as you identity which allows it to be 
descentralized since anyone can send you an email verification message.

![Screenshot](http://farm8.staticflickr.com/7065/6844698494_93723f56bc_z.jpg)

![Screenshot](https://developer.mozilla.org/@api/deki/files/6040/=browserid-remote-verify.png)

How to Use
----------

Include the BrowserID include.js library in your site by adding the following script tag to your pages:

       #head
       <script src="https://browserid.org/include.js" type="text/javascript"></script>


       #JS
       addEvent(butt,'click',function(){

           navigator.id.getVerifiedEmail(function(assertion) {

                if(assertion) {

                   verify(assertion);

                } else {

                   alert('I still don\'t know you...');
                }
           });

       },false)

       function verify(assertion) {

         var params = 'assertion=' + assertion,

             url = 'login.php';

             asyncRequest.REQUEST("POST",url,function(data){

                  var response = JSON.parse(data),

                      p = document.createElement('p');

                      p.innerHTML = 'Welcome, ' + response.email;

                      butt.parentNode.replaceChild(p,butt);

                      $('result').innerHTML = 'WoW, I know you!';

             }, params);
       }

       #PHP
       require_once('login.class.php');
 
       $browserID = new BrowserID($_SERVER['HTTP_HOST'], $_POST['assertion']);

       if($browserID->verify_assertion()) {
 
             echo json_encode(array('status'=>'okay', 'email'=>$browserID->getEmail()));

       } else {

             echo json_encode(array('status'=>'failure'));
       }