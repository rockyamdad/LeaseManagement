var PorchaProcessing = function (){

    function init()
    {
        if ($("#volume-list-page").length) {

            $(document).on("click", ".open-dialog", function () {
                var url = $(this).data('url');
                $("#new-khatian-form").attr("action", url);
            });
        }


    }

    return {
        'init': init
    };
}();