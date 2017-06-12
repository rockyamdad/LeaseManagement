var lease= $('#fileupload');
var error = $('.alert-danger', lease);
var success = $('.alert-success', lease);



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
lease.on('click', function () {
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
lease.validate({
    errorElement: 'span', //default input error message container
    errorClass: 'help-inline', // default input error message class
    focusInvalid: false, // do not focus the last invalid input
    ignore: ".ignore",
    rules: {
        'leasebundle_lease[type]': {
            required:true
        },
        'leasebundle_lease[khatian]': {
            required:true

        },
        'leasebundle_lease[name]': {
            required:true

        },
        'leasebundle_lease[shotangso]': {
            required:true
        },
        'leasebundle_lease[applications][0][registerSix][0][paymentAmount]': {
            required:true
        },
        'leasebundle_lease[applications][0][registerSix][0][demandFee]': {
            required:true
        },
        'leasebundle_lease[applications][0][registerSix][0][chalanNo]': {
            required:true
        },
        'leasebundle_lease[applications][0][registerSix][0][chalanAmount]': {
            required:true
        },
        'leasebundle_lease[applications][0][registerSix][0][paymentChalanNo]': {
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
        }  else {
            error.insertAfter(element);
        }
    }/*,
    submitHandler: function(form){
        if(sainput2.validate() && sainput1.validate()) {
            $(form)[0].submit();
        }

    }*/

});

$('.select2', lease).change(function () {
    lease.validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input
});

$('#add-dag-no').click(function(e) {
    e.preventDefault();

    var elmkhatian = $('#leasebundle_lease_khatian');

    if (elmkhatian.val() == '') {
        $('#fileupload').validate().element('#leasebundle_lease_khatian');
        return false;
    }

    addKhatianDagNo();
    $lastSelect = $('#dag-list select:last');
});

function addKhatianDagNo() {

    var list = jQuery('#row-clone');
    var newWidget = list.attr('data-prototype');

    newWidget = newWidget.replace(/__name__/g, rowCount);
    rowCount++;

    var newLi = jQuery('<tr></tr>').html(newWidget);
    newLi.appendTo($('#dag-list'));
}
$('#dag-list').on('click', '.remove-row', function() {
    $(this).closest('tr').remove()
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

$('#leasebundle_lease_khatian').click(function (){
    var elmkhatian = $('#leasebundle_lease_khatian');

    if (elmkhatian.val() == '') {
        $('#fileupload').validate().element('#leasebundle_lease_khatian');
        return false;
    }

    addKhatianDagNo();
    $lastSelect = $('#dag-list select:last');
});


$('.mouza , .survey').click(function (){
    $('#leasebundle_lease_khatian').empty();

    var mouza = $('#mouza').val();
    var survey = $('#survey').val();
    if(survey=='' || mouza==''){
       // alert('মৌজা এবং সার্ভে ২ টা ই নির্বাচন করতে হবে ');
        return false;
    }else{
        $.ajax({
            type: "Get",
            url: Routing.generate('khatian_load', {'id': mouza}),
            data: {'data':survey},
            success: function (data) {
                $('#leasebundle_lease_khatian').append(data);
            }
        });
    }

});

$(document).ready(function(){
    $(".selectedFileClass").on('click', '.remove-old', function() {
        if(confirm("This action can't be undone. The file will be deleted from system. Ar you sure?")){
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

    $(".remove-old-lease_details").on('click', function() {
        if(confirm("This action can't be undone. The file will be deleted from system. Ar you sure?")){
            var id = $(this).attr('ref');
            var $tr = $(this).closest('tr');
            $.ajax({
                type: "POST",
                url: Routing.generate('lease_details_delete',{'id':id}),
                success: function (data) {
                    $tr.find('td').fadeOut(500,function(){
                        $tr.remove();
                    });
                }
            });
        }
    });

    $("#demandFee").blur('click',function() {
        $('#leasebundle_lease_applications_0_registerSix_0_chalanAmount').empty();
        var demandFee = $(this).val();
        var type = $(this).attr('rel');
        if(type == 'WaterBody'){
            $.ajax({
                type: "POST",
                url: Routing.generate('challan_amount',{'demandFee':demandFee}),
                success: function (data) {
                    $('#leasebundle_lease_applications_0_registerSix_0_chalanAmount').append(data);
                }
            });
        }else{
            $.ajax({
                type: "POST",
                url: Routing.generate('challan_amount_market',{'demandFee':demandFee}),
                success: function (data) {
                    $('#leasebundle_lease_applications_0_registerSix_0_chalanAmount').append(data);
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

if($('#register').length > 0){
    var register = new SFileInput('register',{
        button:'register',
        required:true,
        allowedType:'document|image',
        multipleFile:true,
        selectedFileLabel:'selected_doc_file3',
        showMessage: function(a) {
            alert("Register document is required" + a);
        }
    });
}

/*if($('#nid').length > 0){
    var nid = new SFileInput('nid',{
        button:'nid',
        allowedType:'document',
        multipleFile:false,
        selectedFileLabel:'selected_nid_file'
    });
}*/




/*
var path = window.location.pathname;
if(path.indexOf('water') != -1) {
    if($('#resulation').length > 0){
        var resulation = new SFileInput('resulation',{
            button:'resulation',
            allowedType:'document',
            multipleFile:false,
            selectedFileLabel:'selected_resulation_file'
        });
    }

    if($('#audit').length > 0){
        var audit = new SFileInput('audit',{
            button:'audit',
            allowedType:'document',
            multipleFile:false,
            selectedFileLabel:'selected_audit_file'
        });
    }

    if($('#nibondhon').length > 0){
        var nibondhon = new SFileInput('nibondhon',{
            button:'nibondhon',
            allowedType:'document',
            multipleFile:false,
            selectedFileLabel:'selected_nibondhon_file'
        });
    }

}
if(path.indexOf('market') != -1) {

    if($('#trade').length > 0){
        var trade = new SFileInput('trade',{
            button:'trade',
            allowedType:'document',
            multipleFile:false,
            selectedFileLabel:'selected_trade_file'
        });
    }


}
*/
