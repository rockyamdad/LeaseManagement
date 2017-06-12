$(function() {


    if ($('#udc-page').length) {

        $('#udc-form-page').validate({
            lang: 'bn_BD',
            errorElement: 'span', //default input error message container
            errorClass: 'help-block help-block-error', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "",  // validate all fields including form hidden input
            rules: {
                'udc[user][username]': {
                    required: true,
                    remote: {
                        url: Routing.generate('username_duplicate_check'),
                        data: {
                            'user_id': function () {
                                return $('#user_id') ? $('#user_id').val() : "";
                            }
                        },
                        type: 'post'
                    }
                },
                'udc[user][plainPassword][first]': {
                    minlength: 6
                },

                'udc[user][plainPassword][second]': {
                    equalTo: '#udc_user_plainPassword_first'
                }
            },
            messages : {
                'udc[user][username]': {
                    remote: jQuery.validator.format('এই ব্যবহারকারীর নাম নিবন্ধন করা আছে ')
                }
            },


            highlight: function (element) {
                $(element)
                    .closest('.form-group').addClass('has-error');
            },


            unhighlight: function (element) {
                $(element)
                    .closest('.form-group').removeClass('has-error');
            },


            submitHandler: function (form) {
                form.submit();
            }
        });



        $("#udc_district").change(function () {

            var id = $(this).val();
            console.log(id);
            if (id == '') {
                return false;
            }
            $.ajax({

                url: Routing.generate('district_upozilas', {id: id}),
                dataType: 'json'
            }).done(function ($msg) {

                $('#udc_upozila option').remove();
                $('#udc_upozila').append('<option value="">নির্বাচন করুণ</option>');

                var totalOption = $msg.length;
                for (var i = 0; i < totalOption; i++) {
                    $('#udc_upozila').append($msg[i]);
                }
            });
        });

        $("#udc_upozila").change(function () {

            var id = $(this).val();
            console.log(id);
            if (id == '') {
                return false;
            }
            $.ajax({

                url: Routing.generate('upozilas_unions', {id: id}),
                dataType: 'json'
            }).done(function ($msg) {

                $('#udc_union option').remove();
                $('#udc_union').append('<option value="">নির্বাচন করুণ</option>');

                var totalOption = $msg.length;
                for (var i = 0; i < totalOption; i++) {
                    $('#udc_union').append($msg[i]);
                }
            });
        });

        // this function for udc Entrepreneur Collection Form
        udcEntrepreneurCollectionForm();

    }

    if ($('#udc-user-list-page').length) {

        $("#district").change(function () {
            AppUtil.fetchData($(this).attr('data-url'));
            return false;
        });

        $('.table-data').on('click', '#chk-all', function() {

            if ($("#chk-all").is(':checked')) {
                $("input:checkbox").prop('checked', true);
            } else {
                $("input:checkbox").prop('checked', false);
            }
        });

        $("#btn-form-multi-action-udc-user").click(function (e) {

            if ($('#selectedChk').val() == '') {
                alert('অবস্থা নির্বাচন করুন ');
                return false;
            }

            if ($('.chk:checked').length < 1) {
                alert(' ইউজার  নির্বাচন করুন ');
                return false;
            }

            e.preventDefault();
            if (!confirm('আপনি কি নিশ্চিত ?')) {
                return false;
            }

            var data = $('#form-multi-action-udc-user').serializeArray();

            $.post( Routing.generate('udc_user_status_change'), data)
                .done(function( response ) {
                    $(".table-data").html(response);
                    alert('কাজটি সম্পন্ন হয়েছে ')
                });

            return false;
        });
    }


});




function udcEntrepreneurCollectionForm()
{

    var $collectionHolder;

// setup an "add a tag" link
    var $addTagLink = $('<td><div><a href="#" class="btn btn-xs add_tag_link"><i class="fa fa-plus"></i> যোগ করুন</a></div></td>');

    var $newLinkLi = $('<tr></tr>').append($addTagLink);

    jQuery(document).ready(function() {
        // Get the ul that holds the collection of tags
        $collectionHolder = $('#udcEntrepreneurs');

        // add the "add a tag" anchor and li to the tags ul
        $collectionHolder.append($newLinkLi);

        // count the current form inputs we have (e.g. 2), use that as the new
        // index when inserting a new item (e.g. 2)
        $collectionHolder.data('index', $collectionHolder.find(':input').length);

        $addTagLink.on('click', function(e) {
            // prevent the link from creating a "#" on the URL
            e.preventDefault();

            // add a new tag form (see next code block)
            addTagForm($collectionHolder, $newLinkLi);
        });
        $addTagLink.trigger('click');
        //$addTagLink.trigger('click');
        $addTagLink.hide('click');
    });
}
function addTagForm($collectionHolder, $newLinkLi) {
    // Get the data-prototype explained earlier
    var prototype = $collectionHolder.data('prototype');

    // get the new index
    var index = $collectionHolder.data('index');

    // Replace '__name__' in the prototype's HTML to
    // instead be a number based on how many items we have
    var newForm = prototype.replace(/__name__/g, index);

    // increase the index with one for the next item
    $collectionHolder.data('index', index + 1);

    // Display the form in the page in an li, before the "Add a tag" link li
    var $newFormLi = $('<tr></tr>').append(newForm);
    $newLinkLi.before($newFormLi);

    $("#udc_udcEntrepreneurs_"+ index +"_remove").click(function () {

        var parent     = $(this).closest('tr');
        parent.remove();

    });
}
