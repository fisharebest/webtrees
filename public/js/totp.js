$( document ).ready(function() {
// resize the qr code and hide as default in edit user page
 $('div#qrcode').css('maxWidth', '300px');
 $('div#qrcode').hide();	

// show a link to get another qr code if 2fa enabled	
 if($('input#status-mfa-1').is(':checked')) {
    $('input#status-mfa-1').parent().append("<span> <a href='#' id='getnewqr'>&nbsp;-&nbsp;(Click to generate new QR code)</a> </span>")	 
 }

// click to get new qr code and secret	
$("a#getnewqr").click(function(){
  $('div#qrcode').show();	
  $("a#getnewqr").hide();
  $('input#secret').val($('input#newsecret').val())
});	

// deal with toggling of 2fa setting to ensure no secret saved if no 2fa required but the secret associated with any generated qr code is saved.	
 $('input#status-mfa-1').change(function() {
    if(this.checked) {
      $(this).parent().append("<span> <a href='#'>&nbsp;-&nbsp;(Click to generate new QR code)</a> </span>")
      $('div#qrcode').show();	
      $('input#secret').val($('input#newsecret').val())
    }
    else {
      $('div#qrcode').hide();	
      $("a#getnewqr").hide();
      $('input#secret').val('');	    
    }
 });
});
