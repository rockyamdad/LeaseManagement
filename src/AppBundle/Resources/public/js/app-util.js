var AppUtil = function (){

    var formEntryElm = "#form-entry";
    var formSearchElm = "#form-search";
    var tableDataElm = ".table-data";

    function blockTableContent()
    {
        Metronic.blockUI({
            target: $(tableDataElm),
            animate: true,
            overlayColor: 'black'
        });
    }

    function unBlockTableContent()
    {
        Metronic.unblockUI($(tableDataElm));
    }

    function fetchData(url)
    {
        var dataPost = [];
        if (formSearchElm != 'none') {
            dataPost = serializeSearchParam(formSearchElm);
        }

        $('#btn-print').hide();
        blockTableContent();

        $.ajax({
            url: url,
            data: dataPost,
            type: "GET",
            dataType : "html",
            success: function( response ) {
                $( tableDataElm ).html( response );
                if ($('#btn-print').length && $(tableDataElm).find('tbody tr').length > 1) {
                    $('#btn-print').show();
                }
                unBlockTableContent();
            },
            error: function(){
                unBlockTableContent();
            }
        });
    }

    function serializeSearchParam(elm) {
        var o = {};
        var a = $(elm).serializeArray();
        $.each(a, function() {
            if (o[this.name]) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    }

    function init(param)
    {
        if (param !== undefined) {
            if ( param.hasOwnProperty('formEntryElm') ) {
                formEntryElm = param.formEntryElm;
            }
            if ( param.hasOwnProperty('formSearchElm') ) {
                formSearchElm = param.formSearchElm;
            }
            if ( param.hasOwnProperty('tableDataElm') ) {
                tableDataElm = param.tableDataElm;
            }
        }

        $( tableDataElm ).on( "click", ".table-list th a", function() {
            blockTableContent();
            var _href = $(this).attr('href');
            $.post(_href, '', function (response) {
                $( tableDataElm ).html(response);
                unBlockTableContent();
                //$('.sticky-header').floatThead();
            });
            return false;
        });

        $( tableDataElm ).on( "click", ".pagination a", function() {
            blockTableContent();
            var _href = $(this).attr('href');
            $.post(_href, '', function (response) {
                $( tableDataElm ).html(response);
                unBlockTableContent();
                //$('.sticky-header').floatThead();
            });
            return false;
        });

        $( tableDataElm ).on( "change", ".pagination-panel .per-page", function() {
            blockTableContent();
            var perPage = ($(this).val()) ? $(this).val() : 25 ;
            var panel = $(".pagination-panel");
            var currentUrl = (panel.find('li.active').length) ? panel.find('li.active').attr('data-current-url') : $('#current-page-url').val();
            var _href = currentUrl + '&page=1&per-page=' + perPage;
            $.post(_href, '', function (response) {
                $( tableDataElm ).html(response);
                unBlockTableContent();
                //$('.sticky-header').floatThead();
            });
            return false;
        });

        $( tableDataElm ).on( "change", ".pagination-panel .move-to-page", function() {
            blockTableContent();
            var _href = $(this).val();
            $.post(_href, '', function (response) {
                $( tableDataElm ).html(response);
                unBlockTableContent();
                //$('.sticky-header').floatThead();
            });
            return false;
        });

        $('.table-data').on('click', 'a.confirm', function(){
            var url = $(this).attr('href');
            bootbox.confirm('আপনি কি নিশ্চিত ?', function(response){
                if (response) {
                    document.location.href = url;
                }
            });
            return false;
        });

        $("#btn-search").click(function () {
            AppUtil.fetchData($(this).attr('data-url'));
            return false;
        });

        $(".bn-digit").change(function() {
            $(this).val(englishToBanglaDigit($(this).val()));
        });

        $(".en2bn").change(function() {
            $(this).val(englishToBanglaDigit($(this).val()));
        });

        $(".bn2en").change(function() {
            $(this).val(banglaToEnglish($(this).val()));
        });

        //$('.sticky-header').floatThead();
    }

    var en2bnMap = {'1':'১', '2':'২', '3':'৩', '4':'৪', '5':'৫', '6':'৬', '7':'৭', '8':'৮', '9':'৯', '0': '০' };
    var bn2enMap = {'১':'1', '২':'2', '৩':'3', '৪':'4', '৫': '5', '৬':'6' , '৭':'7', '৮':'8', '৯':'9', '০':'0' };

    function replacemap(input, digitMap) {

        var replacechars = function(c){
            return digitMap[c] || c;
        };

        return input.split('').map(replacechars).join('');
    }

    function banglaToEnglish(input)
    {
        return replacemap(input, bn2enMap);
    }

    function englishToBanglaDigit(input)
    {
        return replacemap(input, en2bnMap);
    }

    return {
        'init': init,
        'fetchData': fetchData,
        'serializeSearchParam': serializeSearchParam,
        'banglaToEnglish': banglaToEnglish,
        'englishToBanglaDigit': englishToBanglaDigit,
        'blockTableContent': blockTableContent,
        'unBlockTableContent': unBlockTableContent
    };
}();