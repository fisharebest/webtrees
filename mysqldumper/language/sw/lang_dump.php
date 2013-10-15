<?php
$lang['L_DUMP_HEADLINE']="skapa backup ...";
$lang['L_GZIP_COMPRESSION']="GZIP-komprimering";
$lang['L_SAVING_TABLE']="Sparar tabellen";
$lang['L_OF']="av";
$lang['L_ACTUAL_TABLE']="Aktuell tabell";
$lang['L_PROGRESS_TABLE']="Genomfört av tabell";
$lang['L_PROGRESS_OVER_ALL']="Genomfört totalt";
$lang['L_ENTRY']="Post";
$lang['L_DONE']="Färdig!";
$lang['L_DUMP_SUCCESSFUL']=" har skapats.";
$lang['L_UPTO']="upp till";
$lang['L_EMAIL_WAS_SEND']="Epostmeddelandet har skickats till";
$lang['L_BACK_TO_CONTROL']="fortsätt";
$lang['L_BACK_TO_OVERVIEW']="Databasöversikt";
$lang['L_DUMP_FILENAME']="Backup-fil: ";
$lang['L_WITHPRAEFIX']="med prefix";
$lang['L_DUMP_NOTABLES']="Inga tabeller hittades i databasen `<b>%s</b>`.";
$lang['L_DUMP_ENDERGEBNIS']="<b>%s</b> tabeller med totalt <b>%s</b> dataposter har säkrats.<br>";
$lang['L_MAILERROR']="Tyvärr uppträdde ett fel när epostmeddelandet skickades!";
$lang['L_EMAILBODY_ATTACH']="Här kommer backupen av din MySQLdatabas.<br>Backup av databasen `%s`
<br><br>Följande fil har skapats:<br><br>%s <br><br>Med vänliga hälsningar<br><br>MySQLDumper<br>";
$lang['L_EMAILBODY_MP_NOATTACH']="En multipart-backup har skapats.<br>Backuperna levereras EJ som bilaga i mail!<br>Backup av databasen `%s`
<br><br>Följande filer har skapats:<br><br>%s<br><br><br>Med vänliga hälsningar<br><br>MySQLDumper<br>";
$lang['L_EMAILBODY_MP_ATTACH']="En multipart-backup har skapats.<br>Backupen levereras i separata mail!<br>Backup av databasen `%s`
<br><br>Följande filer har skapats:<br><br>%s<br><br><br>Med vänliga hälsningar<br><br>MySQLDumper<br>";
$lang['L_EMAILBODY_FOOTER']="<br><br><br>Med vänliga hälsningar<br><br>MySQLDumper<br>";
$lang['L_EMAILBODY_TOOBIG']="Backupen överskrider den maximala storleken på %s och har därför ej bifogats.<br>Backup av databasen `%s` <br><br>Följande fil har skapats:<br><br>%s <br><br>Vänliga hälsningar<br>Din MySQLDumper<br>";
$lang['L_EMAILBODY_NOATTACH']="Backuperna levereras EJ som bilaga i mail!<br>Backup av databasen `%s` <br><br>Följande filer har skapats:<br><br>%s<br><br><br>Med vänliga hälsningar<br><br>MySQLDumper<br>";
$lang['L_EMAIL_ONLY_ATTACHMENT']="... endast bilagan";
$lang['L_TABLESELECTION']="Välj tabeller";
$lang['L_SELECTALL']="markera alla";
$lang['L_DESELECTALL']="Avmarkera alla";
$lang['L_STARTDUMP']="Starta backup";
$lang['L_LASTBUFROM']="senaste uppdatering den";
$lang['L_NOT_SUPPORTED']="Denna backup har inget stöd för den funktionen.";
$lang['L_MULTIDUMP']="Multidump: <b>%d</b> databaser har säkrats.";
$lang['L_FILESENDFTP']="skickar filen via FTP ... var god vänta.";
$lang['L_FTPCONNERROR']="FTP-förbindelsen kunde ej upprättas! Förbindelse med ";
$lang['L_FTPCONNERROR1']="som användare";
$lang['L_FTPCONNERROR2']="ej möjligt";
$lang['L_FTPCONNERROR3']="FTP-överföringen var korrupt!";
$lang['L_FTPCONNECTED1']="Ansluten till";
$lang['L_FTPCONNECTED2']="hos";
$lang['L_FTPCONNECTED3']="överförd";
$lang['L_NR_TABLES_SELECTED']="- med %s valda tabeller";
$lang['L_NR_TABLES_OPTIMIZED']="<span class=\"small\">%s tabeller har optimerats.</span>";
$lang['L_DUMP_ERRORS']="<p class=\"error\">%s fel har uppträtt: <a href=\"log.php?r=3\">visa</a></p>";
$lang['L_FATAL_ERROR_DUMP']="Kritiskt fel: CREATE-kommandot i tabellen '%s' i databasen '%s' kunde ej läsas!";


?>