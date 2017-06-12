var form = $('#ProfileEditForm');
var error = $('.alert-danger', form);
var success = $('.alert-success', form);

form.validate({
    errorElement: 'span',
    errorClass: 'help-inline',
    focusInvalid: false,
    ignore: ".ignore",
    rules: {
        'user_registration[nationalId]':{
            pattern: /^(\d{13}|\d{17})$/
        },
        'user_registration[profile][fullname]':{
            required:true
        },
        'user_registration[profile][fatherName]':{
            required:true
        },
        'user_registration[profile][motherName]':{
            required:true
        },
        'user_registration[profile][address]':{
            required:true
        },
        'user_registration[profile][file]':{
            required:false
        },
        'user_registration[email]':{
            email: true
        },
        'user_registration[phoneNumber]':{
            required:true
        }


    },
    messages : {
        'user_registration[nationalId]': "নম্বর টি সঠিক নয় ",
        'user_registration[profile][fullname]': "তথ্য টি  অবশ্যক",
        'user_registration[profile][fatherName]': "তথ্য টি  অবশ্যক",
        'user_registration[profile][motherName]': "তথ্য টি  অবশ্যক",
        'user_registration[profile][address]': "তথ্য টি  অবশ্যক",
        'user_registration[email]': {
            email: "অনুগ্রহ করে একটি বৈধ ইমেইল লিখুনা",
        },
        'user_registration[phoneNumber]': "তথ্য টি  অবশ্যক"

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