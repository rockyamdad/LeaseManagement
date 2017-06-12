$(function() {

    $('#print').click(function(){
        window.open(window.location.href + '?print=yes&' + $('#form-search').serialize());

        return false;
    });

    function bloclKhatianList() {
        if ($('.khatian-list').find('.blockOverlay').length) return;

        Metronic.blockUI({
            target: $('.khatian-list'),
            animate: true,
            overlayColor: 'black'
        });
    }

    function unblockKhatianList() {
        Metronic.unblockUI($('.khatian-list'));
    }

    $('#porchaprocessingbundle_servicerequest_district').change(function(){
        var upozilaSelector = $('#porchaprocessingbundle_servicerequest_upozila');
        var districtId = $(this).val();

        upozilaSelector.find('option').remove();
        $.ajax({
            url: Routing.generate('data_source_upozilas', {districtId: districtId}),
            dataType: 'json',
            success: function(json){
                $.each(json, function(i, value){
                    upozilaSelector.append('<option value="'+value.id+'">'+value.text+'</option>');
                });
            },
            error: function(html){
                console.log(html);
            },
            beforeSend: function(){

            }
        });
    });

    function calculateFee(){
        var urgency = $('#porchaprocessingbundle_servicerequestcasecopy_serviceRequest_urgency').find('[type=radio]:checked').val();
        var delivery = $('#porchaprocessingbundle_servicerequestcasecopy_serviceRequest_deliveryMethod').find('[type=radio]:checked').val();
        var deliveryDistrict = $('#porchaprocessingbundle_servicerequestcasecopy_serviceRequest_district').val();

        var sameDeliveryFeeDistrict = officeSetting.deliveryFee && officeSetting.deliveryFee.deliveryFee ? parseInt(officeSetting.deliveryFee.deliveryFee) : 0;
        var sameDeliveryFeeDistrictUdc = officeSetting.deliveryFee && officeSetting.deliveryFee.udcDeliveryFee ? parseInt(officeSetting.deliveryFee.udcDeliveryFee) : 0;


        var otherDistrictDeliveryFee = officeSetting.deliveryFee && officeSetting.deliveryFee.otherDistrictDeliveryFee ? parseInt(officeSetting.deliveryFee.otherDistrictDeliveryFee) : 0;

        var priceTable = $('.price-table');

        priceTable.show();
        var totalCell = priceTable.find('.total');
        var priceCell = priceTable.find('.FEE .price');
        var deliveryCell = priceTable.find('.DELIVERY .price');
        var courtFee = 0;
        var deliveryFee = 0;
        var additionalFees = 0;

        courtFee += urgency == 'URGENT' ? parseInt(officeSetting.courtFee.urgentCourtFee) : parseInt(officeSetting.courtFee.normalCourtFee);

        if (delivery == 'POSTAL') {
            if (deliveryDistrict == officeDistrict) {

                deliveryFee += sameDeliveryFeeDistrict;
            } else {
                deliveryFee += otherDistrictDeliveryFee;
            }
        }
        if (delivery == 'UDC') {
            deliveryFee += sameDeliveryFeeDistrictUdc;
        }

        var additionalFeeRows = priceTable.find('.ADDITIONAL_FEE');
        if (additionalFeeRows.length) {
            additionalFeeRows.show().each(function () {
                additionalFees += parseInt($(this).find('.price').attr('rel'));
            });
        }

        priceCell.text(AppUtil.banglaToEnglish(String(courtFee)) + '/=');
        deliveryCell.text(AppUtil.banglaToEnglish(String(deliveryFee)) + '/=');
        totalCell.text(AppUtil.banglaToEnglish(String(courtFee + deliveryFee + additionalFees)) + '/=');
    }

    function updateDeliveryAddress()
    {
        address_text = '';
        var deliveryMethod = $('#porchaprocessingbundle_servicerequestcasecopy_serviceRequest_deliveryMethod').find('[type=radio]:checked').val()
        var name = $("#porchaprocessingbundle_servicerequestcasecopy_serviceRequest_name").val().trim();
        var ongoingCare = $("#porchaprocessingbundle_servicerequestcasecopy_serviceRequest_ongoingCare").val().trim();
        var house = $('#porchaprocessingbundle_servicerequestcasecopy_serviceRequest_houseNo').val().trim();
        var road = $('#porchaprocessingbundle_servicerequestcasecopy_serviceRequest_roadNo').val().trim();
        var area = $('#porchaprocessingbundle_servicerequestcasecopy_serviceRequest_area').val().trim();
        var postal = $('#porchaprocessingbundle_servicerequestcasecopy_serviceRequest_postalCode').val().trim();
        var upozila = $('#porchaprocessingbundle_servicerequestcasecopy_serviceRequest_upozila option:selected').text();
        var district = $('#porchaprocessingbundle_servicerequestcasecopy_serviceRequest_district option:selected').text();

        if (deliveryMethod == 'POSTAL') {
            if (name != "") { address_text += name; }

            if (ongoingCare != "") { address_text += ', \nপ্রযত্নেঃ ' + ongoingCare; }

            if (house != "") { address_text += ', \nবাসাঃ ' + house; }

            if (road != "") { address_text += ', \nরাস্তাঃ ' + road; }

            if (area != "") { address_text += ', \nএলাকাঃ ' + area; }

            if (postal != "") { address_text += ', \nপোস্ট অফিসঃ ' + postal; }

            if (upozila != "") { address_text += ', \nউপজেলাঃ ' + upozila; }

            if (district != "") { address_text += ', \nজেলাঃ ' + district + " ।"; }
        }

        $('#porchaprocessingbundle_servicerequestcasecopy_serviceRequest_address').val(address_text);
    }

    $('#porchaprocessingbundle_servicerequestcasecopy_serviceRequest_urgency, #porchaprocessingbundle_servicerequestcasecopy_serviceRequest_deliveryMethod').find('input[type=radio]').change(function(){
        calculateFee();
    });

    $('#porchaprocessingbundle_servicerequestcasecopy_serviceRequest_district').change(function(){
        calculateFee();
    });

    $('.applicant-address').find('input, select').on('keyup blur',function(){
        updateDeliveryAddress();
    });

    $('#porchaprocessingbundle_servicerequestcasecopy_serviceRequest_deliveryMethod').find('input[type=radio]').change(function(){
        updateDeliveryAddress();
    });

    // Page level event trigger
    if (typeof (mode) != 'undefined' && mode == 'new') {
        updateDeliveryAddress();
    }

    if (typeof (mode) != 'undefined' && mode == 'edit') {
        calculateFee();
    }

    function isServiceRequestPostalAddressRequire()
    {
        return false;$('#porchaprocessingbundle_servicerequestcasecopy_serviceRequest_deliveryMethod').find('[type=radio]:checked').val() === 'POSTAL';
    }

    var validateServiceRequestAddressFields = {
        required: {
            depends: function (element) {
                return isServiceRequestPostalAddressRequire();
            }
        }
    };

    $('form[name=porchaprocessingbundle_servicerequestcasecopy]').validate({

        lang: 'bn_BD',
        errorElement: 'span', //default input error message container
        errorClass: 'help-block help-block-error', // default input error message class
        focusInvalid: true, // do not focus the last invalid input
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