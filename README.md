BrowserID
=========

![Screenshot](http://farm8.staticflickr.com/7065/6844698494_93723f56bc_z.jpg)

![Screenshot](https://developer.mozilla.org/@api/deki/files/6040/=browserid-remote-verify.png)

How to Use
----------

Include the BrowserID include.js library in your site by adding the following script tag to your pages:

       <script src="https://browserid.org/include.js" type="text/javascript"></script>


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