$(function() {

    if ($("#upozila-update-page").length) {

        $('#upozila-update-form').validate({
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
            }
        });
    }

    if ($("#mouza-update-page").length) {

        $('#mouza-update-form').validate({
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
            }
        });

        $('#porcha_processing_mouza_upozila').change(function() {
            if ($(this).val() != '') {
                $('#porcha_processing_mouza_thana').select2("val", "");
            }
        });

    }

});