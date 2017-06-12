HTMLTextAreaElement.prototype.insertAtCaret = function (text) {
    text = text || '';
    if (document.selection) {
        // IE
        this.focus();
        var sel = document.selection.createRange();
        sel.text = text;
    } else if (this.selectionStart || this.selectionStart === 0) {
        // Others
        var startPos = this.selectionStart;
        var endPos = this.selectionEnd;
        this.value = this.value.substring(0, startPos) +
            text +
            this.value.substring(endPos, this.value.length);
        this.selectionStart = startPos + text.length;
        this.selectionEnd = startPos + text.length;
    } else {
        this.value += text;
    }
};

$(function() {

    /*var keyMap = {
        'shift+1': String.fromCharCode(2548),//"৴",
        'shift+2': String.fromCharCode(2549),//"৵",
        'shift+3': String.fromCharCode(2550),//"৶",
        'shift+4': String.fromCharCode(2551),//"৷",
        'shift+5': String.fromCharCode(2552),//"৸"
        'shift+6': String.fromCharCode(2553),//"৹",
        'shift+7': String.fromCharCode(2546),//"৲",
        'shift+8': String.fromCharCode(2414),//"",
        'shift+À': String.fromCharCode(95)//"_"
    };

    $.each(keyMap, function(shortCut, char){
        /!* Added Shortcut *!/
        shortcut.add(shortCut,function() {
            var el = $('textarea:focus');
            if (!el.length) {
                return false;
            }

            el[0].insertAtCaret(char);
        });
    });*/

});