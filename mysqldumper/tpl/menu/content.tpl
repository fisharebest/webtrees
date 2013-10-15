<div id="menu">
<ul>
	<li id="m1" class="active"><a href="main.php" target="MySQL_Dumper_content" onclick="setMenuActive('m1')">{L_HOME}</a></li>
	<li id="m2" class=""><a href="config_overview.php" target="MySQL_Dumper_content" onclick="setMenuActive('m2')">{L_CONFIG}</a></li>
	
	<!-- BEGIN MAINTENANCE -->
	<li id="m3" class=""><a href="filemanagement.php?action=dump"
		target="MySQL_Dumper_content" onclick="setMenuActive('m3')">{L_DUMP}</a></li>
	<li id="m4" class=""><a href="filemanagement.php?action=restore"
		target="MySQL_Dumper_content" onclick="setMenuActive('m4')">{L_RESTORE}</a></li>
	<li id="m5" class=""><a href="filemanagement.php?action=files"
		target="MySQL_Dumper_content" onclick="setMenuActive('m5')">{L_FILE_MANAGE}</a></li>
	<li id="m6" class=""><a	href="sql.php?db={DB_ACTUAL}&amp;dbid={DB_SELECTED_INDEX}"
		target="MySQL_Dumper_content" onclick="setMenuActive('m6')">{L_SQL_BROWSER}</a></li>
	<li id="m7" class=""><a href="log.php" target="MySQL_Dumper_content"
		onclick="setMenuActive('m7')">{L_LOG}</a></li>
	<!-- END MAINTENANCE -->
	<li id="m8" class=""><a href="help.php" target="MySQL_Dumper_content" onclick="setMenuActive('m8')">{L_CREDITS}</a></li>
</ul>
</div>

<div id="selectConfig">
<form action="menu.php" method="post">
<fieldset id="configSelect"><legend>{L_CONFIG}:</legend>
	<select name="selected_config" style="width: 157px;" onchange="this.form.submit()">{GET_FILELIST}</select></fieldset>
</form>
<form action="menu.php" method="post">
<fieldset id="dbSelect"><legend>{L_CHOOSE_DB}:</legend>
	<!-- BEGIN DB_LIST -->
		<select name="dbindex" style="width:157px;" onchange="this.form.submit()">
		<!-- BEGIN DB_ROW -->
			<option value="{DB_LIST.DB_ROW.ID}"{DB_LIST.DB_ROW.SELECTED}>&nbsp;&nbsp;{DB_LIST.DB_ROW.NAME}</option>
		<!-- END DB_ROW -->
		</select>
	<!-- END DB_LIST -->

	<!-- BEGIN NO_DB_FOUND -->
		{L_NO_DB_FOUND}
	<!-- END NO DB_FOUND --> 
	<p><a href="menu.php?action=dbrefresh">{L_LOAD_DATABASE}</a></p>
</fieldset>
</form>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
<p align="center"><input type="hidden" name="cmd" value="_s-xclick">
	<input type="image" src="./images/paypal-de.gif" name="submit" alt="Support MySQLDumper" title="Support MySQLDumper">
	<script type="text/javascript"	language="JavaScript">
		var s='<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">';
		document.write(s);
	</script></p>
</form>
</div>
						