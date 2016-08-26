var CaptchaCallback = function() {
  	var key = captcha_globals.site_key;
  	$recaptchas = $('.google-recaptcha-insert');
  	if($recaptchas.length > 0){
    	$recaptchas.each(function(){
      		$(this).html('');
      		grecaptcha.render($(this).attr('id'), {'sitekey' : key});
    	});
  	}
};
