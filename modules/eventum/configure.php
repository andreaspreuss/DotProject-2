<?php
	// Configure Eventum Integration Support

	GLOBAL $AppUI;

	$perms =& $AppUI->acl();
	if (! $perms->checkModule('eventum', 'view', $AppUI->user_id))
	  $AppUI->redirect('m=public&a=access_denied');

	$titleblock = New CTitleBlock('Eventum Integration', 'ticketsmith.gif', $m, "$m.$a" );
	if (isset($GLOBALS['evDirChanged']))
		$titleblock->addCrumb('?m=eventum&a=configure', 'configure eventum');
	$titleblock->show();

	// First check to see if we have to display any messages instead of doing the config
	if (isset($GLOBALS['evDirChanged'])) {
		// Build the eventum config and write it out
		$ev_conf = '<?php
// Configuration file required for the dotProject integration.
// This file is automatically generated. Do not adjust by hand.

$dproot = \'' . $baseDir . '\'; /* Full path to dotProject installation */
global $baseDir, $baseUrl, $dPconfig;
$baseDir = $dproot;
$baseUrl = \'' . $baseUrl . '\';
$dPconfig = array();
define(\'DP_BASE_DIR\','.$baseDir.');
require_once $dproot . \'/includes/config.php\';
?>';

		$cfname = $GLOBALS['evDir'] . DIRECTORY_SEPARATOR . 'dp_config.php';
		if (! $f = @fopen($cfname, 'w')) {
		  $cfg_ok = false;
		} else {
		  fwrite($f, $ev_conf);
		  fclose($f);
		  $cfg_ok = true;
		}
?>
<div align="center"><?php echo $AppUI->_('evDirChanged'); ?> </div>
<table width="100%" class="std">
  <tr>
    <td>
      <?php if ($cfg_ok) { echo $AppUI->_('configWriteOK'); }
      else {
	echo $AppUI->_('noConfigWrite') . ': ' . $GLOBALS['evDir'];
	?>
	</td>
	</tr>
	<tr>
	<td>
	<textarea readonly="yes" cols="80" rows="12"><?php echo $ev_conf; } ?></textarea>
    </td>
  </tr>
  <tr>
    <td>
      <?php echo $AppUI->_('configFileInstruction'); ?>
    </td>
  </tr>
  <tr>
    <td>
      <?php echo $AppUI->_('configFiles'); ?> : <a href="modules/eventum/evlink.zip">ZIP</a>&nbsp;&nbsp;&nbsp;<a href="modules/eventum/evlink.tar.gz">Gzip TAR</a>
    </td>
  </tr>
</table>
<?php
	} else {
	$q = new DBQuery;
	$q->addTable('companies_support_levels');
	$q->exec();

	$evconfig = New CEventumConfig();
	$support_enabled = $evconfig->getValue("eventum_supplvl_enabled") == 1 ? " checked" : ""; 
	$grace_period = $evconfig->getValue("eventum_contract_grace");
	$eventum_directory = $evconfig->getValue("eventum_directory");
		
?>
	<script>

		function delSupport(supp_id)
		{
			var delid = document.getElementById('delete_level_id');
			delid.value = supp_id;
			var frm = document.getElementById('eventum_configure');
			frm.submit();
		}

		function applyConfig()
		{
			var changecfg = document.getElementById('apply_config_changes');
			changecfg.value = 1;
			var frm = document.getElementById('eventum_configure');
			frm.submit();
		}

		function checkConfigEnabled()
		{
			var optiondiv = document.getElementById('eventum_levels');
			var frm = document.getElementById('eventum_configure');
			if (frm.eventum_supplvl_enabled.checked) {
			  optiondiv.style.visibility = "visible";
			} else {
			  optiondiv.style.visibility = "hidden";
			}
		}

	</script>
	<form method="POST" id="eventum_configure">
	<br />
		<b><?php echo $AppUI->_('Eventum Installation Directory');?>:</b>
		<input type="text" class="text" name="eventum_directory" size="50" value="<?php echo $eventum_directory; ?>"
		onchange="applyConfig();" />
	<br />
		<b><?php echo $AppUI->_('Enable Customer Support Levels'); ?>:</b>
		<input type="checkbox" name="eventum_supplvl_enabled" value="1" <?php echo $support_enabled; ?> 
		  onchange="checkConfigEnabled();"/>
		<p><?php echo $AppUI->_('eventumEnableMsg'); ?></p>
	<hr />
	<input type="hidden" name="dosql" value="do_eventum_supplvl_add" />
	<input type="hidden" name="delete_level_id" id="delete_level_id" value="0" />
	<input type="hidden" name="apply_config_changes" id="apply_config_changes" value="0" />
	<div id="eventum_levels" style="visibility: <?php echo ($support_enabled != "") ? 'visible' : 'hidden'; ?>;" >
		<b><?php echo $AppUI->_('Customer Support Grace Period'); ?></b>
		<input type="text" name="eventum_contract_grace" size="5" value="<?php echo $grace_period; ?>" /> <?php echo $AppUI->_('day(s)'); ?>
		<p><?php echo $AppUI->_('eventumGraceMsg'); ?></p> 
		<b><?php echo $AppUI->_('Defined Support Levels'); ?></b>
		<p><?php echo $AppUI->_('eventumLevelMsg'); ?></p>
	<table>
	<th><?php echo $AppUI->_('Support Level'); ?></th>
	<th><?php echo $AppUI->_('Min Response'); ?></th>
	<th><?php echo $AppUI->_('Max Response'); ?></th><th><?php echo $AppUI->_('Change'); ?></th>
<?php
	while ($row = $q->FetchRow())
	{
		echo '<tr><td><li>'.$row['support_level_desc'].'</li></td>
			<td>'.$row['support_minresponse_hrs'].' hr(s)</td>
			<td>'.$row['support_maxresponse_hrs'].' hr(s)</td>
			<td><a href="javascript:delSupport('.$row['support_level_id'].')">[' . $AppUI->_('delete', UI_OUTPUT_JS) . ']</a></td></tr>' . "\n";
	}

	echo '<tr><td><li><input type="text" name="support_level_desc" /></td>
			<td><input type="text" name="support_minresponse_hrs" size="5" /> '.$AppUI->_('hr(s)').'</td>
			<td><input type="text" name="support_maxresponse_hrs" size="5" /> '.$AppUI->_('hr(s)').'</td>
			<td><input class="button" type="submit" value="'.$AppUI->_('add').'" /></li></td>
		</tr>' . "\n";


?>
	</table>
	</div>
	<br />
		<input class="button" type="button" value="<?php echo $AppUI->_('apply changes', UI_OUTPUT_JS); ?>" onClick="applyConfig()" />
	</form>
<?php
	} // End of if evDirChanged
?>
