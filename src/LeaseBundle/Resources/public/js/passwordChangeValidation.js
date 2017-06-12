var form = $('#passwordChangeForm');
var error = $('.alert-danger', form);
var success = $('.alert-success', form);

form.validate({
    errorElement: 'span',
    errorClass: 'help-inline',
    focusInvalid: false,
    ignore: ".ignore",
    rules: {
        'user_password_change[Password]':{
            required:true,
            rangelength: [6, 20]
        },
        'user_password_change[password][first]':{
            required:true,
            rangelength: [6, 20]
        },
        'user_password_change[password][second]':{
            required:true,
            equalTo: "#user_password_change_password_first"
        }
    },
    messages : {

        'user_password_change[Password]': {
            required: "তথ্য টি  অবশ্যক",
            rangelength: "পাসওয়ার্ড সর্বনিম্ন ৬ এবং সর্বোচ্চ ২০ সংখা হতে হবে "
        },
        'user_password_change[password][first]': {
            required: "তথ্য টি  অবশ্যক",
            rangelength: "পাসওয়ার্ড সর্বনিম্ন ৬ এবং সর্বোচ্চ ২০ সংখা হতে হবে "
        },
        'user_password_change[password][second]': {
            required: "তথ্য টি  অবশ্যক",
            equalTo: "পাসওয়ার্ড মিলছে না"
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
    }




});