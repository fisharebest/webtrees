<a name="dbid"></a>

<h5>{L_INFO_DBDETAIL} "{DB_NAME}"</h5>
{MESSAGE}
<!-- BEGIN DISABLED_KEYS_FOUND -->
<div class="error">
     Found MyISAM tables with disabled keys! For better performance you should enable them.<br>
    <br>
</div>
<!-- END DISABLED_KEYS_FOUND -->

<!-- BEGIN NO_TABLE -->
<strong>{L_INFO_DBEMPTY}</strong><br>
<!-- END NO_TABLE -->

<!-- BEGIN 1_TABLE -->
1 {L_TABLE}
<!-- END 1_TABLE -->

<!-- BEGIN MORE_TABLES -->
{TABLE_COUNT} {L_TABLES}
<!-- END MORE_TABLES -->

<br>
<table class="bdr small">
	<tr class="thead"><th>#</th>
	<th>{L_TABLE}</th>
	<th>{L_INFO_RECORDS}</th>
	<th>{L_INFO_SIZE}</th>
	<th>{L_INFO_LASTUPDATE}</th>
	<th>{L_ENGINE}</th>
	<th>{L_INFO_OPTIMIZED}</th>
    <th>{L_STATUS}</th>
    <th>Key {L_STATUS}</th>
</tr>

<!-- BEGIN ROW -->
<tr class="{ROW.ROWCLASS}">
	<td class="right">{ROW.NR}.</td>
	<td><img src="{ICONPATH}search.gif" alt="">
		<a href="sql.php?db={DB_NAME_URLENCODED}&amp;tablename={ROW.TABLE_NAME_URLENCODED}&amp;dbid={DB_ID}">{ROW.TABLE_NAME}</a></td>
	<td class="right">{ROW.RECORDS}</td>
	<td class="right">{ROW.SIZE}</td>
	<td>{ROW.LAST_UPDATE}</td>
	<td>{ROW.ENGINE}</td>
	<td>
		<!-- BEGIN OPTIMIZED -->
			<img src="{ICONPATH}ok.gif" alt="" width="12" height="12" border="0">
		<!-- END OPTIMIZED -->

		<!-- BEGIN NOT_OPTIMIZED -->
			<img src="{ICONPATH}notok.gif" alt="" width="12" height="12" border="0">
		<!-- END NOT_OPTIMIZED -->
	</td>
	
	<td>
		<!-- BEGIN CHECK_TABLE -->
		<a href="main.php?action=db&amp;dbid={DB_ID}&amp;checkit={ROW.TABLE_NAME_URLENCODED}">{L_CHECK}</a>
		<!-- END CHECK_TABLE -->
		
		<!-- BEGIN CHECK_TABLE_OK -->
		<img src="{ICONPATH}ok.gif" alt="" width="12" height="12" border="0">
		<!-- END CHECK_TABLE_OK -->
		
		<!-- BEGIN CHECK_TABLE_NOT_OK -->
		<a href="main.php?action=db&amp;dbid={DB_ID}&amp;checkit={ROW.TABLE_NAME_URLENCODED}&amp;repair=1"><img src="{ICONPATH}notok.gif" alt="" width="12" height="12" border="0">&nbsp;repair&nbsp;<img src="{ICONPATH}notok.gif" alt="" width="12" height="12" border="0"></a>
		<!-- END CHECK_TABLE_NOT_OK -->
	</td>
    <td>
        <!-- BEGIN KEYS_DISABLED -->
            <img src="{ICONPATH}notok.gif" alt="" width="12" height="12" border="0">
            <a href="main.php?action=db&amp;dbid={DB_ID}&amp;enableKeys={ROW.TABLE_NAME_URLENCODED}">&nbsp;Enable keys</a>
            
        <!-- END KEYS_DISABLED -->
        <!-- BEGIN KEYS_ENABLED -->
            <img src="{ICONPATH}ok.gif" alt="" width="12" height="12" border="0">
        <!-- END KEYS_ENABLED -->
    </td>
</tr>
<!-- END ROW -->

<!-- BEGIN SUM -->
<tr class="dbrowsel">
	<td colspan="2">{L_INFO_SUM}</td>
	<td class="right">{SUM.RECORDS}</td>
	<td class="right">{SUM.SIZE}</td>
	<td>{SUM.LAST_UPDATE}</td>
	<td colspan="4">&nbsp;</td>
</tr>
<!-- END SUM -->
</table>

<br>
<!-- BEGIN DISABLED_KEYS_FOUND -->
<div class="error">
     Found MyISAM tables with disabled keys! For better performance you should enable them.<br>
    <br><br>
</div>
<!-- END DISABLED_KEYS_FOUND -->
<form action="main.php?action=db#dbid" method="post">
	<input class="Formbutton" type="submit" name="empty{DB_ID}" value="{L_CLEAR_DATABASE}" onclick="if (!confirm('{L_INFO_EMPTYDB1} \'{DB_NAME}\' {L_INFO_EMPTYDB2}')) return false;">
	<input class="Formbutton" type="submit" name="kill{DB_ID}" value="{L_DELETE_DATABASE}" onclick="if (!confirm('{L_INFO_EMPTYDB1} \'{DB_NAME}\' {L_INFO_KILLDB}')) return false;">
	<input class="Formbutton" type="submit" name="optimize{DB_ID}" value="{L_OPTIMIZE_DATABASES}">
    <input class="Formbutton" type="submit" name="check{DB_ID}" value="{L_CHECK_TABLES}">
    <!-- BEGIN DISABLED_KEYS_FOUND -->
        <input class="Formbutton" type="submit" name="enableKeys{DB_ID}" value="Enable keys">
    <!-- END DISABLED_KEYS_FOUND -->
</form>
<br><br><br>