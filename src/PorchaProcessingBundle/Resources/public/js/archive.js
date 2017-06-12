$(function() {

    if ($("#khatian-search-page").length) {

        $(".table-data").on("click","#chk-all", function() {
            if ($("#chk-all").is(':checked')) {
                $("input:checkbox").prop('checked', true);
            } else {
                $("input:checkbox").prop('checked', false);
            }
        });

        $("#btn-form-multi-action").click(function (e) {

            if ($('.chk:checked').length < 1) {
                alert('খতিয়ান নির্বাচন করুন ');
                return false;
            }

            if ($('#multiAction').val() == '') {
                alert('অপশন নির্বাচন করুন ');
                return false;
            }

            e.preventDefault();
            if (!confirm('আপনি কি নিশ্চিত ?')) {
                return false;
            }

            var data = $('#form-multi-action').serializeArray();

            AppUtil.blockTableContent();
            $.post( Routing.generate('archive_search_khatian'), data)
                .done(function( response ) {
                    if (response != 'FAILED') {
                        $(".table-data").html(response);
                        alert('কাজটি সম্পন্ন হয়েছে ');
                    } else {
                        alert('অনাকাঙ্ক্ষিত ত্রুটি ');
                    }
                    AppUtil.unBlockTableContent();
                }).error(function() {
                    alert('অনাকাঙ্ক্ষিত ত্রুটি ');
                    AppUtil.unBlockTableContent();
                });

            return false;
        });

        $('#multiAction').change(function() {
            if ($(this).val() == 'CORRECTION_REQUIRED') {
                $('#entryOperators').show();
            } else {
                $('#entryOperators').hide();
            }
        });

    }

});