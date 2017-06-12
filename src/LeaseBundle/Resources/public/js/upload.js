var DocumentUpload = function () {
    var $fileupload = $('#fileupload');

    var validationFailed = function(){

        if($('.files td.start').length < 1 ) {
            return 'You must select some valid files!!';
        }

        if ((typeof $fileupload.valid == 'function') && !$fileupload.valid()) {
            return "You must fill-up the required fields first"
        }

        return false;
    };

    var handleUpload = function (maxFileSize, UploadType) {

        var type = UploadType || 'document';

        $('.document-upload').click(
            function (e) {
                e.preventDefault();
                var errorMessage = validationFailed();

                if (errorMessage) {

                    $.gritter.add({
                        title: 'Error!!',
                        text: errorMessage,
                        sticky: false
                    });

                    return;
                }

                $('.fileupload-buttonbar').find('button.start').click();
            }
        );

        // Initialize the jQuery File Upload widget:
        var url = $fileupload[0].action;

        $fileupload.fileupload({
            // Uncomment the following to send cross-domain cookies:
            //xhrFields: {withCredentials: true},
            url: url,
            acceptFileTypes: /(\.|\/)(png|jpe?g|tiff|pdf|doc|docx|xls|xlsx|ppt|pptx|rtf|flv|mpe?g|avi|mp4|mp3|wav)$/i,
            maxFileSize: maxFileSize
        }).bind('fileuploadprogressall', function (e, data) {
            var progressPercent, progress;
            progressPercent = parseInt(data.loaded / data.total * 100, 10);
            progress = parseInt(data.loaded / data.total, 10)

            $(".fileupload-progress").find('.progress')
                .attr('aria-valuenow', progressPercent)
                .find('.bar').css(
                    'width',
                    progressPercent + '%'
                );

            if (progress == 1) {
                $("#fileupload tbody.files").html('');

                $msg = type == 'document' ? 'All the document(s) with all associate data saved successfully!' : 'Upload completed!'

                $.gritter.add({
                    title: 'Upload status',
                    // (string | mandatory) the text inside the notification
                    text: $msg,
                    sticky: false
                });
                $fileupload.trigger("uploadCompleted");
            }
        });

    };

    var init = function (maxFileSize, UploadType) {
        handleUpload(maxFileSize, UploadType);
        // initialize uniform checkboxes
    };

    return {
        //main function to initiate the module
        init: init

    };

}();