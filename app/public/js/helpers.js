CKEDITOR.on('dialogDefinition', function (ev) {
    var dialog = ev.data;

    if (dialog.name === 'link') {
        var infoTab = dialog.definition.getContents('info');
        infoTab.remove('linkType');
        infoTab.remove('protocol');
        infoTab.remove('browse');
    }
});


function show_reloging_info() {
    BootstrapDialog.show({
        message: 'Your session has expired please log again',
        buttons: [{
            label: 'Ok',
            action: function (dialogItself) {
                dialogItself.close();
                window.location.reload();
            }
        }]
    });
}


function save_template_error() {

    BootstrapDialog.show({
        message: 'Template not saved , please try again',
        buttons: [{
            label: 'Ok',
            action: function (dialogItself) {
                dialogItself.close();
                $('#editing_template').show();
                $('#saving_template').hide();
            }
        }]
    });
}

function convertToSlug(Text) {
    return Text.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
}
function isUrlValid(s) {
    if (/^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i.test(s)) {
        if (s.indexOf("http") > -1 || s.indexOf("https") > -1 || s.indexOf("ftp") > -1) {
            return true;
        } else {
            return false;
        }

    } else {

        if (/^(mailto):([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i.test(s)) {
            return true;
        } else {
            return false;
        }
    }


}
