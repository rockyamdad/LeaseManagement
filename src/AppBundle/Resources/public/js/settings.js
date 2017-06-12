$(function() {

    if ($('#dc_office_setting').length) {

        $('#form-settings-update').validate({
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

    if ($("#union-update-page").length) {

        $("#appbundle_union_district").change(function () {

            var id = $(this).val();
            console.log(id);
            if (id == '') {
                return false;
            }
            $.ajax({

                url: Routing.generate('district_upozilas', {id: id}),
                dataType: 'json'
            }).done(function ($msg) {

                $('#appbundle_union_upozila option').remove();
                $('#appbundle_union_upozila').append('<option value="">নির্বাচন করুণ</option>');

                var totalOption = $msg.length;
                for (var i = 0; i < totalOption; i++) {
                    $('#appbundle_union_upozila').append($msg[i]);
                }
            });
        });
    }
});