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
    }

    $('#add-khatian-info').click(function(e) {
        e.preventDefault();

       // addKhatianInfo();
    });

    $('#khatian-fields-list').on('change', '.survey_selector', function(){
        console.log('survey_selector')
        setSelector($(this));
        resetPorchaField(['jl']);

        mouzaSelector.trigger('change');
    }).on('change', '.district_selector', function(){
        console.log('district_selector')
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
        console.log('upozila_selector')
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
        console.log('mouza_selector')
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

    });

    $('#porchaprocessingbundle_servicerequest_detail_entities___name___district').change(function(){

        var upozilaSelector = $('#porchaprocessingbundle_servicerequest_detail_entities___name___upozila');
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

    // Page level event trigger
    if (mode == 'new') {
       // addKhatianInfo();
    }

    function isPostalAddressRequire()
    {
        return $('#porchaprocessingbundle_servicerequest_deliveryMethod').find('[type=radio]:checked').val() === 'POSTAL';
    }

    $('form[name=porchaprocessingbundle_servicerequest]').validate({
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
            console.log(error);
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent().parent().parent());
            } else {
                error.insertAfter(element);
            }
        }
    });

});