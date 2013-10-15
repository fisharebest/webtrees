<div id="pagetitle">{PAGETITLE}</div>
<h6>{L_DATABASE}: {DATABASE}</h6>
<div id="content">
<form name="frm_tbl" action="filemanagement.php" method="post" onSubmit="return chkFormular()">
<div><input type="button" class="Formbutton" onclick="Sel(true);"
	value="{L_SELECT_ALL}"> <input type="button" onclick="Sel(false);"
	value="{L_DESELECT_ALL}" class="Formbutton"> <input type="submit"
	class="Formbutton" name="{BUTTON_NAME}"
	value="{L_START_RESTORE}" onclick="if (!confirm('{L_CONFIRM_RESTORE}')) return false;"></div>
<br>

<table class="bdr">
	<tr class="thead">
		<th>#</th>
		<th>{L_NAME}</th>
		<th>{L_RESTORE}</th>
		<th>{L_ROWS}</th>
		<th>{L_SIZE}</th>
		<th>{L_LAST_UPDATE}</th>
		<th>{L_TABLE_TYPE}</th>
	</tr>
	<!-- BEGIN NO_MSD_BACKUP -->
	<tr>
		<td colspan="7">{L_NO_MSD_BACKUP}</td>
	</tr>
	<!-- END NO_MSD_BACKUP -->
	
	<!-- BEGIN ROW -->
	<tr class="{ROW.CLASS}">
		<td style="text-align:right">{ROW.NR}.</td>
		<td>
			<label for="t{ROW.ID}">{ROW.TABLENAME}</label>
		</td>
		<td class="sm" align="left">
			<input type="checkbox" class="checkbox" name="chk_tbl" id="t{ROW.ID}" value="{ROW.TABLENAME}">
			<!-- 
			<input type="checkbox" class="checkbox" name="chk_tbl_data" id="t_data{ROW.ID}" value="{ROW.TABLENAME}">
			 -->
		</td>
		<td style="text-align:right">
			<strong>{ROW.RECORDS}</strong>
		</td>
		<td style="text-align:right">{ROW.SIZE}</td>
		<td>{ROW.LAST_UPDATE}</td>
		<td>{ROW.TABLETYPE}</td>
	</tr>
	<!-- END ROW -->
</table>
<br>
<div><input type="button" class="Formbutton" onclick="Sel(true);"
	value="{L_SELECT_ALL}"> <input type="button" onclick="Sel(false);"
	value="{L_DESELECT_ALL}" class="Formbutton"> <input type="submit"
	class="Formbutton" name="{BUTTON_NAME}"
	value="{L_START_RESTORE}"></div>
<br>
<br>
<br>
<br>
<input type="hidden" name="tbl_array" value="">
<input type="hidden" name="filename" value="{FILENAME}">
<input type="hidden" name="sel_dump_encoding" value="{SEL_DUMP_ENCODING}">
</form>
</div>
</body>
</html>