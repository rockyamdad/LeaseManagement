$(function() {

    $('.table-data').on('change', '#check-all', function(){
        $('.table-data').find('.check').attr('checked', $(this).is(':checked'));
    });

    $('button[name=archive]').click(function(e){

        if (!$('.table-data').find('.check:checked').length) {
            bootbox.alert("কোন খতিয়ান নির্বাচন করা হয়নি");
            return false;
        }

        bootbox.confirm("আপনি কি নিশ্চিত ?", function(result) {
            if (result) {
                $('#archived-list-form').submit();
            }
        });

        return false;
    });

    $("#chk-all").click(function() {
        if ($("#chk-all").is(':checked')) {
            $("input:checkbox").prop('checked', true);
        } else {
            $("input:checkbox").prop('checked', false);
        }
    });

    $("#btn-form-multi-action").click(function (e) {

        if ($('#selectedChk').val() == '') {
            return false;
        }

        if ($('.chk:checked').length < 1) {
            alert('খতিয়ান নির্বাচন করুন ');
            return false;
        }

        e.preventDefault();
        if (!confirm('আপনি কি নিশ্চিত ?')) {
            return false;
        }

        var data = $('#form-multi-action').serializeArray();

        $.post( Routing.generate('workflow_move_khatians_to_next_step'), data)
            .done(function( response ) {
                window.location = window.location.href;
            });

    });

    $("#btn-multi-print-action").click(function (e) {

        if ($('.chk:checked').length < 1) {
            alert('খতিয়ান নির্বাচন করুন ');
            return false;
        }

        e.preventDefault();
        if (!confirm('আপনি কি নিশ্চিত ?')) {
            return false;
        }

        $.post( Routing.generate('khatian_selected_khatians_print_view'), $('#form-multi-action').serializeArray())
            .done(function( response ) {
                var popupWin = window.open('', '_blank', 'width=auto,height=auto');
                popupWin.document.open();
                popupWin.document.write(response);
                popupWin.document.close();
            });

    });
});