document.addEventListener("touchstart", function() {}, false);
(function($) {
    "use strict";
    $(function() {
        var randNumber_1 = parseInt(Math.ceil(Math.random() * 15), 10);
        var randNumber_2 = parseInt(Math.ceil(Math.random() * 15), 10);
        humanCheckCaptcha(randNumber_1, randNumber_2);
    });

    function humanCheckCaptcha(randNumber_1, randNumber_2) {
        $("#humanCheckCaptchaBox").html("Solve The Math ");
        $("#firstDigit").html('<input name="mathfirstnum" id="mathfirstnum" class="form-control" type="text" value="' + randNumber_1 + '" readonly>');
        $("#secondDigit").html('<input name="mathsecondnum" id="mathsecondnum" class="form-control" type="text" value="' + randNumber_2 + '" readonly>');
    }
    
	$("#contactForm").validator().on("submit", function(event) {
		var parametros =$(this).serialize();
        if (event.isDefaultPrevented()) {
            formError();
            submitContactFormActionMSG(false, "Por favor completa el formulario correctamente!");
        } else {
           			
                event.preventDefault();
                $.ajax({
                    type: "POST",
                    url: "formularios/process.php",
                    data: parametros,
                    success: function(recibe) {
					 var datos =  $.parseJSON(recibe); 
					    if (datos[0].sucedio == 1) {
                    		contactFormSuccess();
							
                        } else {
                            formError();
                            submitContactFormActionMSG(false, text);
                        }
                    }
                });
            
        }
    });

    function submitContactFormActionMSG(valid, msg) {
        if (valid) {
            var msgClasses = "h3 text-center text-success col-md-12";
        } else {
            var msgClasses = "h3 text-center text-danger col-md-12";
        }
        $("#msgContactSubmit").removeClass().addClass(msgClasses).text(msg);
			
        return false;
		
    }

    function contactFormSuccess() {
        submitContactFormActionMSG(true, "Los datos han sido enviados con éxito!");
    }



    function formError() {
        $(".help-block.with-errors").removeClass('hidden');
    }
})(jQuery);