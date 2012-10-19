<?php
        
	include('../config/config.inc.php');
        include('../config/db_connect.php');
	include('../includes/NewHeader.php');
?>
<table>
    <td width="10%"></td>
    <td width="80%">
<div id="main">
	<h2>Downloads</h2>
	<h4>Please note that JavaScript must be turned ON for your web browser or the bookmarklet will not work.</h4>

        
<!--//	if(isset($_SESSION['drsmarts_userid'])){
//
//		$ui = $_SESSION['drsmarts_userid'];
//		$un = "'".$_SESSION['drsmarts_username']."'";
//	}else{
//		echo"You must log in before you can download.";
//	}-->

<?php

      $sql= "select users.id, users.email from users where md5(users.id) = '".$_REQUEST['uid']."' ";

      $uii =  mysql_fetch_array(mysql_query($sql));
      $ui=$uii['id'];
      //$un;// = $uii['email'];
	
	$url_stickyNoteJs = $url.'/drsmarts/class_StickyNote.js'; 
	$url_eStickyNoteJs = $url.'/drsmarts/class_EditableSticky.js'; 
	$url_peStickyNoteJs = $url.'/drsmarts/class_PartialEditableSticky.js'; 
	$url_neStickyNoteJs = $url.'/drsmarts/class_NonEditableSticky.js'; 
	$url_dbConnectJs = $url.'/drsmarts/class_DBConnect.js';
	$url_vMozillaJs = $url.'/drsmarts/class_Drsmarts_Mozilla.js'; 
	$url_vIEJs = $url.'/drsmarts/class_Drsmarts_IE.js';
        $url_rangycore = $url.'/drsmarts/rangy/rangy-core.js';
        $url_rangycssclassapplier = $url.'/drsmarts/rangy/rangy-cssclassapplier.js';
        $url_rangyselectionsaverestore = $url.'/drsmarts/rangy/rangy-selectionsaverestore.js';
        $url_rangyserializer = $url.'/drsmarts/rangy/rangy-serializer.js';
        $url_drag = $url.'/drsmarts/Drag.js';
        $url_hcss = $url.'/drsmarts/css/opera.css';
        $url_picker = $url.'/drsmarts/picker.js';
        $url_Annotation = $url.'/drsmarts/AnnotationClass.js';
        $url_jquery = $url.'/drsmarts/JQuery.js';
        $url_accessSpecifire = $url.'/drsmarts/AccessSpecifire.js';
        $url_stickyNotes = $url.'/drsmarts/StickyNotes.js';
        $url_colorPickerStickyNotes = $url.'/drsmarts/StickyColorPicker.js';
        $url_dragStickyNotes = $url.'/drsmarts/DragStickyNotes.js';
        $url_drsmartsAjax = $url.'/drsmarts/DrsmartsAjax.js';
        $url_loaderSpan = $url.'/drsmarts/Loader.js';
        //$url_loaderSpanOpera = $url.'/drsmarts/LoderOpera.js';
        
        // for Opera
        $url_stickyNoteJsOpera = $url.'/drsmartsOpera/class_StickyNote.js'; 
	$url_eStickyNoteJsOpera = $url.'/drsmartsOpera/class_EditableSticky.js'; 
	$url_peStickyNoteJsOpera = $url.'/drsmartsOpera/class_PartialEditableSticky.js'; 
	$url_neStickyNoteJsOpera = $url.'/drsmartsOpera/class_NonEditableSticky.js'; 
	$url_dbConnectJsOpera = $url.'/drsmartsOpera/class_DBConnect.js';
	$url_vMozillaJsOpera = $url.'/drsmartsOpera/class_Drsmarts_Mozilla.js'; 
	$url_vIEJsOpera = $url.'/drsmartsOpera/class_Drsmarts_IE.js';
        $url_rangycoreOpera = $url.'/drsmartsOpera/rangy/rangy-core.js';
        $url_rangycssclassapplierOpera = $url.'/drsmartsOpera/rangy/rangy-cssclassapplier.js';
        $url_rangyselectionsaverestoreOpera = $url.'/drsmartsOpera/rangy/rangy-selectionsaverestore.js';
        $url_rangyserializerOpera = $url.'/drsmartsOpera/rangy/rangy-serializer.js';
        $url_dragOpera = $url.'/drsmartsOpera/Drag.js';
        $url_hcssOpera = $url.'/drsmartsOpera/css/opera.css';
        $url_pickerOpera = $url.'/drsmartsOpera/picker.js';
        $url_AnnotationOpera = $url.'/drsmartsOpera/AnnotationClass.js';
        $url_jqueryOpera = $url.'/drsmartsOpera/JQuery.js';
        $url_accessSpecifireOpera = $url.'/drsmartsOpera/AccessSpecifire.js';
        $url_stickyNotesOpera = $url.'/drsmartsOpera/StickyNotes.js';
        $url_colorPickerStickyNotesOpera = $url.'/drsmartsOpera/StickyColorPicker.js';
        $url_dragStickyNotesOpera = $url.'/drsmartsOpera/DragStickyNotes.js';
        $url_drsmartsAjaxOpera = $url.'/drsmartsOpera/DrsmartsAjax.js';
        $url_loaderSpanOpera = $url.'/drsmartsOpera/Loader.js';
        $url_loaderSpanOpera = $url.'/drsmartsOpera/LoderOpera.js';

