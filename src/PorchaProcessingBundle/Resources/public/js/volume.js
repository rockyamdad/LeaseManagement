$(function() {

    $('.template').change(function(){
        var id = $(this).val();
        if (id) {
            var href = Routing.generate('template_preview_template', { id: id });
            $(this).parent().find('.template-preview').attr('href', href).show();
        } else {
            $(this).parent().find('.template-preview').attr('href', '#').hide();
        }
    });

    if ($('#volume-update-page').length) {

        $('#form-add-volume').validate({
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

                $('#page-msg').empty();

                if (alreadyApproved) {
                    form.submit();
                    return false;
                }

                if ($('#row-clone tr').length < 2) {
                    Metronic.alert({ container: '#page-msg', type: 'danger', place: 'append', message: "মৌজা যুক্ত করুন। " });
                    return false;
                }

                var numberError = false;
                $('#mouza-list').find('tr').each(function () {

                    var row = $(this);
                    var txt1 = parseInt($('td:eq(1) input[type="number"]', row).val());
                    var txt2 = parseInt($('td:eq(2) input[type="number"]', row).val());

                    if ( txt1 < 1 || txt2 < 1 || txt1 == txt2 || txt1 > txt2 ) {
                        numberError = true;
                        return false
                    }
                });

                if (numberError) {
                    Metronic.alert({ container: '#page-msg', type: 'danger', place: 'append', message: "খতিয়ান ব্যাপ্তি ঠিক নয় । " });
                    return false;
                }

                $.ajax({
                    url: Routing.generate('volume_no_verify'),
                    type: "post",
                    dataType: 'json',
                    data: $('#form-add-volume').serialize(),
                    beforeSend: function() {
                        Metronic.blockUI({
                            target: $('#form-add-volume'),
                            animate: true,
                            overlayColor: 'black'
                        });
                    },
                    success: function(response){
                        Metronic.unblockUI($('#form-add-volume'));
                        if (response.status !== false) {
                            Metronic.alert({ container: '#page-msg', type: 'danger', place: 'append', message: response.msg });
                            $('html, body').animate({scrollTop:0}, 'slow');
                        } else {
                            form.submit();
                        }
                    }
                });
            }
        });

        $('#porcha_processing_volume_district').change(function(){

            var elmUpozila = $('#porcha_processing_volume_upozila');
            var districtId = $(this).val();
            elmUpozila.find('option').remove();
            if (!districtId) {
                return false;
            }

            $.ajax({
                url: Routing.generate('combo_upozila', {districtId: districtId}),
                dataType: 'json',
                success: function(json){
                    unBlockContent();
                    $.each(json, function(i, value){
                        elmUpozila.append('<option value="'+value.id+'">'+value.text+'</option>');
                    });
                    $('#porcha_processing_volume_upozila').change();
                },
                error: function() {
                    unBlockContent();
                },
                beforeSend: function() {
                    blockContent();
                }
            });
        });

        $('#porcha_processing_volume_upozila').change(function(){

            var elmMouza = $('#porcha_processing_volume_volumeMouzas');
            var upozilaId = $(this).val();
            elmMouza.find('option').remove();
        });

        function upozilaMouzas(elmMouza, upozilaId, surveyId) {

            $.ajax({
                url: Routing.generate('combo_mouza', {type: 'upozila', id: upozilaId}),
                dataType: 'json',
                success: function(json){
                    unBlockContent();
                    $.each(json, function(i, value){
                        elmMouza.append('<option value="'+value.id+'">'+value.text+'</option>');
                    });
                },
                error: function() {
                    unBlockContent();
                },
                beforeSend: function() {
                    blockContent();
                }
            });
        }

        function addMouza() {

            var list = jQuery('#row-clone');
            var newWidget = list.attr('data-prototype');

            newWidget = newWidget.replace(/__name__/g, rowCount);
            rowCount++;

            var newLi = jQuery('<tr></tr>').html(newWidget);
            newLi.appendTo($('#mouza-list'));
        }

        $('#add-mouza').click(function(e) {
            e.preventDefault();

            var elmUpozila = $('#porcha_processing_volume_upozila');
            var elmSurvey = $('#porcha_processing_volume_survey');

            if (elmUpozila.val() == '') {
                $('#form-add-volume').validate().element('#porcha_processing_volume_upozila');
                return false;
            }

            addMouza();
            $lastSelect = $('#mouza-list select:last');
            upozilaMouzas($lastSelect, elmUpozila.val(), elmSurvey.val());
        });

        $('#mouza-list').on('click', '.remove-row', function() {
            $(this).closest('tr').remove()
        })

    }

    if ($('#volume-khatians-page').length) {

        $("#open-en-Template").on("hover", "li", function() {
            var anchor = $(this).find('a');
            var mouzaId = $("#entry-mouza").val();
            var url = anchor.attr('href').replace("mouza", mouzaId);
            anchor.attr('href', url);
        });

        $("#open-en-Template-single").on("hover", function(e) {
            var mouzaId = $("#entry-mouza").val();
            var url = $(this).attr('href').replace("mouza", mouzaId);
            $(this).attr('href', url);
            return true;
        });

        $('#entry-mouza').change(function(){
            var id = $(this).val();
            $(".entry-btn").hide();
            $("#entry-btn-"+id).show();
        }).change();

        $( '.actions' ).on( 'click', '#btn-archive', function(e) {
            e.preventDefault();
            if (!confirm('আপনি কি নিশ্চিত ?')) {
                return false;
            }
            var button = $(this);
            $.post(button.attr('href'), {'_token': button.attr('data-csrf')}, function (response) {
                window.location = Routing.generate('volume_list');
            });
        });
    }

    $('div.modal').on('hidden.bs.modal', function (e) {
        $(this).removeData('bs.modal');
        if ($(e.target).attr('role') == 'dialog') {
            return false;
        }
        $(this).find('.modal-content').html('<div class="modal-body">'+
            '<img src="/assets/layout3/img/loading-spinner-grey.gif" alt="" class="loading">' +
            '<span> &nbsp;&nbsp;Loading... </span></div>');
        try{
        } catch(e){}
    });


    if ($('#volume-index-update-page').length) {

        $('#add-record').click(function (e) {
            e.preventDefault();
            addIndexRecord();
        });

        $('#record-list').on('click', '.remove', function() {
            $(this).closest('tr').remove()
        });
    }

    function addIndexRecord() {

        var recordList = jQuery('#row-clone');

        var newWidget = recordList.attr('data-prototype');
        newWidget = newWidget.replace(/__name__/g, indexCount);
        indexCount++;

        var newLi = jQuery('<tr></tr>').html(newWidget);
        newLi.appendTo($('#record-list'));
    }

    function blockContent()
    {
        if ($('.block-group').length) {
            Metronic.blockUI({
                target: $('.block-group'),
                animate: true,
                overlayColor: 'black'
            });
        }
    }

    function unBlockContent()
    {
        if ($('.block-group').length) {
            Metronic.unblockUI($('.block-group'));
        }
    }
});