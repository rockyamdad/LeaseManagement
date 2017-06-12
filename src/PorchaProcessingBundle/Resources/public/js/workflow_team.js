var exSelected = [];

$("#porcha_processing_workflow_team_upozilas").change(function() {

    var elmMouzas = $("#porcha_processing_workflow_team_mouzas");
    var upozilas = $(this).select2("val");

    if(upozilas.length < 1) {
        elmMouzas.select2("val", null);
        return false;
    }

    $.ajax({

        url:Routing.generate('upozila_mouzas',{upozilas: upozilas}),
        type: "GET",
        dataType: 'json'
    }).done(function ($msg) {

        elmMouzas.select2("val").forEach(fillExSelected);
        elmMouzas.find('option').remove();

        var totalOption = $msg.length;
        for (var i = 0; i < totalOption; i++) {
            elmMouzas.append($msg[i]);
        }

        elmMouzas.select2("val", exSelected);
    });
});

$('.table-data').on('click', '.btn-delete', function(e) {

    e.preventDefault();
    if (!confirm('আপনি কি নিশ্চিত ?')) {
        return false;
    }

    AppUtil.blockTableContent();
    var button = $(this);
    $.ajax({
        url: button.attr('href'),
        data: {'_token': button.attr('data-csrf')},
        type: "POST",
        dataType : "html",
        success: function( response ) {
            alert('গ্রুপ মুছে ফেলা হয়েছে ');
            AppUtil.unBlockTableContent();
            window.location = window.location.href ;
        }
    });
});

$('.sticky-header').floatThead();

function fillExSelected(item, index) {
    exSelected.push(item);
}
