var PorchaOption = function (){

    var multipleMouza = true;

    function init(param)
    {
        if (param !== undefined) {
            if ( param.hasOwnProperty('multipleMouza') ) {
                multipleMouza = param.multipleMouza;
            }
        }

        var select2DistrictData = [];
        var select2DistrictDataCitizen = [];
        var select2District = $('.po-district').select2(
            {
                id: function(e) {return e.id; },
                data:{results: select2DistrictData, text:'text'},
                placeholder: 'নির্বাচন করুণ',
                formatSelection: formatResult,
                formatResult: formatResult,
                allowClear: true
            });

        var select2DistrictCitizen = $('.citizen-district').select2(
            {
                id: function(e) {return e.id; },
                data:{results: select2DistrictDataCitizen, text:'text'},
                placeholder: 'নির্বাচন করুণ',
                formatSelection: formatResult,
                formatResult: formatResult,
                allowClear: true
            });




        $(".po-division").change(function() {

            if(!$(".po-district").length) {
                return;
            }

            $(".po-district").select2("val", "");
            var divisionId = $(this).val();
            if(divisionId){
                $("#division").after("<div class=\"imageshow\">. </div>");
            }
            clearSelect2Fields([select2DistrictData]);

            if(divisionId != "") {
                $.get(Routing.generate('combo_districts', {'divisionId': divisionId}), function (response) {
                    for (var k in response) {
                        console.log(k, response[k]);
                        select2DistrictData.push(response[k]);
                        $('.imageshow').hide();
                    }
                });
            }

            //$(".po-district").change();
        });
        $(".citizen-division").change(function() {

            if(!$(".citizen-district").length) {
                return;
            }

            $(".citizen-district").select2("val", "");
            var divisionId = $(this).val();
            if(divisionId){
                $("#citizenDivision").after("<div class=\"imageshow\">. </div>");
            }
            clearSelect2Fields([select2DistrictDataCitizen]);

            if(divisionId != "") {
                $.get(Routing.generate('combo_districts', {'divisionId': divisionId}), function (response) {
                    for (var k in response) {
                        console.log(k, response[k]);
                        select2DistrictDataCitizen.push(response[k]);
                        $('.imageshow').hide();
                    }
                });
            }

            //$(".po-district").change();
        });

        var select2UpozilaData = [];
        var select2Upozila = $('.po-upozila').select2(
            {
                id: function(e) {return e.id; },
                data:{results: select2UpozilaData, text:'text'},
                placeholder: 'নির্বাচন করুণ',
                formatSelection: formatResult,
                formatResult: formatResult,
                allowClear: true
            });
        var select2UpozilaDataCitizen = [];
        var select2UpozilaCitizen = $('.citizen-upozila').select2(
            {
                id: function(e) {return e.id; },
                data:{results: select2UpozilaDataCitizen, text:'text'},
                placeholder: 'নির্বাচন করুণ',
                formatSelection: formatResult,
                formatResult: formatResult,
                allowClear: true
            });
        $(".citizen-district").change(function() {

            if(!$(".citizen-upozila").length) {
                return;
            }

            $(".citizen-upozila").select2("val", "");
            var districtId = $(this).val();
            if(districtId){
                $("#citizenDistrict").after("<div class=\"imageshow\">. </div>");
            }
            clearSelect2Fields([select2UpozilaCitizen]);

            if(districtId != "") {
                $.get(Routing.generate('combo_upozilas', {'districtId': districtId}), function (response) {
                    for (var k in response) {
                        console.log(k, response[k]);
                        select2UpozilaDataCitizen.push(response[k]);
                        $('.imageshow').hide();
                    }
                });
            }
        });

        $(".po-district, .survey").change(function() {

            if(!$(".po-upozila").length) {
                return;
            }

            $(".po-upozila").select2("val", "");
            var districtId = $(this).val();
            if(districtId){
                $("#district").after("<div class=\"imageshow\">. </div>");
            }
            var survey = $('.survey').select2("val");
            clearSelect2Fields([select2UpozilaData]);

            if(districtId != "") {
                $.get(Routing.generate('combo_upozilas', {'districtId': districtId}), function (response) {
                    for (var k in response) {
                        console.log(k, response[k]);
                        select2UpozilaData.push(response[k]);
                    }
                });
                $.get(Routing.generate('court_fee_list', {'districtId': districtId}), function (response) {
                    $.each(response,function(key,value) {
                        if(key == survey)
                            $('.courtPrice').text(value.normalCourtFee);
                        $('.deliveryPrice').text(0);
                        $('.court-fee').val(value.normalCourtFee);
                        $('.delivery-fee').val(0);
                        calculateFee();
                        $('.imageshow').hide();
                    });
                });
            }
        });
        $(".deliveryUrgency").change(function() {
            var districtId = $('.po-district').select2("val");
            var survey = $('.survey').select2("val");
            var deliveryUrgency = $('input[name="deliveryUrgency"]:checked').val();
            $.get(Routing.generate('court_fee_list', {'districtId': districtId}), function (response) {
                $.each(response,function(key,value) {
                    if(key == survey){
                        if(deliveryUrgency == 'NORMAL'){
                            $('.courtPrice').text(value.normalCourtFee);
                            $('.court-fee').val(value.normalCourtFee);


                        }else{
                            $('.courtPrice').text(value.urgentCourtFee);
                            $('.court-fee').val(value.urgentCourtFee);
                        }
                        calculateFee();
                    }

                });
            });

        });
        $(".deliveryWay").change(function() {
            var districtId = $('.po-district').select2("val");
            var deliveryWay = $('input[name="deliveryWay"]:checked').val();
            var district = $('.po-district').select2('data').text;
            var CitizenDistrict = $('.citizen-district').select2('data').text;
            $.get(Routing.generate('delivery_fee_list', {'districtId': districtId}), function (response) {
                if(deliveryWay == 'DIRECT'){
                    $('.deliveryPrice').text(0);
                    $('.delivery-fee').val(0);
                }else{
                    if(district == CitizenDistrict){
                        $('.deliveryPrice').text(response.deliveryFee);
                        $('.delivery-fee').val(response.deliveryFee);
                    }else{
                        $('.deliveryPrice').text(response.otherDistrictDeliveryFee);
                        $('.delivery-fee').val(response.otherDistrictDeliveryFee);
                    }
                }
                calculateFee();
            });

        });
        function calculateFee(){
            var courtPrice =  $('.courtPrice').text();
            var deliveryPrice =  $('.deliveryPrice').text();
            var total = parseInt(courtPrice) + parseInt(deliveryPrice);
            $('.total').text(total);
        }
        var select2MouzaData = [];
        var select2Mouza = $('.po-mouza').select2(
            {
                id: function(e) {return e.id; },
                data:{results: select2MouzaData, text:'text'},
                placeholder: 'নির্বাচন করুণ',
                formatSelection: formatResult,
                formatResult: formatResult,
                allowClear: true
            });

        $(".po-upozila").change(function() {

            if(!$(".po-mouza").length) {
                return;
            }
            $(".po-mouza").select2("val", "");
            var upozilaId = $(this).val();
            if(upozilaId){
                $("#upozila").after("<div class=\"imageshow\">. </div>");
            }
            clearSelect2Fields([select2MouzaData]);

            if(upozilaId != "") {
                $.get(Routing.generate('combo_mouzas', {'upozilaId': upozilaId}), function (response) {
                    for (var k in response) {
                        console.log(k, response[k]);
                        select2MouzaData.push(response[k]);
                        $('.imageshow').hide();
                    }
                });
            }

            //$(".po-district").change();
        });

        $('.search_porcha').click(function (){
            validation();
        });

        function validation(){
            var form = $('#search_validation');
            var error = $('.alert-danger', form);
            var success = $('.alert-success', form);

            form.validate({
                errorElement: 'span',
                errorClass: 'help-inline',
                focusInvalid: false,
                ignore: ".ignore",
                rules: {
                    'survey':{
                        required:true
                    },
                    'division':{
                        required:true
                    },
                    'district':{
                        required:true
                    },
                    'upozila':{
                        required:true
                    },
                    'mouza':{
                        required:true
                    }

                },
                messages : {
                    'survey': "তথ্য টি  আবশ্যক ",
                    'division': "তথ্য টি  আবশ্যক ",
                    'district': "তথ্য টি  আবশ্যক ",
                    'upozila': "তথ্য টি  আবশ্যক ",
                    'mouza': "তথ্য টি  আবশ্যক "
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
                },
                submitHandler: function (form) {
                    success.show();
                    error.hide();

                    $.ajax({
                        url: Routing.generate('porcha_search_result'),
                        type:'POST',
                        data:$('.search_khatian').serializeArray(),
                        success: function (response){
                            var content = $('#data-list');
                            content. html(response);
                            $('html, body').animate({ scrollTop: 700 }, 'slow');
                        }

                    });

                }




            });
            //apply validation on select2 dropdown value change, this only needed for chosen dropdown integration.
            $('.select2me', form).change(function () {
                form.validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input
            });

        }



        var row = $('#search_khatian tr').length;
        if(row > 0){
            $('html, body').animate({ scrollTop: 700 }, 'slow');
        }


        function clearSelect2Fields(fieldData) {
            for(var k in fieldData) {
                var last = fieldData[k].length;
                fieldData[k].splice(0, last);
            }
        }

        function formatResult(state) {
            if (!state.id) return state.text;
            return state.text;
        }
    }

    return {
        'init': init
    };
}();


