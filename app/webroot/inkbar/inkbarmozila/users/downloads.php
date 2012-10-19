<?php
        
	include('../config/config.inc.php');
        include('../config/db_connect.php');
	include('NewHeader.php');
?>
<table>
    <td width="10%"></td>
    <td width="80%">
<div id="main">
	<h2>Downloads</h2>
	<h4>Please note that JavaScript must be turned ON for your web browser otherwise the toolbar will not work.</h4>

        
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


?>

	<table class="table_style">
            <tr>
                <td>Mozilla/Safari(5.1 or higher version):</td>
                <td>
                    <a onmouseover="status='Download this'; return true;" href="javascript:(function(){
                       var aa=document.createElement('script');aa.innerHTML=%22var baseURL = 'http://www.drsmarts.com/drsmartstoolbar' %22;document.body.appendChild(aa);
                       var w=document.createElement('script');w.innerHTML = %22var u = <?php echo $ui; ?>%22;document.body.appendChild(w);
                       var loaderSpan=document.createElement('script');loaderSpan.src='http://www.drsmarts.com/drsmartstoolbar/drsmarts/Loader.js';loaderSpan.setAttribute('id', 'loaderSpan');document.body.appendChild(loaderSpan);
                       void 0;})()">Smartsed Ink Bar
                    </a> <---- Please bookmark this.
                </td>
            </tr>

            <tr>
                <td>IE: </td>
                <td><a onmouseover="status='Download this'; return true;" href="javascript:(function(){
                       var%20w=document.createElement('script');w.type='text/javascript';w.text=%22var%20u%20%3D<?php echo $ui; ?>%22;document.body.appendChild(w);
                       var%20loaderSpan=document.createElement('script');loaderSpan.type='text/javascript';loaderSpan.src='http://www.drsmarts.com/toolbarIE/drsmarts/Loader.js';loaderSpan.setAttribute('id', 'loaderSpan');document.body.appendChild(loaderSpan);
                       void 0;})()">Smartsed Ink Bar
                    </a> <---- Please bookmark this.
                </td>
            </tr>

            <tr>
              <td>Opera:</td>
                <td>
                    <a onmouseover="status='Download this'; return true;" href="javascript:(function(){
                       var aa=document.createElement('script');aa.innerHTML=%22var baseURL = 'http://www.drsmarts.com/toolbaropera' %22;document.body.appendChild(aa);
                       var w=document.createElement('script');w.innerHTML = %22var u = <?php echo $ui; ?>%22;document.body.appendChild(w);
                       var loaderSpan=document.createElement('script');loaderSpan.src='http://184.168.20.200/toolbaropera/drsmartsOpera/LoderOpera.js';loaderSpan.setAttribute('id', 'loaderSpan');document.body.appendChild(loaderSpan);
                       void 0;})()">Smartsed Ink Bar
                    </a> <---- Please bookmark this.
                </td>
            </tr>
                       
	</table>
	<h3>User instructions</h3>
	<h4>Downloading and installing</h4>
	<ul>
		<li><p>Mozilla: Please drag and drop the above link on to the browser bookmarks toolbar.</p></li>
<!--		<li><p>IE: Please right-click on the above link and click 'Add to favourite'</p></li>
		<li><p>Opera: Please right-click on the above link and click 'Bookmark link'</p></li>-->
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
<?php include('NewFooter.php'); ?>
