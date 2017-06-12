
// ############ CK Editor setup
// Toolbar configuration guide: http://docs.ckeditor.com/#!/guide/dev_toolbar
// Button Names: http://s1.ckeditor.com/sites/default/files/uploads/Complete%20List%20of%20Toolbar%20Items%20for%20CKEditor.pdf
var ckEditorCommon = {
    uiColor: '#fafafa',
        //skin: 'icy_orange',
        toolbar: [
            ['Bold', 'Italic', 'Underline', "RemoveFormat"], ['Styles', 'Format',"FontSize", 'Table'],
            ['NumberedList', 'BulletedList', "Outdent", "Indent"],['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
            ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', "SpecialChar", '-', 'Undo', 'Redo'],
            ["Find", "Replace"], ['Source', 'Preview', "Maximize"]
        ]
};

var savingButton = $('<button type="button" class="btn btn-circle blue-madison"><i class="fa fa-refresh fa-spin"></i> Saving... </button>');
var loadingIcon = $('<i class="fa fa-circle-o-notch fa-spin"></i>');

function modalMessage(title, message)
{
    $('#common-modal #modal-title').html(title);
    $('#common-modal #modal-content').html(message);

    $('#common-modal').modal('show');
}

// ################ Functons for reordering by Drag-n-Drop ###############
function fixDragHelper(e, tr)
{
    var $originals = tr.children();
    var $helper = tr.clone();
    $helper.children().each(function(index)
    {
        $(this).width($originals.eq(index).width());
    });
    return $helper;
}

function activateOrdering(button, container)
{
    var buttonId = $(button).attr('id');
    $(container).find('tr td:first-child').prepend('<i class="fa fa-bars fa-fw font-grey-steel handle"></i> ');

    $(container).sortable({
        helper: fixDragHelper,
        cursor: "move",
        handle: ".handle",
        change: function( event, ui ) {
            $(button).removeClass('hidden');
            $(ui.item).addClass('bg-yellow-lemon');
        }
    });

    $(button).click(function() {
        saveReordering(button, container);
    });
}

function saveReordering(button, container)
{
    var order = $(container).sortable( "serialize");
    var url = $(button).data('url');
    var entity = $(button).data('entity');
    var saving = $(savingButton).clone(false);

    $(button).addClass('hidden').before(saving);
    $.post(url + '?entity=' + entity + '&' + order)
        .done(function(data) {
            if('OK' == data) {
                $(saving).addClass('green-haze').html('<i class="fa fa-check"></i> Order Saved');
                $(container).find('tr.bg-yellow-lemon').removeClass('bg-yellow-lemon');
                setTimeout('$("#'+ $(button).attr('id') +'").prev().fadeOut("slow", function() { $(this).remove(); })', 1500);
            }
        })
        .fail(function() {
            $(saving).html('<i class="fa fa-times font-red-pink"></i> Failed!');
        });
}

function isServiceRequestPostalAddressRequire()
{
    return $('#porchaprocessingbundle_servicerequest_deliveryMethod').find('[type=radio]:checked').val() === 'POSTAL';
}

var validateServiceRequestAddressFields = {
    required: {
        depends: function (element) {
            return isServiceRequestPostalAddressRequire();
        }
    }
};

// ################## Document load event ############################
$(document).ready(function() {
    var now = new Date();

    // Tick the checked radio in button groups
    $('.btn-group input[type=radio]:checked').parent('label').addClass('active');

    // Empty contents on hiding common modal
    $('#common-modal').on('hidden.bs.modal', function (e) {
        $('#common-modal #modal-title').empty();
        $('#common-modal #modal-content').empty();
    });

    // -------------- Generic delete confirmation ---------
    // https://gist.github.com/ajaxray/cee088f219fcdc292806
    $(document).on('click', 'a.remove, a.confirm', function(e) {
        e.preventDefault();
        var link = this;
        $('#delete-confirmation-msg').html($(this).data('message'));
        $('#delete-confirmation-yes').on('click', function() {
            var callback = $(link).data('callback');
            window[callback](link);

            $('#delete-confirmation').modal('hide');
        });

        if(yesBtn = $(this).data('yes-btn')) {
            $('#delete-confirmation-yes').html(yesBtn);
        }
        if(noBtn = $(this).data('no-btn')) {
            $('#delete-confirmation-no').html(noBtn);
        }

        $('#delete-confirmation').modal('show');
    });
    $('#delete-confirmation').on('hidden.bs.modal', function (e) {
        $('#delete-confirmation-yes').off();

        $('#delete-confirmation-msg').empty();
        $('#delete-confirmation-yes').text('Remove');
        $('#delete-confirmation-no').text('Cancel');
    });
    // END common delete confirmation -------------------------

    // few common callbacks for confirmation
    function submitForm(link)
    {
        $(link).closest('form').submit();
    }
    function goToLink(link)
    {
        window.location = $(link).data('url');
    }

    //-------- Handling file uploads --------------------------
    // https://gist.github.com/ajaxray/ac1d258c2425dea08047
    $(document).on('change', '.btn-file :file', function() {
        var input = $(this),
            filefield = input.get(0),
            numFiles = filefield.files ? filefield.files.length : 1;

        var acceptedTypes = input.data('file-types')? input.data('file-types').split(',') : false;

        //console.log(acceptedTypes);
        var labels = [];
        for (var x = 0; x < filefield.files.length; x++) {
            console.log(filefield.files[x].type);
            if(acceptedTypes && acceptedTypes.indexOf(filefield.files[x].type) < 0) {
                input.trigger('fileselectError', ['File type <b>'+ filefield.files[x].type +'</b> is not acceptable']);
                filefield.value = "";
                return false;
                // Check for size and other conditions same way
            } else {
                labels.push(filefield.files[x].name);
            }
        }

        input.trigger('fileselect', [numFiles, labels]);
        //$('#cover_photo').attr('src',)
    });

    $('.btn-file :file').on('fileselect', function(event, numFiles, labels) {
        var infoBox = $(this).parents('.fileinput').find('.fileList').empty();
        labels.forEach(function(filename){
            infoBox.append('<li class="font-blue-hoki"><i class="fa fa-file-pdf-o"></i> '+filename+'</li>');
        });
    });

    $('.btn-file :file').on('fileselectError', function(event, message) {
        var infoBox = $(this).parents('.fileinput').find('.fileList').empty();
        infoBox.append('<li class="text-danger"><i class="fa fa-warning"></i> '+message+'</li>');
    });
    // End Handling file uploads ******************

    $('#search-expand').click(function(){
        $('#form-search').toggle();
    });

    if (jQuery().datepicker) {
        $('.date-picker').datepicker({
            rtl: Metronic.isRTL(),
            orientation: "center",
            todayHighlight: true,
            language: 'bn',
            format: 'dd-mm-yyyy'
        });
    }

    $('.table-data').on('click', '.delivery-confirmation, .ready-for-delivery, .processing-confirmation', function(){
        var url = $(this).attr('href');

        var deliveryMsg = '<h3>প্রদান নিশ্চিতকরণ:</h3>';
        deliveryMsg += '<p>আবেদন আইডি: ' + $(this).attr('data-app-id') + '</p>';
        deliveryMsg += '<p>আবেদনকারীর নাম: ' + $(this).attr('data-app-name') + '</p>';
        if($(this).attr('data-app-khatian')){
        deliveryMsg += '<p>খতিয়ান : ' + $(this).attr('data-app-khatian') + '</p>';
        }
        var msg = $(this).hasClass('delivery-confirmation') ? 'প্রেরণ নিশ্চিতকরণ' : 'ডেলিভারির জন্য প্রস্তুত';
        if ($(this).hasClass('processing-confirmation')) {
            msg = 'প্রক্রিয়ার জন্য প্রস্তুত';
        } else if ($(this).hasClass('ready-for-delivery')) {
            msg = 'ডেলিভারির জন্য প্রস্তুত';
        } else if ($(this).hasClass('delivery-confirmation')) {
            msg = deliveryMsg;
        }
        bootbox.confirm(msg, function(result){
            if (result) {
                Metronic.blockUI({
                    target: $('.table-data'),
                    animate: true,
                    overlayColor: 'black'
                });

                $.ajax({
                    url: url,
                    dataType: 'json',
                    success: function(json){
                        Metronic.unblockUI($('.table-data'));
                        if (json.status && json.status == 'success') {
                            $('#btn-search').trigger('click');
                        }
                        bootbox.alert(json.message);
                    },
                    error: function(){
                        Metronic.unblockUI($('.table-data'));
                        bootbox.alert("অনাকাঙ্ক্ষিত ত্রুটি, অনুগ্রহ করে আবার চেষ্টা করুন|");
                    }
                });
            }
        });

        return false;
    });

    $('#btn-print').on('click', function(){
        var url = $(this).attr('data-url');
        var requestDate = $('.date-picker');

        if ($.trim(requestDate.val()) == '') {
            bootbox.alert('Date is require for print');
            return false;
        }

        window.open(url+'?'+$('#form-search').serialize()+'&view-type=print', 'Print', "height=600,width=900");
    });



});
