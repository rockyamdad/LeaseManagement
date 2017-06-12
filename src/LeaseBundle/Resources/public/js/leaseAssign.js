var leaseAssign= $('#fileupload');
var error = $('.alert-danger', leaseAssign);
var success = $('.alert-success', leaseAssign);

leaseAssign.validate({
    errorElement: 'span', //default input error message container
    errorClass: 'help-inline', // default input error message class
    focusInvalid: false, // do not focus the last invalid input
    ignore: ".ignore",
    rules: {
        'leasebundle_registerleasesix[demandFee]': {
            required:true
        },
        'leasebundle_registerleasesix[chalanNo]': {
            required:true

        },
        'leasebundle_registerleasesix[chalanDate]': {
            required:true

        },
        'leasebundle_registerleasesix[chalanAmount]': {
            required:true

        },
        'leasebundle_registerleasesix[paymentDate]': {
            required:true
        },
        'leasebundle_registerleasesix[paymentAmount]': {
            required:true
        },
        'leasebundle_registerleasesix[paymentChalanNo]': {
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
       if(element.attr('id') == 'leasebundle_registerleasesix_chalanDate' || element.attr('id') == 'leasebundle_registerleasesix_paymentDate') {
            element.parent('.input-group').after(error);
        } else {
            error.insertAfter(element);
        }
    }

});

$('.select2', leaseAssign).change(function () {
    leaseAssign.validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input
});




$(document).ready(function(){
    $("#selected_doc_file1").on('click', '.remove-old', function() {
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

    $("#demandFee").blur('click',function() {
        $('#leasebundle_registerleasesix_chalanAmount').empty();
            var demandFee = $(this).val();
            var type = $(this).attr('rel');
        if(type == 'WaterBody'){
            $.ajax({
                type: "POST",
                url: Routing.generate('challan_amount',{'demandFee':demandFee}),
                success: function (data) {
                    $('#leasebundle_registerleasesix_chalanAmount').append(data);
                }
            });
        }else{
            $.ajax({
                type: "POST",
                url: Routing.generate('challan_amount_market',{'demandFee':demandFee}),
                success: function (data) {
                    $('#leasebundle_registerleasesix_chalanAmount').append(data);
                }
            });

        }

    });


});

if($('#register').length > 0){
    var register = new SFileInput('register',{
        button:'register',
        required:true,
        allowedType:'document|image',
        multipleFile:true,
        selectedFileLabel:'selected_doc_file3',
        showMessage: function(a) {
            alert("Register document is required");
        }
    });
}

if($('#application_terminated').length > 0){
    var doc_file2 = new SFileInput('application_terminated',{
        button:'application_terminated',
        allowedType:'document|image',
        multipleFile:true,
        selectedFileLabel:'selected_doc_file1'
    });
}
