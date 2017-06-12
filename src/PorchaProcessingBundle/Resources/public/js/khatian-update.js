$(function() {

    function insertFraction( editor ) {
        var firstNo = prompt('First Number');
        if (firstNo) {
            var secondNo = prompt('Second Number');
            if (secondNo) {
                editor.insertHtml('<span class="entry-area-fraction"><span class="first">' + firstNo + '</span><span class="last">' + secondNo + '</span></span> &nbsp;');
            }
        }
    }

    function getKeyMap() {
        var mainKey = CKEDITOR.SHIFT;
        var keyMaps = {};
        /*keyMaps[mainKey + 49] = '৴'; // 1
        keyMaps[mainKey + 50] = '৵'; // 2
        keyMaps[mainKey + 51] = '৶'; // 3
        keyMaps[mainKey + 52] = '৷'; // 4
        keyMaps[mainKey + 53] = '৸'; // 5
        keyMaps[mainKey + 54] = '॰'; // 6
        keyMaps[mainKey + 55] = '৲'; // 7
        keyMaps[mainKey + 56] = '८'; // 8*/
        keyMaps[2228416] = '_'; // ~
        keyMaps[1114304] = insertFraction; // CTRL + ~

        return keyMaps;
    }


    $('.entry-area').each( function () {
        var height = $(this).parent()[0].tagName == 'PRE' ? '50' : $('#' + this.id).parents('td').height();
        CKEDITOR.replace(this.id, {
            easykeymaps: getKeyMap(),
            removePlugins: 'toolbar,elementspath,resize',
            contentsCss: '/assets/ckeditor/custom.css',
            extraPlugins: 'easykeymap,autocorrect',
            allowedContent: 'hr br;span[class]',
            enterMode: Number(2),
            bodyClass: 'entry-area',
            on: {
                'instanceReady': function (evt) {
                    evt.editor.resize("100%", height);
                }
            }
        });
    });



    $('.workflow-action').click(function(){

        var error = requiredCheck();

        if ( error ) {
            return false;
        }

        var that = $(this);

        var formEntry = $('#update-khatian');
        var url = $('#entry_action_url').val();
        formEntry.attr('action', url);
        formEntry.attr('target', '_self');

        if (that.attr('data-action') != 'DRAFT') {
            bootbox.confirm("আপনি কি নিশ্চিত ?", function(result) {
                if (result) {
                    if (that.hasClass('next-entry')) {
                        $('#next-entry').val(1)
                    }
                    $('#khatian-action').val(that.attr('data-action')).parents('form').submit();
                }
            });
        } else {
            $('#khatian-action').val(that.attr('data-action')).parents('form').submit();
        }
        return false;
    });

    $('.page-action').click(function() {

        var error = requiredCheck();

        if ( error ) {
            return false;
        }

        $('#page-template').val($(this).attr('data-val'));
        var formEntry = $('#update-khatian');
        var url = $('#entry_action_url').val();
        formEntry.attr('action', url);
        formEntry.attr('target', '_self');
        $('#khatian-action').val($(this).attr('data-action')).submit();
    });


    $(".pagination").on("click", "a", function(e) {

        var error = requiredCheck();

        if ( error ) {
            return false;
        }

        var formEntry = $('#update-khatian');
        formEntry.attr('action', window.location.href);
        formEntry.attr('target', '_self');
        $('#khatian-action').val('PAGE_SAVE');
        formEntry.submit();
    });



    $(".switch-template").on("click", "li", function() {

        var error = false;

        if (!$('#non-deliverable').is(':checked')) {
            error = requiredCheck();
        }

        if ( error ) {
            return false;
        }

        $('#page-template').val($(this).find('a').attr('rel'));
        var formEntry = $('#update-khatian');
        var url = $('#entry_action_url').val();
        formEntry.attr('action', url);
        formEntry.attr('target', '_self');
        $('#khatian-action').val($(this).parent().attr('data-action'));
        $('#update-khatian').submit();
    });

    $('#non-deliverable').click(function() {
        if ($(this).is(':checked')) {
            $('#non-deliverable-reason').show();
            return;
        }
        $('#non-deliverable-reason').hide();
    });

    $('#preview-khatian-page').click(function() {

        var error = requiredCheck();

        if ( error ) {
            return false;
        }
        
        var form = $('#update-khatian');
        var url = $(this).attr('data-url');
        form.attr('action', url);
        form.attr('target', '_blank');
        form.submit();
    });

    $('#khatian_page_khatian_no').change(function(){

        var  khatianLogId = $('#khatianLogId').val();
        var  khatianNo = $('#khatian_page_khatian_no');
        var  except = $('#primary_khatian_no').val();
        khatianNo.css('border', '');

        if (khatianNo.val() == '') {
            return false;
        }

        $.ajax({
            url: Routing.generate("khatian_no_check"),
            data: 'khatianLogId='+khatianLogId+'&khatianNo='+AppUtil.englishToBanglaDigit(khatianNo.val())+'&except='+except,
            type: "POST",
            dataType : "json",
            success: function( response ) {
                if (!response.status) {
                    khatianNo.css('border', '1px solid red');
                    alert(response.message);
                }
            }
        });
    });

   /* $("#template-update textarea").keydown(function(e) {

        if (e.keyCode == 13) {
            newLines = $(this).val().split("\n").length;
            if (newLines * 25 > $(this).parent().height()) {
                return false;
            }
            console.log(newLines * 25);
            console.log($(this).parent().height());
        }
    });*/

    function requiredCheck() {

        var error = false;
        var elements = [
            $( "#khatian_page_district" ),
            $( "#khatian_page_khatian_no" ),
            $( "#khatian_page_mouza" ),
            $( "#khatian_page_jl_no" )
        ];

        $.each( elements, function(i, item) {

            if (elements[i].length) {
                if ( elements[i].val() == '' ) {
                    $( this ).css('border', '1px solid red');
                    error = true;
                } else {
                    $( this ).css('border', '');
                }
            }
        });
        return error;
    }
});