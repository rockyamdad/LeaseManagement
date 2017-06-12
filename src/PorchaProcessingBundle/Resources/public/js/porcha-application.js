$(function() {

    var parentRow;
    var surveySelector;
    var districtSelector;
    var upozilaSelector;
    var mouzaSelector;
    var jlSelector;

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

    function setSelector(elm){
        parentRow = elm.parents('tr');

        surveySelector = parentRow.find('.survey_selector');
        districtSelector = parentRow.find('.district_selector');
        upozilaSelector = parentRow.find('.upozila_selector');
        mouzaSelector = parentRow.find('.mouza_selector');
        jlSelector = parentRow.find('.jl_selector');
    }

    function resetPorchaField(resetFields){
        var fields = {
            survey: surveySelector,
            district: districtSelector,
            upozila: upozilaSelector,
            mouza: mouzaSelector,
            jl: jlSelector
        };

        if (resetFields.length == 0) {
            resetFields = fields;
        }
        $.each(resetFields, function(i, val){
            fields[val].val('').find('option').remove();
        });
    }

    function addKhatianInfo() {
        var khatianList = jQuery('#khatian-fields-list');

        var newWidget = khatianList.attr('data-prototype');
        newWidget = newWidget.replace(/__name__/g, khatianCount);
        khatianCount++;

        var newLi = jQuery('<tr></tr>').html(newWidget);
        newLi.appendTo($('#khatian-list'));

        calculateFee();
    }

    $('#add-khatian-info').click(function(e) {
        e.preventDefault();

        addKhatianInfo();
    });

    $('#khatian-list').on('change', '.survey_selector', function(){
        setSelector($(this));
        resetPorchaField(['jl']);

        mouzaSelector.trigger('change');
        calculateFee();
    }).on('change', '.district_selector', function(){
        setSelector($(this));
        resetPorchaField(['upozila', 'mouza', 'jl']);
        bloclKhatianList();
        upozilaSelector.find('option').remove();

        var distictId = $(this).val();
        if ($.trim(distictId) == '') {
            unblockKhatianList();
            return;
        }
        $.ajax({
            url: Routing.generate('combo_upozila', {districtId: distictId}),
            dataType: 'json',
            success: function(json){
                upozilaSelector.append('<option value="">উপজেলা নির্বাচন করুণ</option>');
                $.each(json, function(i, value){
                    upozilaSelector.append('<option value="'+value.id+'">'+value.text+'</option>');
                });
                upozilaSelector.trigger('change');
            },
            error: function(html){
                console.log(html);
            },
            beforeSend: function(){

            }
        });

    }).on('change', '.upozila_selector', function(){
        setSelector($(this));
        resetPorchaField(['mouza', 'jl']);
        bloclKhatianList();
        mouzaSelector.find('option').remove();

        var upozilaId = $(this).val();
        if ($.trim(upozilaId) == '') {
            unblockKhatianList();
            return;
        }
        $.ajax({
            url: Routing.generate('combo_mouza', {type: 'upozila', id: upozilaId}),
            dataType: 'json',
            success: function(json){
                mouzaSelector.append('<option value="">মৌজা নির্বাচন করুণ</option>');
                $.each(json, function(i, value){
                    mouzaSelector.append('<option value="'+value.id+'">'+value.text+'</option>');
                });
                mouzaSelector.trigger('change');
            },
            error: function(html){
                console.log(html);
            },
            beforeSend: function(){

            }
        });

    }).on('change', '.mouza_selector', function(){
        setSelector($(this));
        resetPorchaField(['jl']);
        bloclKhatianList();

        var mouzaId = $(this).val();
        var surveyId = surveySelector.val();

        if (!mouzaId || !surveyId || !surveyId) {
            unblockKhatianList();
            return false;
        }

        jlSelector.find('option').remove();
        $.ajax({
            url: Routing.generate('combo_jl', {surveyId: surveyId, mouzaId: mouzaId}),
            success: function(val){
                jlSelector.val(val);
                unblockKhatianList();
            },
            error: function(html){
                console.log(html);
            },
            beforeSend: function(){

            }
        });

    }).on('click', '.remove', function(){

        if ($('#khatian-list').find('tr').length <= 1) {
            bootbox.alert('নূন্যতম একটি খতিয়ানের তথ্য প্রয়োজন');
            return;
        }

        setSelector($(this));
        parentRow.remove();

        calculateFee();
    });

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
        var urgency = $('#porchaprocessingbundle_servicerequest_urgency').find('[type=radio]:checked').val();
        var delivery = $('#porchaprocessingbundle_servicerequest_deliveryMethod').find('[type=radio]:checked').val();
        var deliveryDistrict = $('#porchaprocessingbundle_servicerequest_district').val();

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
        var surveyId;

        $('#khatian-list').find('tr').each(function(index, tr){
            var row = $(tr);
            surveyId = row.find('.survey_selector').val();

            if (officeSetting.courtFee && officeSetting.courtFee[surveyId]) {
                courtFee += urgency == 'URGENT' ? parseInt(officeSetting.courtFee[surveyId].urgentCourtFee) : parseInt(officeSetting.courtFee[surveyId].normalCourtFee);
            }
        });
        $('.porcha-address').hide();
        if (delivery == 'POSTAL') {
            if (deliveryDistrict == officeDistrict) {

                deliveryFee += sameDeliveryFeeDistrict;
            } else {
                deliveryFee += otherDistrictDeliveryFee;
            }
            $('.porcha-address').show();
        }
        if (delivery == 'UDC') {
            deliveryFee += sameDeliveryFeeDistrictUdc;
            $('.porcha-address').hide();
        }

        priceTable.find('.ADDITIONAL_FEE').hide();
        var additionalFeeRows = priceTable.find('.ADDITIONAL_FEE_FOR_' + surveyId);
        if (additionalFeeRows.length) {
            additionalFeeRows.show().each(function () {
                additionalFees += parseInt($(this).find('.price').attr('rel'));
            });
        }

        priceCell.text(AppUtil.englishToBanglaDigit(String(courtFee)) + '/=');
        deliveryCell.text(AppUtil.englishToBanglaDigit(String(deliveryFee)) + '/=');
        totalCell.text(AppUtil.englishToBanglaDigit(String(courtFee + deliveryFee + additionalFees)) + '/=');
    }

    function updateDeliveryAddress()
    {
        address_text = '';
        var deliveryMethod = $('#porchaprocessingbundle_servicerequest_deliveryMethod').find('[type=radio]:checked').val()
        var name = $("#porchaprocessingbundle_servicerequest_name").val().trim();
        var ongoingCare = $("#porchaprocessingbundle_servicerequest_ongoingCare").val().trim();
        var house = $('#porchaprocessingbundle_servicerequest_houseNo').val().trim();
        var road = $('#porchaprocessingbundle_servicerequest_roadNo').val().trim();
        var area = $('#porchaprocessingbundle_servicerequest_area').val().trim();
        var postal = $('#porchaprocessingbundle_servicerequest_postalCode').val().trim();
        var upozila = $('#porchaprocessingbundle_servicerequest_upozila option:selected').text();
        var district = $('#porchaprocessingbundle_servicerequest_district option:selected').text();

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

        $('#porchaprocessingbundle_servicerequest_address').val(address_text);
    }

    $('#porchaprocessingbundle_servicerequest_urgency, #porchaprocessingbundle_servicerequest_deliveryMethod').find('input[type=radio]').change(function(){
        calculateFee();
    });

    $('#porchaprocessingbundle_servicerequest_district').change(function(){
        calculateFee();
    });

    $('.applicant-address').find('input, select').on('keyup blur',function(){
        updateDeliveryAddress();
    });

    $('#porchaprocessingbundle_servicerequest_deliveryMethod').find('input[type=radio]').change(function(){
        updateDeliveryAddress();
    });

    // Page level event trigger
    if (mode == 'new') {
        addKhatianInfo();
        updateDeliveryAddress();
    }

    if (mode == 'edit') {
        calculateFee();
    }

    function isPostalAddressRequire()
    {
        return $('#porchaprocessingbundle_servicerequest_deliveryMethod').find('[type=radio]:checked').val() === 'POSTAL';
    }
    $('form[name=porchaprocessingbundle_servicerequest]').validate({
        rules: {
            'porchaprocessingbundle_servicerequest[district]': validateServiceRequestAddressFields,
            'porchaprocessingbundle_servicerequest[upozila]': validateServiceRequestAddressFields,
            'porchaprocessingbundle_servicerequest[postalCode]': validateServiceRequestAddressFields,
            'porchaprocessingbundle_servicerequest[area]': validateServiceRequestAddressFields
        },
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