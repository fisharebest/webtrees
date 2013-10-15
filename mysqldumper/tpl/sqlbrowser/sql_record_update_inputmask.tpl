<script language="javascript" type="text/javascript">
function switch_area(textarea)
{
	var t=document.getElementById('area_'+textarea);
	var c=document.getElementById('null_'+textarea);
	if (c.checked==true) { t.className="off";t.disabled=true;  }
	else { t.className="";t.disabled=false;  }
}
</script>
<form action="sql.php{TARGET}" method="post">
<input type="hidden" name="recordkey" value="{RECORDKEY}">
<input type="hidden" name="sql_statement" value="{SQL_STATEMENT}">
{HIDDEN_FIELDS}

<table class="bdr">
	<tr class="thead"><th colspan="3">{L_SQL_RECORDEDIT}</th></tr>
	<tr class="thead"><th>{L_NAME}</th><th>NULL</th><th>{L_INHALT}</th></tr>
	<!-- BEGIN ROW -->
	<tr class="dbrow{ROW.CLASS}">
		<td>{ROW.FIELD_NAME}</td>
		<td nowrap="nowrap">
			&nbsp;
			<!-- BEGIN IS_NULLABLE -->
				<input type="checkbox" name="null_{ROW.FIELD_ID}" id="null_{ROW.FIELD_ID}" 
					onchange="switch_area('{ROW.FIELD_ID}')"{ROW.IS_NULLABLE.NULL_CHECKED}>
				&nbsp;
				<label for="null_{ROW.FIELD_ID}">NULL</label>
			<!-- END IS_NULLABLE -->
		</td>
		<td>			
			<!-- BEGIN IS_TEXTINPUT -->
				<input type="text" style="width:100%" name="{ROW.FIELD_ID}" value="{ROW.FIELD_VALUE}">
			<!-- END IS_TEXTINPUT -->

			<!-- BEGIN IS_TEXTAREA -->
			<textarea cols="80" rows="4" name="{ROW.FIELD_ID}" id="area_{ROW-FIELD_ID}">{ROW.FIELD_VALUE}</textarea>
			<!-- END IS_TEXTAREA -->
		</td>
	</tr>
	<!-- END ROW -->
	
	<tr class="dbrow1">
		<td colspan="3">
			<br>
			<input type="hidden" name="feldnamen" value="{FIELDNAMES}">
			<input class="Formbutton" type="submit" name="update" value="{L_SAVE}">
			&nbsp;&nbsp;&nbsp;<input class="Formbutton" type="reset" name="reset" value="{L_RESET}">
			&nbsp;&nbsp;&nbsp;<input class="Formbutton" type="submit" name="cancel" value="{L_CANCEL}">
			<br><br>
		</td>
	</tr>
</table>
</form>