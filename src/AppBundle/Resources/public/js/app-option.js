var AppOption = function (){

    var multipleMouza = true;

    function init(param)
    {
        if (param !== undefined) {
            if ( param.hasOwnProperty('multipleMouza') ) {
                multipleMouza = param.multipleMouza;
            }
        }

        var select2UpozilaData = [];
        var select2Upozila = $('.mo-upozila').select2(
            {
                id: function(e) {return e.id; },
                data:{results: select2UpozilaData, text:'text'},
                placeholder: 'নির্বাচন করুণ',
                formatSelection: formatResult,
                formatResult: formatResult,
                allowClear: true
            });


        $(".mo-district").change(function() {

            var districtId = $(this).val();
            clearSelect2Fields([select2UpozilaData]);

            if(districtId != "") {
                $.get(Routing.generate('settings_combo_upozila', {'districtId': districtId}), function (response) {
                    for (var k in response) {
                        console.log(k, response[k]);
                        select2UpozilaData.push(response[k]);
                    }
                });
            }

        });

        $(".mo-district").change(function() {

            var districtId = $(this).val();
            clearSelect2Fields([select2UpozilaData]);

            if(districtId != "") {
                $.get(Routing.generate('settings_combo_upozila', {'districtId': districtId}), function (response) {
                    for (var k in response) {
                        console.log(k, response[k]);
                        select2UpozilaData.push(response[k]);
                    }
                });
            }

        });

        var select2UnionData = [];
        var select2Union = $('.mo-union').select2(
            {
                id: function(e) {return e.id; },
                data:{results: select2UnionData, text:'text'},
                placeholder: 'নির্বাচন করুণ',
                formatSelection: formatResult,
                formatResult: formatResult,
                //multiple: multipleMouza,
                allowClear: true
            });

        $(".mo-upozila").change(function(){

            var upozilaId = $(this).val();

            clearSelect2Fields([select2UnionData]);
            if(upozilaId != "") {
                $.get(Routing.generate('settings_combo_union', {'type': 'upozila', 'upozilaId': upozilaId}), function (response) {
                    for (var k in response) {
                        console.log(k, response[k]);
                        select2UnionData.push(response[k]);
                    }
                });
            }
            $(".mo-union").change();
        });

        function clearSelect2Fields(fieldData) {
            for(var k in fieldData) {
                var last = fieldData[k].length;
                fieldData[k].splice(0, last);
            }
        }

        function formatResult(state) {
            if (!state.id) return state.text;
            return state.text;
        }
    }

    return {
        'init': init
    };
}();