<h5>{L_INFO_DATABASES}</h5>

<table class="bdr">
<tr class="thead">
	<th>#</th>
	<th>{L_DBS}</th>
	<th>{L_TABLES}</th>
</tr>

<!-- BEGIN DB_NOT_FOUND -->
	<tr class="{DB_NOT_FOUND.ROWCLASS}">
		<td class="right">{DB_NOT_FOUND.NR}.</td>
		<td>{DB_NOT_FOUND.DB_NAME}</td>
		<td>{L_INFO_NODB}</td>
	</tr>
<!-- END DB_NOT_FOUND -->

<!-- BEGIN ROW -->
<tr class="{ROW.ROWCLASS}">
	<td class="right">{ROW.NR}.</td>
	<td>
		<img src="{ICONPATH}search.gif" alt="">
		<a href="main.php?action=db&amp;dbid={ROW.DB_ID}">
		{ROW.DB_NAME}</a>
	</td>
	<td class="right">
		{ROW.TABLE_COUNT}
			<!-- BEGIN TABLE -->
				{L_TABLE}
			<!-- END TABLE -->
			<!-- BEGIN TABLES -->
				{L_TABLES}
			<!-- END ABLES -->	
	</td>
</tr>
<!-- END ROW -->

<tr>
	<td colspan="3" class="center">
		<br>
			<input type="button" class="Formbutton" onclick="location.href='sql.php?context=3'" value="{L_CREATE_DATABASE}">
		<br><br>
	</td>
</tr>


</table>
