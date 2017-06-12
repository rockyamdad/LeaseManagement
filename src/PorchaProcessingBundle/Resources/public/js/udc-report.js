$(function() {

    if ($("#udc-report-page").length) {
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
    }
});