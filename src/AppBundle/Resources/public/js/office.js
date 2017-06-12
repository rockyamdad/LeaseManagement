$(function() {

    if ($('#office-list-page').length) {

        $("#district").change(function () {
            AppUtil.fetchData($(this).attr('data-url'));
            return false;
        });

        $('.table-data').on('click', '#chk-all', function() {
            if ($("#chk-all").is(':checked')) {
                $("input:checkbox").prop('checked', true);
            } else {
                $("input:checkbox").prop('checked', false);
            }
        });

        $("#btn-form-multi-action-office").click(function (e) {

            if ($('#selectedChk').val() == '') {
                alert('অবস্থা নির্বাচন করুন ');
                return false;
            }

            if ($('.chk:checked').length < 1) {
                alert('অফিস নির্বাচন করুন ');
                return false;
            }

            e.preventDefault();
            if (!confirm('আপনি কি নিশ্চিত ?')) {
                return false;
            }

            var data = $('#form-multi-action-office').serializeArray();

            $.post( Routing.generate('office_status_change'), data)
                .done(function( response ) {
                    $(".table-data").html(response);
                    alert('কাজটি সম্পন্ন হয়েছে ')
                });

            return false;
        });
    }


    if ($('#office-update-page').length) {

        $('#form-update-office').validate({
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

        $("#appbundle_office_parent").change(function() {

            $('#appbundle_office_upozila').select2("val", "");

            var id = $(this).val();
            if(id == '') {
                return false;
            }
            $.ajax({

                url:Routing.generate('office_upozilas',{id: id}),
                dataType: 'json'
            }).done(function ($msg) {

                $('#appbundle_office_upozila option').remove();
                $('#appbundle_office_upozila').append('<option value="">নির্বাচন করুণ</option>');

                var totalOption = $msg.length;
                for (var i = 0; i < totalOption; i++) {
                    $('#appbundle_office_upozila').append($msg[i]);
                }
            });
        });
    }


    if ($('#holiday-update-page').length) {

        $("#appbundle_holiday_type").change(function(){
            $(this).find("option:selected").each(function(){
                if($(this).attr("value")=="GOV_LEAVE"){
                    $(".holiday").not(".govt").hide();
                    $(".govt").show();
                }
                else if($(this).attr("value")=="CEO_LEAVE"){
                    $(".holiday").not(".govt").hide();
                    $(".govt").show();
                }
                else if($(this).attr("value")=="WEEK_LEAVE"){
                    $(".holiday").not(".weekly").hide();
                    $(".weekly").show();
                }
                else{
                    $(".holiday").hide();
                }
            });
        }).change();
        var startDate;
        var currentDate;
        var endDate;
        $( "#appbundle_holiday_startDate" ).datepicker({
            defaultDate: "+1w",
            changeMonth: true,
            dateFormat: "yy-mm-dd",
            minDate: 0,
            numberOfMonths: 1,
            onClose: function( selectedDate ) {
                startDate = $(this).val();
                currentDate = new Date();
                var fullDate = (currentDate.getFullYear())+ "-" + ("0"+(currentDate.getMonth()+1)).slice(-2) + "-" +("0"+(currentDate.getDate())).slice(-2);
                if(startDate < fullDate){
                    $( "#appbundle_holiday_startDate" ).datepicker( "option", "minDate", fullDate);
                }
                $( "#appbundle_holiday_endDate" ).datepicker( "option", "minDate", selectedDate );
            }
        });
        $( "#appbundle_holiday_endDate" ).datepicker({
            defaultDate: "+1w",
            changeMonth: true,
            dateFormat: "yy-mm-dd",
            minDate: 0,
            numberOfMonths: 1,
            onClose: function( selectedDate ) {
                endDate = $(this).val();
                currentDate = new Date();
                var fullDate = (currentDate.getFullYear())+ "-" + ("0"+(currentDate.getMonth()+1)).slice(-2) + "-" +("0"+(currentDate.getDate())).slice(-2);
                if(endDate < fullDate){
                    $( "#appbundle_holiday_endDate" ).datepicker( "option", "minDate", currentDate);
                    $( "#appbundle_holiday_startDate" ).datepicker( "option", "maxDate", currentDate );
                }else{
                    $( "#appbundle_holiday_startDate" ).datepicker( "option", "maxDate", selectedDate );
                }
            }
        });
    }

    if ($('#holiday-list-page').length) {

        $("#year").on('change',function () {
            var year = $('#year').val();
            $.ajax({
                url: Routing.generate('holiday_year_summery'),
                data: 'year='+year,
                type: "GET",
                dataType : "html",
                success: function( response ) {
                    $( '#summery' ).html( response );
                }
            });
            return false;
        }).change();
    }

    if ($('#courtfee-update-page').length) {

        function applicationTypeCourtFee()
        {
            return $('#court_fee_applicationType').find(":selected").val() == "PORCHA" || $('#court_fee_applicationType').find(":selected").val() == "MOUZA_MAP";
        }

        $("#court_fee_submit").click(function () {
                $('#form-update-courtfee').validate({
                    lang: 'bn_BD',
                    errorElement: 'span', //default input error message container
                    errorClass: 'help-block help-block-error', // default input error message class
                    focusInvalid: false, // do not focus the last invalid input
                    ignore: "",  // validate all fields including form hidden input
                    rules: {
                        'court_fee[survey]': {
                            required: {
                                depends: function (element) {
                                    return applicationTypeCourtFee();
                                }
                            }
                        }
                    },
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

        });

        $(".application_type").change(function(){
            $(this).find("option:selected").each(function(){
                if($(this).attr("value")=="INFORMATION_SLIP"){
                    $(".survy_type").hide();
                }
                else if($(this).attr("value")=="CASE_COPY"){
                    $(".survy_type").hide();
                }
                else{
                    $(".survy_type").show();
                }
                //$('#form-update-courtfee').reset();
            });

        }).change();
    }

    if ($('#additionalfee-update-page').length) {

        function applicationTypeAdditionalFee()
        {
            return $('#additional_fee_applicationType').find(":selected").val() == "PORCHA" || $('#additional_fee_applicationType').find(":selected").val() == "MOUZA_MAP";
        }

        $("#additional_fee_submit").click(function () {
                $('#form-update-additionalfee').validate({
                    lang: 'bn_BD',
                    errorElement: 'span', //default input error message container
                    errorClass: 'help-block help-block-error', // default input error message class
                    focusInvalid: false, // do not focus the last invalid input
                    ignore: "",  // validate all fields including form hidden input
                    rules: {
                        'additional_fee[survey]': {
                            required: {
                                depends: function (element) {
                                    return applicationTypeAdditionalFee();
                                }
                            }
                        }
                    },
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

        });

        $(".application_type").change(function(){
            $(this).find("option:selected").each(function(){
                if($(this).attr("value")=="INFORMATION_SLIP"){
                    $(".survy_type").hide();
                }
                else if($(this).attr("value")=="CASE_COPY"){
                    $(".survy_type").hide();
                }
                else{
                    $(".survy_type").show();
                }
                //$('#form-update-courtfee').reset();
            });

        }).change();
    }

    if ($('#deliveryday-update-page').length) {

        function applicationTypeDeliveryDay()
        {
            return $('#delivery_day_applicationType').find(":selected").val() == "PORCHA" || $('#delivery_day_applicationType').find(":selected").val() == "MOUZA_MAP";
        }
        $("#delivery_day_submit").click(function () {
            $('#form-update-deliveryday').validate({
                lang: 'bn_BD',
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",  // validate all fields including form hidden input
                rules: {
                    'delivery_day[survey]': {
                        required: {
                            depends: function (element) {
                                return applicationTypeDeliveryDay();
                            }
                        }
                    }
                },
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
        });


        $(".application_type").change(function(){
            $(this).find("option:selected").each(function(){
                if($(this).attr("value")=="INFORMATION_SLIP"){
                    $(".survy_type").hide();
                    $(".app_type").hide();
                    $('label').removeClass('extra-top-margin');
                }
                else if($(this).attr("value")=="CASE_COPY"){
                    $(".survy_type").hide();
                    $(".app_type").hide();
                    $('label').removeClass('extra-top-margin');
                }
                else if($(this).attr("value")=="MOUZA_MAP"){
                    $(".survy_type").show();
                    $(".app_type").hide();
                    $('label').removeClass('extra-top-margin');
                }
                else{
                    $(".survy_type").show();
                    $(".app_type").show();
                    $('label.extra-margin').addClass('extra-top-margin');
                }
            });
        }).change();
    }

});