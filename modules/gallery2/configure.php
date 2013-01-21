<?php 
##
## Configure gallery2 integration 
##
## Sensorlink AS (c) 2006
## Vegard Fiksdal (fiksdal@sensorlink.no)
##

// Update database if we are saving
$action = @$_REQUEST["action"];
if($action) {
        $gallery_uri= $_POST["gallery_uri"];
        $gallery_folder = $_POST["gallery_folder"];
        $gallery_user = $_POST["gallery_user"];

        if( $action == "add" ) {
		db_exec("UPDATE gallery2 SET gallery_uri='".$gallery_uri."'");
		db_exec("UPDATE gallery2 SET gallery_folder='".$gallery_folder."'");
		db_exec("UPDATE gallery2 SET gallery_user='".$gallery_user."'");

		$AppUI->setMsg( db_error() );
		$AppUI->redirect();
	}
}

// Load database settings
$gallery_uri= db_loadResult( 'SELECT gallery_uri FROM gallery2' );
$gallery_folder= db_loadResult( 'SELECT gallery_folder FROM gallery2' );
$gallery_user= db_loadResult( 'SELECT gallery_user FROM gallery2' );

// Override if database failed
if(!$gallery_folder){
	$gallery_folder='/var/www/gallery2/';
}
if(!$gallery_uri){
	$gallery_uri='http://gallery.example.com/';
}

?>

<form name="ConfigureGallery" method="post">				
<table width="100%" border="0" cellpadding="0" cellspacing="1">
<input name="action" type="hidden" value="add"">
<tr>
	<td><img src="./images/icons/tasks.gif" alt="" border="0"></td>
	<td align='left' nowrap='nowrap' width='100%'><h1>Configure Gallery2 integration</h1></td>
</tr>
</table>

<table width='100%' border='0' cellpadding='1' cellspacing='1' class='std'>
<tr>
	<td nowrap="nowrap" align="right"><?php echo $AppUI->_( 'Gallery2 URI:' );?></td>
	<td nowrap="nowrap" align="left"><input type="text" class="text" size="100%" name="gallery_uri" value=<?php echo $gallery_uri;?>></td>
</tr>
<tr>
	<td nowrap="nowrap" align="right"><?php echo $AppUI->_( 'Gallery2 Local Folder:' );?></td>
	<td nowrap="nowrap" align="left"><input type="text" class="text" size="100%" name="gallery_folder" value=<?php echo $gallery_folder;?>></td>
</tr>
<tr>
	<td nowrap="nowrap" align="right"><?php echo $AppUI->_( 'Gallery2 Username:' );?></td>
	<td nowrap="nowrap" align="left"><input type="text" class="text" size="100%" name="gallery_user" value=<?php echo $gallery_user;?>></td>
</tr>
</table>	

<table border="0" cellspacing="0" cellpadding="3" width="100%">
<tr>
	<td height="40" width="30%">&nbsp;</td>
	<td  height="40" width="35%" align="right">
		<table>
		<tr>
			<td>
				<input class="button" type="button" name="save" value="<?php echo $AppUI->_('save'); ?>" onClick="submit()">
			</td>
		</tr>
		</table>
	</td>
</tr>
	
</table>
</form>		
</body>
</html>
