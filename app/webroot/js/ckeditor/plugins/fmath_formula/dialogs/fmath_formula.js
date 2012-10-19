//-------------------------------------------------------------
//	Created by: Ionel Alexandru 
//	Mail: ionel.alexandru@gmail.com
//	Site: www.fmath.info
//---------------------------------------------------------------

(function()
{

	CKEDITOR.dialog.add( 'fmath_formula', function( editor )
	{
              return {
                 title : 'Mathml Editor',
                 minWidth : 820,
                 minHeight : 470,
                 //buttons: [],
                 contents :
                       [
                          {
                             id : 'iframe',
                             label : 'Mathml Editor',
                             expand : true,
                             elements :
                                   [
                                      {
				       type : 'html',
				       id : 'pageMathMLEmbed',
				       label : 'Mathml Editor',
				       style : 'width : 100%;',
				       html : '<iframe src="'+ CKEDITOR.plugins.getPath('fmath_formula') +'dialogs/editor.html" frameborder="0" name="iframeMathmlEditor" id="iframeMathmlEditor" allowtransparency="1" style="width:820px;height:470px;margin:0;padding:0;" scrolling="no"></iframe>'
				      }
                                   ]
                          }
                       ],
		onOk : function()
		{
			var frame = document.getElementById('iframeMathmlEditor').contentWindow;
			frame.saveImage(editor);
			return false;
                 },
		onHide : function()
		{
			var frame = document.getElementById('iframeMathmlEditor');
			frame.src = CKEDITOR.plugins.getPath('fmath_formula') +'dialogs/editor.html';
		}
              };
        } );
			
})();



