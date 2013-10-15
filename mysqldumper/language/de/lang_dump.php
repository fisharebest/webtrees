<?php
$lang['L_DUMP_HEADLINE']="erzeuge Backup...";
$lang['L_GZIP_COMPRESSION']="GZip-Kompression";
$lang['L_SAVING_TABLE']="Speichere Tabelle ";
$lang['L_OF']="von";
$lang['L_ACTUAL_TABLE']="Aktuelle Tabelle";
$lang['L_PROGRESS_TABLE']="Fortschritt Tabelle";
$lang['L_PROGRESS_OVER_ALL']="Fortschritt gesamt";
$lang['L_ENTRY']="Eintrag";
$lang['L_DONE']="Fertig!";
$lang['L_DUMP_SUCCESSFUL']=" wurde erfolgreich erstellt.";
$lang['L_UPTO']="bis";
$lang['L_EMAIL_WAS_SEND']="Die E-Mail wurde erfolgreich verschickt an ";
$lang['L_BACK_TO_CONTROL']="weiter";
$lang['L_BACK_TO_OVERVIEW']="Datenbank-Übersicht";
$lang['L_DUMP_FILENAME']="Backup-Datei: ";
$lang['L_WITHPRAEFIX']="mit Praefix";
$lang['L_DUMP_NOTABLES']="Es konnten keine Tabellen in der Datenbank `<b>%s</b>` gefunden werden.";
$lang['L_DUMP_ENDERGEBNIS']="Es wurden <b>%s</b> Tabellen mit insgesamt <b>%s</b> Datensätzen gesichert.<br>";
$lang['L_MAILERROR']="Leider ist beim Verschicken der E-Mail ein Fehler aufgetreten!";
$lang['L_EMAILBODY_ATTACH']="In der Anlage finden Sie die Sicherung Ihrer MySQL-Datenbank.<br>Sicherung der Datenbank `%s`
<br><br>Folgende Datei wurde erzeugt:<br><br>%s <br><br>Viele Grüße<br><br>MySQLDumper<br>";
$lang['L_EMAILBODY_MP_NOATTACH']="Es wurde eine Multipart-Sicherung erstellt.<br>Die Sicherungen werden nicht als Anhang mitgeliefert!<br>Sicherung der Datenbank `%s`
<br><br>Folgende Dateien wurden erzeugt:<br><br>%s<br><br><br>Viele Grüße<br><br>MySQLDumper<br>";
$lang['L_EMAILBODY_MP_ATTACH']="Es wurde eine Multipart-Sicherung erstellt.<br>Die Sicherungen werden in separaten E-Mails als Anhang geliefert!<br>Sicherung der Datenbank `%s`
<br><br>Folgende Dateien wurden erzeugt:<br><br>%s<br><br><br>Viele Grüße<br><br>MySQLDumper<br>";
$lang['L_EMAILBODY_FOOTER']="<br><br><br>Viele Grüße<br><br>MySQLDumper<br>";
$lang['L_EMAILBODY_TOOBIG']="Die Sicherung überschreitet die Maximalgröße von %s und wurde daher nicht angehängt.<br>Sicherung der Datenbank `%s`
<br><br>Folgende Datei wurde erzeugt:<br><br>%s
<br><br>Viele Grüße<br><br>MySQLDumper<br>";
$lang['L_EMAILBODY_NOATTACH']="Das Backup wurde nicht angehängt.<br>Sicherung der Datenbank `%s`
<br><br>Folgende Datei wurde erzeugt:<br><br>%s
<br><br>Viele Grüße<br><br>MySQLDumper<br>";
$lang['L_EMAIL_ONLY_ATTACHMENT']=" ... nur der Anhang";
$lang['L_TABLESELECTION']="Tabellenauswahl";
$lang['L_SELECTALL']="alle auswählen";
$lang['L_DESELECTALL']="Auswahl aufheben";
$lang['L_STARTDUMP']="Backup starten";
$lang['L_LASTBUFROM']="letztes Update vom";
$lang['L_NOT_SUPPORTED']="Dieses Backup unterstützt diese Funktion nicht.";
$lang['L_MULTIDUMP']="Multidump: Es wurden <b>%d</b> Datenbanken gesichert.";
$lang['L_FILESENDFTP']="versende File via FTP... bitte habe etwas Geduld. ";
$lang['L_FTPCONNERROR']="FTP-Verbindung nicht hergestellt! Verbindung mit ";
$lang['L_FTPCONNERROR1']=" als Benutzer ";
$lang['L_FTPCONNERROR2']=" nicht möglich";
$lang['L_FTPCONNERROR3']="FTP-Upload war fehlerhaft! ";
$lang['L_FTPCONNECTED1']="Verbunden mit ";
$lang['L_FTPCONNECTED2']=" auf ";
$lang['L_FTPCONNECTED3']=" geschrieben";
$lang['L_NR_TABLES_SELECTED']="- mit %s gewählten Tabellen";
$lang['L_NR_TABLES_OPTIMIZED']="<span class=\"small\">%s Tabellen wurden optimiert.</span>";
$lang['L_DUMP_ERRORS']="<p class=\"error\">%s Fehler aufgetreten: <a href=\"log.php?r=3\">anzeigen</a></p>";
$lang['L_FATAL_ERROR_DUMP']="Schwerwiegender Fehler: die CREATE-Anweisung der Tabelle '%s' in der Datenbank '%s' konnte nicht gelesen werden! ";


?>