?>

	<table class="table_style">
            <tr>
                <td>Mozilla:</td>
                <td>
                    <a onmouseover="status='Download this'; return true;" href="javascript:(function(){
                       var aa=document.createElement('script');aa.innerHTML=%22var baseURL = 'http://184.168.20.200' %22;document.body.appendChild(aa);
                       var w=document.createElement('script');w.innerHTML = %22var u = <?php echo $ui; ?>%22;document.body.appendChild(w);
                       var loaderSpan=document.createElement('script');loaderSpan.src='<?php echo "$url_loaderSpan"; ?>';loaderSpan.setAttribute('id', 'loaderSpan');document.body.appendChild(loaderSpan);
                       void 0;})()">Drsmarts Toolbar
                    </a> <---- Please bookmark this.
                </td>
            </tr>
<!--var y=document.createElement('script');y.innerHTML = %22generateToolbar(u, un)%22;document.body.appendChild(y);-->
<!--var x=document.createElement('script');x.innerHTML = %22var un = <?php echo $un; ?>%22;document.body.appendChild(x);	-->
		<tr><td>IE:</td><td><a onmouseover="status='Download this'; return true;" href="javascript:(function(){
                       var aa=document.createElement('script');aa.innerHTML=%22var baseURL = 'http://184.168.20.200' %22;document.body.appendChild(aa);
                       var w=document.createElement('script');w.innerHTML = %22var u = <?php echo $ui; ?>%22;document.body.appendChild(w);
                       var loaderSpan=document.createElement('script');loaderSpan.src='<?php echo "$url_loaderSpan"; ?>';loaderSpan.setAttribute('id', 'loaderSpan');document.body.appendChild(loaderSpan);
                       void 0;})()">Drsmarts Toolbar
                    </a> <---- Please bookmark this.</td></tr>

		<tr><td>Opera:</td><td> <a onmouseover="status='Download this'; return true;" href="javascript:(function(){
                       var aa=document.createElement('script');aa.innerHTML=%22var baseURL = 'http://184.168.20.200' %22;document.body.appendChild(aa);
                       var w=document.createElement('script');w.innerHTML = %22var u = <?php echo $ui; ?>%22;document.body.appendChild(w);
                       var loaderSpanOpera=document.createElement('script');loaderSpanOpera.src='<?php echo "$url_loaderSpanOpera"; ?>';loaderSpanOpera.setAttribute('id', 'loaderSpanOpera');document.body.appendChild(loaderSpanOpera);
                       void 0;})()">Drsmarts Toolbar
                    </a> <---- Please bookmark this.
	</table>
	<h3>User instructions</h3>
	<h4>Downloading and installing</h4>
	<ul>
		<li><p>Mozilla: Please drag and drop the above link on to the browser toolbar.</p></li>
		<li><p>IE: Please right-click on the above link and click 'Add to favourite'</p></li>
		<li><p>Opera: Please right-click on the above link and click 'Bookmark link'</p></li>
	</ul>
	<h4>Creating a sticky note</h4>
	<ol>
		<li><p>Make a Selection on Page and then click on "Add Sticky". Selection color and Sticky Note color will be same for future references.</p></li>
		<li><p>Default Color of Sticky Note is Yellow. You can change the Color of Sticky by Clicking on Color button of "Add Sticky".</p></li>
                <li><p>Enter title and some notes in the Sticky Notes.</p></li>
		<li><p>Any Number of Sticky Notes can be created.</p></li>
                <li><p>Sticky notes has to icon on the right side namely Minimize and Cross.</p></li>
		<li><p>Clicking on minimize icon notes will be minimize at the end of selection.</p></li>
                <li><p>Clicking on close icon notes and selection color will be removed.</p></li>
        </ol>
	<h4>Making a Selection Highlighted</h4>
	<ol>
		<li><p>Make a Selection and click on Highlight icon, default color will be yellow.</p></li>
		<li><p>Highlight Color can be change using color button right near to it.</p></li>
		<li><p>Cross button will be appear at the end of highlighted section, by clicking on cross button highlight will be removed.</p></li>
	</ol>
        <h4>Saving Annotation</h4>
	<ol>
		<li><p>Click on save icon of Drsmarts Toolbar.</p></li>
		<li><p>A Popup will be open, enter tag, choose category and if you wants to make it private then make private checked (left side).</p></li>
		<li><p>Click on save button to publish.</p></li>
	</ol>
        <h4>Show all annotation</h4>
	<ol>
		<li><p>Clicking on "Show all annotation" will take to your library of all saved annotated pages.</p></li>
	</ol>
<!--	<h3>Known bugs</h3>
	<ul>
            <li>IE seem to experience some sort of lag when executing the application. If the application doesn't react, please refresh page and call again. Please keep trying because it does work!</li>
	    <li><p>Highlighting across two paragraphs or two list items cause problems.</p></li>
	    <li><p>Bookmarklet does not perform reliably on web pages with frames.</p></li>
	    <li><p>Sticky notes added to 'li' tags renders out of place in IE.</p></li>
	</ul>-->
</div>
</td>
<td width="10%"></td>
</table>
<?php include('../includes/NewFooter.php'); ?>
