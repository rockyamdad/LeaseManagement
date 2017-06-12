var Placeholder = function () {

    function init(placeHolders) {
        jQuery("body").on('CKE.placeholder.onload', function(e, el){
            console.log(placeHolders);
            for(var i in placeHolders) {
                el.add(placeHolders[i], i);
            }
        });
    }

    return {
        init: init
    };

}();