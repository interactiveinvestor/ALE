/*
  Custom configuration for ckeditor.
 
  Configuration options can be set here. Any settings described here will be
  overridden by settings defined in the $settings variable of the hook. To
  override those settings, do it directly in the hook itself to $settings.
*/
CKEDITOR.editorConfig = function( config )
{
	config.entities = false;
	config.font_names = 'Arial/Arial; Helvetica Neue Condensed/HNC;Helvetica Neue Medium Condensed/HNMC;Helvetica Neue Light Condensed/HNLC; Helvetica Neue Thin Condensed/HNTC; ';
	
	//config.contentsCss='/sites/all/modules/ckeditor_custom/ckeditor.css'
  // config.styleSet is an array of objects that define each style available
  // in the font styles tool in the ckeditor toolbar
  config.stylesSet =
  [
        /* Block Styles */
 		//{ name : 'Heading'   , element : 'h2', attributes : { 'style' : "font-family:'HelveticaNeueW01-67MdCn 692710';font-size:20px" } },
		{ name : 'Secondary Heading'   , element : 'h2', attributes : { 'class' : "secondary-heading" } },
		{ name : 'Tertiary Heading'   , element : 'h3', attributes : { 'class' : "tertiary-heading" } },
		{ name : 'Body copy'   , element : 'p', attributes : { 'class' : "body-copy" } },

  ];
	
	config.format_h1 = { element: 'span', attributes: { 'class': 'my_custom_class' } };

	config.indentOffset = 10;
	
	var fontSizes='';

	for (var i=8; i < 31; i++) fontSizes+=i+'/'+i+'px;';
	
	config.fontSize_sizes = fontSizes;
	config.colorButton_colors = '00aeef,76787b,ffcb07,000000,ffffff';
 
}