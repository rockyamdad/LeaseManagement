$(function() {

    $('#print').click(function(){
        window.open(window.location.href + '?print=yes&' + $('#form-search').serialize());

        return false;
    });

    $('#address-print').click(function(){
        var date = $("[name='ff[sr.createdAt]']").val();

        if(!date){
            alert('তারিখ প্রদান করুন ।');
            return false;
        }
        //e.preventDefault();
        window.open($(this).attr('href') + '?address-print=yes&' + $('#form-search').serialize());

        return false;
    });

    $('#request-detail').on('hidden.bs.modal', function (e) {
        $(this).removeData('bs.modal');
        $(this).find('.modal-content').html('<div class="modal-body">'+
            '<img src="/assets/layout3/img/loading-spinner-grey.gif" alt="" class="loading">' +
            '<span> &nbsp;&nbsp;Loading... </span></div>');
    });

    /*$('#request-detail')
        .on('click', '#delivery-confirmation, #receive-confirmation', function () {
            var that = $(this);
            var url = that.attr('data-url');
            that.attr('disabled', true);
            $.ajax({
                url: url,
                dataType: 'json',
                success: function(){
                    $('#request-detail').modal('hide');
                    bootbox.alert(that.attr('data-success-msg'));
                },
                error: function(){
                    bootbox.alert(that.attr('data-error-msg'));
                    that.attr('disabled', false);
                }
            });

        }).on('click', '#receive-confirmation', function () {

            var that = $(this);
            var url = that.attr('data-url');
            console.log(url);

        console.log('hello');
    }).on('submit', '#service-status-update', function(){

        $.ajax({
            url: $(this).attr('action'),
            type: 'post',
            dataType: 'json',
            data: $(this).serialize(),
            success: function(json){
                if (json.status && json.status == 'success') {
                    $('#request-detail').modal('hide');
                }
                bootbox.alert(json.message);
            },
            error: function(){
                bootbox.alert("অনাকাঙ্ক্ষিত ত্রুটি, অনুগ্রহ করে আবার চেষ্টা করুন|");
                $('#request-detail').modal('hide');
            }
        });

        return false;
    });*/

});