// Define changes to default configuration here.
// For complete reference see:
// http://docs.ckeditor.com/#!/api/CKEDITOR.config
var roxyFileman = '/fileman/index.html'; 
// The toolbar groups arrangement, optimized for two toolbar rows.
CKEDITOR.editorConfig = function (config) {
    config.toolbarGroups = [
		//{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
		//{ name: 'editing',     groups: [ 'find', 'selection' ] },
		{ name: 'insert' },
		//{ name: 'document',	   groups: [ 'mode', 'document' ] },
		{ name: 'basicstyles', groups: [ 'basicstyles' ] }

    ];

// Remove some buttons provided by the standard plugins, which are
// not needed in the Standard(s) toolbar.
	// Remove some buttons provided by the standard plugins, which are
	// not needed in the Standard(s) toolbar.
	config.removeButtons = 'Underline,Subscript,Superscript,Image';

	// Set the most common block elements.
	config.format_tags = 'p;h1;h2;h3;pre';

	// Simplify the dialog windows.

    config.allowedContent = true;
   // config.removePlugins = 'link';
  //  config.linkShowTargetTab = false;
    config.resize_enabled = false;
    config.height = '100%';
    
  //  config.extraPlugins = 'imgbrowse';
   // config.extraPlugins = 'uploadimage';
  //  config.filebrowserImageBrowseUrl= "/ckeditor/plugins/imgbrowse/imgbrowse.html";
    config.filebrowserBrowseUrl =roxyFileman;
    config.filebrowserImageBrowseUrl=roxyFileman+'?type=image';
    config.removeDialogTabs ='link:upload;image:upload;image:advanced;link:advanced;image:Link;link:target;';
    
};