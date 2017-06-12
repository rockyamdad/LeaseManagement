$(function() {

    $("#btn-report-print").click(function (e) {

        e.preventDefault();
        if (!confirm('আপনি কি নিশ্চিত ?')) {
            return false;
        }
        var data = $('#form-search').serializeArray();
        data.push({name: 'print',value:'print'});
        $.get($(this).attr('data-url'), data)
            .done(function (response) {
                var popupWin = window.open('', '_blank', 'width=auto,height=auto');

                popupWin.document.open();

                popupWin.document.write(response);
                popupWin.onload = function (){
                    window.print();
                }
                popupWin.document.close();
            });

    });

    if ($("#dc-volume-wise-entry-report-page").length) {

        var select2VolumeData = [];
        var select2Volume = $('#volumeId').select2(
            {
                id: function(e) {return e.id; },
                data:{results: select2VolumeData, text:'text'},
                placeholder: 'নির্বাচন করুণ',
                formatSelection:    function (state) {
                    if (!state.id) return state.text;
                    return state.text;
                },
                formatResult: function (state) {
                    if (!state.id) return state.text;
                    return state.text;
                },
                allowClear: true
            });
        $(".mo-mouza").change(function(){

            if(!$("#volumeId").length) {
                return;
            }

            var mouzaId = $(this).val();
                var elmVolume = $('#volumeId');
            clearSelect2Fields([select2VolumeData]);
                $.ajax({
                    url: Routing.generate('mouza_volumes', {'mouzaId': mouzaId}),
                    type: "GET",
                    dataType : "json",
                    success: function( response ) {
                        unBlockContent();
                        for (var k in response) {
                            select2VolumeData.push(response[k]);
                        }
                    },
                    error: function() {
                        unBlockContent();
                    },
                    beforeSend: function() {
                        blockContent();
                    }
                });

        });

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
        function clearSelect2Fields(fieldData) {
            for(var k in fieldData) {
                var last = fieldData[k].length;
                fieldData[k].splice(0, last);
            }
        }


    }
    function mouzaValidation(){

        $('form#form-search').find('input').each(function(){
            if(!$(this).prop('required')){
                alert('ok ');
                return true;
            } else {
                alert('মৌজা  নির্বাচন করতে হবে  । ');
                return false;
            }
        });
    }

    if ($("#dc-delivery-khatian-register-report-page").length) {

    }

});