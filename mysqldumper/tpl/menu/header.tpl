{MSD_HEADER}
{MSD_HEADLINE}
<!-- BEGIN CONFIG_REFRESH_TRUE -->
	{CONFIG_REFRESH}
<!-- END CONFIG_REFRESH_TRUE -->

<!-- BEGIN DB_REFRESH -->
	<script language="JavaScript" type="text/javascript">
	var curl=parent.MySQL_Dumper_content.location.href.split("/");
	var cdatei=curl.pop();
	var ca=cdatei.split(".");
	if(ca[0]!="dump" && ca[0]!="restore" && ca[0]!="frameset" && ca[0]!="crondump") {
		parent.MySQL_Dumper_content.location.href=parent.MySQL_Dumper_content.location.href;
	}
	if(ca[0]=="sql")
	{
		parent.MySQL_Dumper_content.location.href='sql.php';
		{DB_REFRESH_INDEX}
	}
	</script>
<!-- END DB_REFRESH -->

<!-- BEGIN CHANGED_LANGUAGE -->
	<script language="JavaScript" type="text/javascript">self.location.href='menu.php';</script>
<!-- END CHANGED_LANGUAGE -->
<a href="http://www.mysqldumper.net" target="_blank" title="{L_VISIT_HOMEPAGE} {CONFIG_HOMEPAGE}"><img src="css/{CONFIG_THEME}/pics/h1_logo.gif" alt="MySQLDumper - Homepage"></a>
<div id="version">
	<a href="main.php" title="{L_HOME}" target="MySQL_Dumper_content" style="text-decoration: none">
		<span class="version-line">Version {MSD_VERSION}</span>
		<img src="css/{CONFIG_THEME}/pics/navi_bg.jpg" alt="">
	</a>
</div>

