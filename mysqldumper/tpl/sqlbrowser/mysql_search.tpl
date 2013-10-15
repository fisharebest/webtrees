<div id="sqlsearch">
<form action="sql.php?db={DB_NAME_URLENCODED}&amp;search=1" method="POST" name="suche">
<fieldset>
    <legend><b>{LANG_SQLSEARCH}</b></legend>
		<b>{LANG_SQL_SEARCHWORDS}:</b> <input type="text" style="width:300px;" name="suchbegriffe" value="{SUCHBEGRIFFE}">
		<input type="submit" name="suche" value="{LANG_START_SQLSEARCH}" class="Formbutton">
		<input type="submit" name="reset" value="{LANG_RESET_SEARCHWORDS}" class="Formbutton" onclick="document.suche.suchbegriffe.value='';">
		<span style="font-size:10px;">{LANG_SEARCH_EXPLAIN}<br></span>
		<fieldset>
			<legend><b>{LANG_SEARCH_OPTIONS}</b></legend>
				<input type="radio" id="and" name="suchart" value="AND"{AND_SEARCH}>
				<label for="and" onmouseover="this.style.cursor='pointer'">{LANG_SEARCH_OPTIONS_AND}</label>
				<br>
				<input type="radio" id="or" name="suchart" value="OR"{OR_SEARCH}>
				<label for="or" onmouseover="this.style.cursor='pointer'">{LANG_SEARCH_OPTIONS_OR}</label>
				<br>
				<input type="radio" id="concat" name="suchart" value="CONCAT"{CONCAT_SEARCH}>
				<label for="concat" onmouseover="this.style.cursor='pointer'">{LANG_SEARCH_OPTIONS_CONCAT}</label>
				<br>
				{LANG_SEARCH_IN_TABLE}:&nbsp;&nbsp;
				<select name="table_selected" size="1" onchange="document.suche.submit();">
				{TABLE_OPTIONS}
				</select>
				<input type="hidden" name="offset" value="0">
				{HIDDEN_FIELDS}
 		</fieldset>
</fieldset>
</form>
<!-- BEGIN HITS -->
	{HITS.LANG_SEARCH_RESULTS}:<br>

	<input type="button" value="&nbsp;<<&nbsp;" class="Formbutton"
	onclick="document.suche.offset.value='{HITS.LAST_OFFSET}';document.suche.submit();"
	{HITS.BACK_BUTTON_DISABLED} accesskey="c">&nbsp;&nbsp;

	<input type="button" value="&nbsp;>>&nbsp;" class="Formbutton"
	onclick="document.suche.offset.value='{HITS.NEXT_OFFSET}';document.suche.submit();"
	{HITS.NEXT_BUTTON_DISABLED} accesskey="v">&nbsp;&nbsp;
	{HITS.LANG_ACCESS_KEYS}

	<br><br>
	<table cellpadding="0" cellspacing="0" class="bdr">
	<tr class="thead">
		<th class="thead">&nbsp;</th>
		<th class="thead" style="text-align:left">#</th>
		<!-- BEGIN TABLEHEAD -->
			<th class="thead" style="text-align:left">{HITS.TABLEHEAD.KEY}</th>
		<!-- END TABLEHEAD -->
	</tr>
		<!-- BEGIN TABLEROW -->
		<tr class="{HITS.TABLEROW.CLASS}">
			<td nowrap="nowrap">
				<a href="{HITS.TABLEROW.LINK_EDIT}">{HITS.TABLEROW.ICON_EDIT}</a><a href="{HITS.TABLEROW.LINK_DELETE}">{HITS.TABLEROW.ICON_DELETE}</a>
			</td>
			<td style="text-align:right;">{HITS.TABLEROW.NR}.&nbsp;</td>
			<!-- BEGIN TABLEDATA -->
				<td>{HITS.TABLEROW.TABLEDATA.VAL}</td>
			<!-- END TABLEDATA -->	
		</tr>
		<!-- END TABLEROW -->
	</table>
<!-- END HITS -->

<!-- BEGIN NO_RESULTS -->
	{NO_RESULTS.LANG_SEARCH_NO_RESULTS}
<!-- END NO_RESULTS -->

<!-- BEGIN NO_ENTRIES -->
	{NO_ENTRIES.LANG_NO_ENTRIES}
<!-- END NO_ENTRIES -->
</div>

<script type="text/javascript">document.suche.suchbegriffe.focus();</script>
