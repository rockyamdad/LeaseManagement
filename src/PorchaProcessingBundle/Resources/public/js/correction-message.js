$.fn.center = function () {
    //this.css("position","absolute");
    //this.css("top", ( $(window).height() - this.height() ) / 2+$(window).scrollTop() + "px");
    this.css("left", ( $(window).width() - this.width() ) / 2+$(window).scrollLeft() + "px");
    return this;
};

$(function() {

    $('#correction-message').click(function(){
        var currentPage = $('.print-page:visible');
        var modal = currentPage.find('.correction-form');

        if (!modal.length) return;
        var textAreaId = modal.find('textarea').attr('id');
        //$('#porcha_processing_khatian_correction_log label').hide();

        $.blockUI({
            message: modal,
            title: 'সংশোধনী  বার্তা ',        // title string; only used when theme == true
            draggable: true,    // only used when theme == true (requires jquery-ui.js to be loaded)
            theme: true,
            showOverlay: false,
            themedCSS: {
                width: '70%',
                top: '20%',
                //left: ($(window).width() / 2) - ($('#correction-form').width() / 2),
                padding: '0',
                backgroundColor:'#fff'
            },
            ignoreIfBlocked: true
        });
        $('.blockUI.blockMsg').center();

        $('.ui-widget-content.ui-dialog-content').css('background-color', 'white');

        CKEDITOR.config.toolbar = [
            ['Bold','Italic','Underline','Strike','Table']
        ] ;
        CKEDITOR.config.height = '250px';
        CKEDITOR.replace(textAreaId);
        console.log(textAreaId);
        return false;

    });

    $('.close').click(function(){
        $.unblockUI();
        console.log($(this).attr('rel'));
        CKEDITOR.instances[$(this).attr('rel')].destroy();
    });

    $(window).resize(function() {
        $('.blockUI.blockMsg').center();
    });

    $('.print-pagination').click(function(){

        var pageId = $(this).attr('rel');
        if (pageId) {
            $('.print-pagination').removeClass('active');
            $(this).addClass('active');

            $('.print-page').hide();
            $('#print-page-'+$(this).attr('rel')).show();
        }

        return false;
    });

    $('.print-pagination:eq(0)').addClass('active');

    $('button.action').click(function(){
        var response = confirm('আপনি কি নিশ্চিত ?');
        if (response) {
            $('#porcha_processing_workflow_action_workflowAction').val($(this).attr('data-action')).parents('form').submit();
        }
        return false;
    });

});
