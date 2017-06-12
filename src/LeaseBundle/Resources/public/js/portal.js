var nid = new SFileInput('nid',{
    button:'nid',
    allowedType:'document',
    multipleFile:false,
    selectedFileLabel:'selected_nid_file'
});
var photo = new SFileInput('photo',{
    button:'photo',
    allowedType:'image',
    multipleFile:false,
    selectedFileLabel:'selected_photo_file'
});

var path = window.location.pathname;
if(path.indexOf('water') != -1) {
    var sainput = new SFileInput('resulation',{
        button:'resulation',
        allowedType:'document|image',
        multipleFile:false,
        selectedFileLabel:'selected_resulation_file'
    });
    var sainput = new SFileInput('audit',{
        button:'audit',
        allowedType:'document|image',
        multipleFile:false,
        selectedFileLabel:'selected_audit_file'
    });
    var sainput = new SFileInput('nibondhon',{
        button:'nibondhon',
        allowedType:'document|image',
        multipleFile:false,
        selectedFileLabel:'selected_nibondhon_file'
    });
}
if(path.indexOf('market') != -1) {
    var sainput = new SFileInput('trade',{
        button:'trade',
        allowedType:'document|image',
        multipleFile:false,
        selectedFileLabel:'selected_trade_file'
    });

}