var gadget= $('#gadget_form');
var error = $('.alert-danger', gadget);
var success = $('.alert-success', gadget);


// initialise plugin
var telInput = $(".phoneNo"),
    errorMsg = $(".error-msg"),
    validMsg = $(".valid-msg");

var reset = function() {
    telInput.removeClass("error");
    telInput.closest('.form-group').removeClass('has-success');
    telInput.closest('.form-group').removeClass('has-error');
    errorMsg.addClass("hide");
    validMsg.addClass("hide");
};
gadget.on('click', function () {
    var intlNumber = telInput.intlTelInput("getNumber");
    if (intlNumber) {
        telInput.val(intlNumber);
    }
});
// on blur: validate
telInput.blur(function() {
    reset();
    if ($.trim(telInput.val())) {
        var intlNumber = telInput.intlTelInput("getNumber");
        if (intlNumber) {
            telInput.val(intlNumber);
        }
        if (telInput.intlTelInput("isValidNumber")) {
            telInput.closest('.form-group').removeClass('has-error').addClass('has-success');
            validMsg.removeClass("hide error").addClass("has-success");
            $("#code").removeClass("hide");
        } else {
            telInput.addClass("error");
            telInput.closest('.form-group').removeClass('has-success').addClass('has-error');
            errorMsg.removeClass("hide");
        }
    }
});

// on keyup / change flag: reset
telInput.on("keyup change", reset);
telInput.intlTelInput({
    utilsScript: "/assets/plugins/intl-tel-input-master/build/js/utils.js",
    nationalMode: true,
    preferredCountries: ['bd', 'in']
});
gadget.validate({
    errorElement: 'span', //default input error message container
    errorClass: 'help-inline', // default input error message class
    focusInvalid: false, // do not focus the last invalid input
    ignore: ".ignore",
    rules: {
        'leasebundle_gadget[orginalOwnerName]': {
            required:true
        },
        'leasebundle_gadget[fatherName]': {
            required:true

        },
        'leasebundle_gadget[address]': {
            required:true

        },
        'leasebundle_gadget[caseFileNo]': {
            required:true

        },
        'leasebundle_gadget[govtAquiredDate]': {
            required:true
        }
    },
    invalidHandler: function (event, validator) { //display error alert on form submit
        success.hide();
        error.show();
        Metronic.scrollTo(error, -200);
    },

    highlight: function (element) { // hightlight error inputs
        $(element)
            .closest('.form-group').removeClass('has-success').addClass('has-error'); // set error class to the control group
    },

    unhighlight: function (element) { // revert the change done by hightlight
        $(element)
            .closest('.form-group').removeClass('has-error'); // set error class to the control group
    },

    success: function (label) {
        label
            .addClass('valid') // mark the current input as valid and display OK icon
            .closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
    },
    errorPlacement: function(error, element) {

        if(element.attr('class') == 'form-control phoneNo form-control') {
            element.parent('.input-group').after(error);
        } else if(element.attr('class') == 'date2 form-control') {
            element.parent('.input-group').after(error);
        } else {
            error.insertAfter(element);
        }
    }

});

$('.select2', gadget).change(function () {
    gadget.validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input
});

$('#add-khatian-info').click(function(e) {
    e.preventDefault();
    var mouza = $('#mouza').val();
    if(mouza==''){
        alert('মৌজা নির্বাচন করতে হবে ');
        $('#info-list').closest('tr').remove();
        return false;
    }else{

        addKhatianInfo();
    }

});

function addKhatianInfo() {

    var list = jQuery('#row-clone');
    var newWidget = list.attr('data-prototype');

    newWidget = newWidget.replace(/__name__/g, rowCount);
    rowCount++;

    var newLi = jQuery('<tr></tr>').html(newWidget);
    newLi.appendTo($('#info-list'));
}
$('#info-list').on('click', '.remove-row', function() {
    rowCount--;
    $(this).closest('tr').remove()
});

