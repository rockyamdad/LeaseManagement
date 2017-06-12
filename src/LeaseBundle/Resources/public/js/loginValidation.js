var form = $('#loginform');
var error = $('.alert-danger', form);
var success = $('.alert-success', form);

var telInput = $("#user_login_phoneNumber"),
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
// on blur: validate
telInput.blur(function() {
    reset();
    if ($.trim(telInput.val())) {
        var intlNumber = telInput.intlTelInput("getNumber");
        if (intlNumber) {
            telInput.val(intlNumber);
        }
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
        'user_login[password]':{
            required:true,
            rangelength: [6, 20]
        },
        'user_login_phoneNumber':{
            required:true,

        }
    },
    messages : {
        'user_login[password]': {
            'required':'তথ্য টি  আবশ্যক',
            'rangelength': "পাসওয়ার্ড সর্বনিম্ন ৬ এবং সর্বোচ্চ ২০ সংখা হতে হবে "

        },
        'user_login_phoneNumber': {
            'required':'তথ্য টি  আবশ্যক'

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
        if ($(element).attr('id') == 'user_login_phoneNumber') {
            element.parents('.input-group').after(error);
        } else {
            error.insertAfter(element);
        }
    }


});