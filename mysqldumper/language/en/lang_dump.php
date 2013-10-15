<?php
$lang['L_DUMP_HEADLINE']="Create backup...";
$lang['L_GZIP_COMPRESSION']="GZip Compression";
$lang['L_SAVING_TABLE']="Saving table ";
$lang['L_OF']="of";
$lang['L_ACTUAL_TABLE']="Actual table";
$lang['L_PROGRESS_TABLE']="Progress of table";
$lang['L_PROGRESS_OVER_ALL']="Overall Progress";
$lang['L_ENTRY']="Entry";
$lang['L_DONE']="Done!";
$lang['L_DUMP_SUCCESSFUL']=" was successfully created.";
$lang['L_UPTO']="up to";
$lang['L_EMAIL_WAS_SEND']="Email was successfully sent to ";
$lang['L_BACK_TO_CONTROL']="Continue";
$lang['L_BACK_TO_OVERVIEW']="Database Overview";
$lang['L_DUMP_FILENAME']="Backup File: ";
$lang['L_WITHPRAEFIX']="with prefix";
$lang['L_DUMP_NOTABLES']="No tables found in database `<b>%s</b>` ";
$lang['L_DUMP_ENDERGEBNIS']="The file contains <b>%s</b> tables with <b>%s</b> records.<br>";
$lang['L_MAILERROR']="Sending of email failed!";
$lang['L_EMAILBODY_ATTACH']="The Attachment contains the backup of your MySQL-Database.<br>Backup of Database `%s`
<br><br>Following File was created:<br><br>%s <br><br>Kind regards<br><br>MySQLDumper<br>";
$lang['L_EMAILBODY_MP_NOATTACH']="A Multipart Backup was created.<br>The Backup files are not attached to this email!<br>Backup of Database `%s`
<br><br>Following Files were created:<br><br>%s
<br><br>Kind regards<br><br>MySQLDumper<br>";
$lang['L_EMAILBODY_MP_ATTACH']="A Multipart Backup was created.<br>The Backup files are attached to separate emails.<br>Backup of Database `%s`
<br><br>Following Files were created:<br><br>%s <br><br>Kind regards<br><br>MySQLDumper<br>";
$lang['L_EMAILBODY_FOOTER']="`<br><br>Kind regards<br><br>MySQLDumper<br>";
$lang['L_EMAILBODY_TOOBIG']="The Backup file exceeded the maximum size of %s and was not attached to this email.<br>Backup of Database `%s`
<br><br>Following File was created:<br><br>%s
<br><br>Kind regards<br><br>MySQLDumper<br>";
$lang['L_EMAILBODY_NOATTACH']="Files are not attached to this email!<br>Backup of Database `%s`
<br><br>Following File was created:<br><br>%s
<br><br>Kind regards<br><br>MySQLDumper<br>";
$lang['L_EMAIL_ONLY_ATTACHMENT']=" ... attachment only.";
$lang['L_TABLESELECTION']="Table selection";
$lang['L_SELECTALL']="Select All";
$lang['L_DESELECTALL']="Deselect all";
$lang['L_STARTDUMP']="Start Backup";
$lang['L_LASTBUFROM']="last update from";
$lang['L_NOT_SUPPORTED']="This backup doesn't support this function.";
$lang['L_MULTIDUMP']="Multidump: Backup of <b>%d</b> Databases done.";
$lang['L_FILESENDFTP']="send file via FTP... please be patient. ";
$lang['L_FTPCONNERROR']="FTP connection not established! Connection with ";
$lang['L_FTPCONNERROR1']=" as user ";
$lang['L_FTPCONNERROR2']=" not possible";
$lang['L_FTPCONNERROR3']="FTP Upload failed! ";
$lang['L_FTPCONNECTED1']="Connected with ";
$lang['L_FTPCONNECTED2']=" on ";
$lang['L_FTPCONNECTED3']=" transfer successful";
$lang['L_NR_TABLES_SELECTED']="- with %s selected tables";
$lang['L_NR_TABLES_OPTIMIZED']="<span class=\"small\">%s tables have been optimized.</span>";
$lang['L_DUMP_ERRORS']="<p class=\"error\">%s errors occured: <a href=\"log.php?r=3\">view</a></p>";
$lang['L_FATAL_ERROR_DUMP']="Fatal error: the CREATE-Statement of table '%s' in database '%s' couldn't be read!";


?>