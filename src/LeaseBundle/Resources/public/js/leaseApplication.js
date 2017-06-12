var leaseApplication= $('#leaseApplicationFrom');
var error = $('.alert-danger', leaseApplication);
var success = $('.alert-success', leaseApplication);

$("#verify").on("click",function(){

    var phoneNumber = $('#leasebundle_application_phoneNo').val();
    $.ajax(
        {
            type: "POST",
            url: Routing.generate('portal_phoneNumber_verification_code_send'),
            data: {'phoneNumber':phoneNumber},
            dataType: "json",
            success:function( html ) {
                console.log(html);
                alert('আপনার ফোন নম্বরে যাচাইকরণ কোড  পাঠানো হয়েছে!');
                $("#add-applicant-info").removeClass("hide");
                $("#verify").addClass("hide");
            }
        }
    );

});
var telInput = $(".phoneNo"),
    errorMsg = $(".error-msg"),
    validMsg = $(".valid-msg");

// initialise plugin
var reset = function() {
    telInput.removeClass("error");
    telInput.closest('.form-group').removeClass('has-success');
    telInput.closest('.form-group').removeClass('has-error');
    errorMsg.addClass("hide");
    validMsg.addClass("hide");
    $("#code").removeClass("success");
    $("#code").val("");
    $("#verify").removeClass("hide");
    $("#add-applicant-info").addClass("hide");
};
leaseApplication.on('click', function () {
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
            $("#code").removeClass("hide");
        } else {
            telInput.addClass("error");
            telInput.closest('.form-group').removeClass('has-success').addClass('has-error');
            errorMsg.removeClass("hide");
        }
    }
});

// on keyup / change flag: reset
telInput.on("keyup change", reset);
telInput.intlTelInput({
    utilsScript: "/assets/plugins/intl-tel-input-master/build/js/utils.js",
    nationalMode: true,
    preferredCountries: ['bd', 'in']
});

$('.select2', leaseApplication).change(function () {
    leaseApplication.validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input
});
var _sms_verified = false;
leaseApplication.validate({
    errorElement: 'span', //default input error message container
    errorClass: 'help-inline', // default input error message class
    focusInvalid: false, // do not focus the last invalid input
    ignore: ".ignore",
    rules : {
        'nid': {
            required:true
        },
        'photo': {
            required:true
        },
        'trade': {
            required:true
        },
        'audit': {
            required:true
        },
        'resulation': {
            required:true
        },
        'nibondhon': {
            required:true
        },

        'verification_code': {
            remote: {
                type: 'post',
                url: Routing.generate('portal_phoneNumber_verification_code_confirmation'),
                data: {
                    'code':function () {
                        return $('#code').val();
                    },
                    'phoneNumber':function () {
                        return $('#leasebundle_application_phoneNo').val();
                    }
                }
            }
        }
    },
    messages:{
        'verification_code': 'আপনার যাচাইকরণ কোড সঠিক নয়'
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

    success: function (label, element) {
        if (element.id == 'code' && !_sms_verified) {
             _sms_verified = true;
            ////
            alert('যাচাইকরণ সফল হয়েছে');
        }

        label
            .addClass('valid') // mark the current input as valid and display OK icon
            .closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
    },
    errorPlacement: function(error, element) {
        if(element.attr('id') == 'code') {
            _sms_verified = false;
        }
        if(element.attr('class') == 'form-control phoneNo form-control') {
            element.parent('.input-group').after(error);
        } else if(element.attr('class') == 'date2 form-control form-control') {

        } else if(element.attr('class') == 'form-control phoneNo form-control error') {
            element.parent('.input-group').after(error);
        } else{
            error.insertAfter(element);
        }
        if (element.prop("type") === "file") {
            error.insertAfter(element.parent().parent());
        }
    },
    submitHandler: function(form) {
        $(form)[0].submit();
    }

});
