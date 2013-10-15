<?php
$lang['L_DUMP_HEADLINE']="Lav backup...";
$lang['L_GZIP_COMPRESSION']="GZip-komprimering";
$lang['L_SAVING_TABLE']="Gemmer tabel ";
$lang['L_OF']="af";
$lang['L_ACTUAL_TABLE']="Aktuel tabel";
$lang['L_PROGRESS_TABLE']="Fremskridt i tabel";
$lang['L_PROGRESS_OVER_ALL']="Samlet fremskridt";
$lang['L_ENTRY']="Indlæg";
$lang['L_DONE']="Færdig!";
$lang['L_DUMP_SUCCESSFUL']=" blev fremstillet korrekt.";
$lang['L_UPTO']="op til";
$lang['L_EMAIL_WAS_SEND']="Email blev korrekt sendt til ";
$lang['L_BACK_TO_CONTROL']="Fortsæt";
$lang['L_BACK_TO_OVERVIEW']="Databaseoversigt";
$lang['L_DUMP_FILENAME']="Backup Fil: ";
$lang['L_WITHPRAEFIX']="med præfiks";
$lang['L_DUMP_NOTABLES']="Ingen tabeller fundet i database `<b>%s</b>` ";
$lang['L_DUMP_ENDERGEBNIS']="Filen indeholder <b>%s</b> tabeller med <b>%s</b> poster.<br>";
$lang['L_MAILERROR']="Afsendelse af email slog fejl!";
$lang['L_EMAILBODY_ATTACH']="Den vedhæftede fil indeholder backup af din MySQL-Database.<br>Backup af Database `%s`
<br><br>Følgende fil blev oprettet:<br><br>%s <br><br>Venlig hilsen<br><br>MySQLDumper<br>";
$lang['L_EMAILBODY_MP_NOATTACH']="En Multipart Backup blev oprettet.<br>Backupfilerne er ikke vedhæftet denne email!<br>Backup af Database `%s`
<br><br>Følgende filer blev oprettet:<br><br>%s
<br><br>Venlig hilsen<br><br>MySQLDumper<br>";
$lang['L_EMAILBODY_MP_ATTACH']="En Multipart Backup er blevet oprettet.<br>Backupfilerne er vedhæftet separate emails.<br>Backup af Database `%s`
<br><br>Følgende filer blev oprettet:<br><br>%s <br><br>Med venlig hilsen<br><br>MySQLDumper<br>";
$lang['L_EMAILBODY_FOOTER']="<br><br>Venlig hilsen<br><br>MySQLDumper<br>";
$lang['L_EMAILBODY_TOOBIG']="Backupfilen oversteg maksimumstørrelsen på %s og blev ikke vedhæftet denne email.<br>Backup sf Database `%s`
<br><br>Følgende fil blev oprettet:<br><br>%s
<br><br>Venlig hilsen<br><br>MySQLDumper<br>";
$lang['L_EMAILBODY_NOATTACH']="Filer er ikke vedhæftet denne email!<br>Backup af Database `%s`
<br><br>Følgende fil blev oprettet:<br><br>%s
<br><br>Venlig hilsen<br><br>MySQLDumper<br>";
$lang['L_EMAIL_ONLY_ATTACHMENT']=" ... kun vedhæftet.";
$lang['L_TABLESELECTION']="Tabelvælg";
$lang['L_SELECTALL']="Vælg alle";
$lang['L_DESELECTALL']="Fravælg alle";
$lang['L_STARTDUMP']="Start Backup";
$lang['L_LASTBUFROM']="sidst opdateret fra";
$lang['L_NOT_SUPPORTED']="Denne backup understøtter ikke denne funktion.";
$lang['L_MULTIDUMP']="Multidump: Backup af <b>%d</b> Databaser færdige.";
$lang['L_FILESENDFTP']="send fil via FTP... vær venligst tålmodig. ";
$lang['L_FTPCONNERROR']="FTP-forbindelse ikke etableret! Forbind med ";
$lang['L_FTPCONNERROR1']=" som bruger ";
$lang['L_FTPCONNERROR2']=" ikke muligt";
$lang['L_FTPCONNERROR3']="FTP-upload fejlede! ";
$lang['L_FTPCONNECTED1']="Forbundet med ";
$lang['L_FTPCONNECTED2']=" på ";
$lang['L_FTPCONNECTED3']=" overførsel korrekt gennemført";
$lang['L_NR_TABLES_SELECTED']="- med %s valgte tabeller";
$lang['L_NR_TABLES_OPTIMIZED']="<span class=\"small\">%s tabeller er blevet optimeret.</span>";
$lang['L_DUMP_ERRORS']="<p class=\"error\">%s fejl optrådte: <a href=\"log.php?r=3\">se log</a></p>


";
$lang['L_FATAL_ERROR_DUMP']="Fatal error: the CREATE-Statement of table '%s' in database '%s' couldn't be read!";


?>