$('.mouza').click(function (){

    var mouza = $('#leasebundle_gadget_mouza').val();
    if(mouza==''){
        alert('মৌজা নির্বাচন করতে হবে ');
        $('#add-khatian-info').addClass('hide');
        $('#info-list').closest('tr').remove();
        $('.sa-khatian, .rs-khatian').html("<option value=''>খতিয়ান নির্বাচন করুন</option>");
        return false;
    }else{
        var list = jQuery('#row-clone');
        $('#add-khatian-info').removeClass('hide');
        $.get(Routing.generate('gadget_khatian_load', {'id': mouza}), function (response) {

            var newWidget = list.attr('data-prototype');

            var newLi = jQuery('<tr></tr>').html(newWidget);

            newLi.find('.sa-khatian').html(response['SA']);
            newLi.find('.rs-khatian').html(response['RS']);

            list.attr('data-prototype', newLi.html());

            $('.sa-khatian').html(response['SA']);
            $('.rs-khatian').html(response['RS']);
        });
    }
});


if($('#doc_upload1').length > 0){
    var doc_file = new SFileInput('doc_upload1',{
        button:'doc_upload1',
        required:true,
        allowedType:'document|image',
        multipleFile:true,
        selectedFileLabel:'selected_doc_file1',
        showMessage: function(a) {
            alert("Private document is required" + a);
        }
    });
}

if($('#doc_upload2').length > 0){
    var doc_file2 = new SFileInput('doc_upload2',{
        button:'doc_upload2',
        required:true,
        allowedType:'document|image',
        multipleFile:true,
        selectedFileLabel:'selected_doc_file2',
        showMessage: function(a) {
            alert("Public document is required" + a);
        }
    });
}

$(document).ready(function(){
    $(".selectedFileClass").on('click', '.remove-old', function() {
        if(confirm("এই ফাইল পুনরুদ্ধার করা যাবে না, ফাইল সিস্টেম থেকে মুছে ফেলা হবে, আপনি কি নিশ্চিত?")){
            var id = $(this).attr('ref');
            var parent = this.parentNode;
            $.ajax({
                type: "POST",
                url: Routing.generate('lease_document_delete',{'id':id}),
                success: function (data) {
                    parent.parentNode.removeChild(parent);
                }
            });
        }
    });

    $(".remove_old_gadget_details").on('click', function() {
        rowCount--;
        if(confirm("এই ফাইল পুনরুদ্ধার করা যাবে না, ফাইল সিস্টেম থেকে মুছে ফেলা হবে, আপনি কি নিশ্চিত?")){
            var id = $(this).attr('id');
            var $tr = $(this).closest('tr');
            $.ajax({
                type: "POST",
                url: Routing.generate('gadget_details_delete',{'id':id}),
                success: function (data) {
                    $tr.find('td').fadeOut(500,function(){
                        $tr.remove();
                    });
                }
            });
        }
    });

});
$("body").on('blur', '.proposedAmount', function() {
    var parent = $(this).closest('tr');

    var proposedAmount = parseInt(parent.find('.proposedAmount').val());
    var totalAmount = parseInt(parent.find('.totalAmount').val());

    if(totalAmount < proposedAmount){
        parent.find('.proposedAmount').val('');
        parent.find('.totalAmount').val('');
        alert('এরর !!! মোট পরিমান প্রস্তাবিত  পরিমান  থেকে বড়  হবে ');
        return false;
    }

});

/*
var sainput= new SFileInput('nid',{
    button:'nid',
    allowedType:'document',
    multipleFile:false,
    selectedFileLabel:'selected_nid_file'
});
var sainput = new SFileInput('photo',{
    button:'photo',
    allowedType:'image',
    multipleFile:false,
    selectedFileLabel:'selected_photo_file'
});


    var sainput = new SFileInput('trade',{
        button:'trade',
        allowedType:'document',
        multipleFile:false,
        selectedFileLabel:'selected_trade_file'
    });
*/

