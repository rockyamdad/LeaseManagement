var form = $('#forgetPassword');
var error = $('.alert-danger', form);
var success = $('.alert-success', form);

var telInput = $("#mobile"),
    errorMsg = $("#error-msg"),
    validMsg = $("#valid-msg");

// initialise plugin
var reset = function() {
    telInput.removeClass("error");
    telInput.closest('.form-group').removeClass('has-success');
    telInput.closest('.form-group').removeClass('has-error');
    errorMsg.addClass("hide");
    validMsg.addClass("hide");
};

form.on('click', function () {
    var intlNumber = telInput.intlTelInput("getNumber");
    if (intlNumber) {
        telInput.val(intlNumber);
    }
});
telInput.blur(function() {
    reset();
    if ($.trim(telInput.val())) {

        if (telInput.intlTelInput("isValidNumber")) {
            telInput.closest('.form-group').removeClass('has-error').addClass('has-success');
            validMsg.removeClass("hide error").addClass("has-success");
        } else {
            telInput.addClass("error");
            telInput.closest('.form-group').removeClass('has-success').addClass('has-error');
            errorMsg.removeClass("hide");
        }
    }
});

// on keyup / change flag: reset
telInput.on("keyup change", reset);


form.validate({
    errorElement: 'span',
    errorClass: 'help-inline',
    focusInvalid: false,
    ignore: ".ignore",
    rules: {
        'mobile':{
            required:true
        }
    },
    messages : {

        'mobile': {
            required: "তথ্য টি  অবশ্যক"
        }
    },

    invalidHandler: function (event, validator) { //display error alert on form submit
        success.hide();
        error.show();
        Metronic.scrollTo(error, -200);
    },

    highlight: function (element) { // hightlight error inputs
        $(element)
            .closest('.form-group').removeClass('has-success').addClass('has-error'); // set error class to the control group
    },

    unhighlight: function (element) { // revert the change done by hightlight
        $(element)
            .closest('.form-group').removeClass('has-error'); // set error class to the control group
    },

    success: function (label) {
        label
            .addClass('valid') // mark the current input as valid and display OK icon
            .closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
    },
    errorPlacement: function(error, element) {
        if ($(element).attr('name') == 'mobile') {
            element.parents('.input-group').after(error);
        } else {
            error.insertAfter(element);
        }
    }




});