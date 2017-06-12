$(function () {

    var userGroup = $('#user_groups');
    userGroup.multiSelect({
        afterSelect: function(val){
            userGroup.multiSelect('deselect_all');
            userGroup.find('option[value='+val[0]+']').attr('selected', true);
            userGroup.multiSelect('refresh');
        }
    });

    $('#user_profile_dob,#user_profile_joiningDate,#user_profile_joiningDateOfCurrentOffice').datepicker({
        rtl: Metronic.isRTL(),
        orientation: "center",
        todayHighlight: true,
        language: 'bn',
        format: 'dd-mm-yyyy'
    });

    $('form[name=user]').validate({
        rules : {
            'user[plainPassword][first]' : {
                minlength : 6
            },
            'user[plainPassword][second]' : {
                minlength : 6,
                equalTo : "#user_plainPassword_first"
            },
            'user[profile][nid]' : {
                digits: true,
                minlength: 13,
                maxlength: 13
            },
            'user[username]': {
                required: true,
                remote: "/user/check/username"
            },
            'user[email]': {
                required: true,
                email: true,
                remote: "/user/check/email"
            }
        },
        messages: {
            'user[username]': {
                remote: 'এই ব্যবহারকারীর নাম ইতিমধ্যে ব্যাবহার করা হচ্ছে'
            },
            'user[email]': {
                remote: 'এই ইমেইলটি ইতিমধ্যেই ব্যাবহার করা হচ্ছে'
            }
        },
        lang: 'bn_BD',
        errorElement: 'span', //default input error message container
        errorClass: 'help-block help-block-error', // default input error message class
        focusInvalid: false, // do not focus the last invalid input
        ignore: "",  // validate all fields including form hidden input

        highlight: function (element) {
            $(element)
                .closest('.form-group').addClass('has-error');
        },

        unhighlight: function (element) {
            $(element)
                .closest('.form-group').removeClass('has-error');
        },

        submitHandler: function (form) {
            form.submit();
        },
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length || element.prop('type') === 'checkbox' || element.prop('type') === 'radio') {
                error.insertAfter(element.parent().parent().parent());
            } else {
                error.insertAfter(element);
            }
        }
    });

    $('form[name=change-password]').validate({
        rules : {
            'fos_user_change_password_form[current_password]' : {
                minlength : 6
            },
            'fos_user_change_password_form[plainPassword][first]' : {
                minlength : 6
            },
            'fos_user_change_password_form[plainPassword][second]' : {
                minlength : 6,
                equalTo : "#fos_user_change_password_form_plainPassword_first"
            }
        },
        lang: 'bn_BD',
        errorElement: 'span', //default input error message container
        errorClass: 'help-block help-block-error', // default input error message class
        focusInvalid: false, // do not focus the last invalid input
        ignore: "",  // validate all fields including form hidden input

        highlight: function (element) {
            $(element)
                .closest('.form-group').addClass('has-error');
        },

        unhighlight: function (element) {
            $(element)
                .closest('.form-group').removeClass('has-error');
        },

        submitHandler: function (form) {
            form.submit();
        },
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length || element.prop('type') === 'checkbox' || element.prop('type') === 'radio') {
                error.insertAfter(element.parent().parent().parent());
            } else {
                error.insertAfter(element);
            }
        }
    });
});