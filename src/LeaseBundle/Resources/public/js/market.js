var market= $('#market_form');
var error = $('.alert-danger', market);
var success = $('.alert-success', market);

market.validate({
    errorElement: 'span', //default input error message container
    errorClass: 'help-inline', // default input error message class
    focusInvalid: false, // do not focus the last invalid input
    ignore: ".ignore",
    rules: {
        'market[marketName]': {
            required:true
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
        if(element.attr('class') == 'form-control phoneNo form-control') {
            element.parent('.input-group').after(error);
        } else if(element.attr('class') == 'date2 form-control') {
            element.parent('.input-group').after(error);
        }  else {
            error.insertAfter(element);
        }
    }

});

