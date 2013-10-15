<?php
$lang['L_CONVERT_START']="Konvertierung starten";
$lang['L_CONVERT_TITLE']="Konvertiere Dump ins MSD-Format";
$lang['L_CONVERT_WRONG_PARAMETERS']="Falsche Parameter! Konvertierung ist nicht möglich.";
$lang['L_FM_UPLOADFILEREQUEST']="Geben Sie bitte eine Datei an.";
$lang['L_FM_UPLOADNOTALLOWED1']="Dieser Dateityp ist nicht erlaubt.";
$lang['L_FM_UPLOADNOTALLOWED2']="Gültige Typen sind: *.gz und *.sql-Dateien";
$lang['L_FM_UPLOADMOVEERROR']="Die hochgeladene Datei konnte nicht in den richtigen Ordner verschoben werden.";
$lang['L_FM_UPLOADFAILED']="Der Upload ist leider fehlgeschlagen!";
$lang['L_FM_UPLOADFILEEXISTS']="Es existiert bereits eine Datei mit diesem Namen!";
$lang['L_FM_NOFILE']="Sie haben gar keine Datei ausgewählt!";
$lang['L_FM_DELETE1']="Die Datei ";
$lang['L_FM_DELETE2']=" wurde erfolgreich gelöscht.";
$lang['L_FM_DELETE3']=" konnte nicht gelöscht werden!";
$lang['L_FM_CHOOSE_FILE']="Gewählte Datei:";
$lang['L_FM_FILESIZE']="Dateigröße";
$lang['L_FM_FILEDATE']="Datum";
$lang['L_FM_NOFILESFOUND']="Keine Datei gefunden.";
$lang['L_FM_TABLES']="Tabellen";
$lang['L_FM_RECORDS']="Einträge";
$lang['L_FM_ALL_BU']="alle Backups";
$lang['L_FM_ANZ_BU']="Backups";
$lang['L_FM_LAST_BU']="letztes Backup";
$lang['L_FM_TOTALSIZE']="Gesamtgröße";
$lang['L_FM_SELECTTABLES']="Auswahl bestimmter Tabellen";
$lang['L_FM_COMMENT']="Kommentar eingeben";
$lang['L_FM_RESTORE']="Wiederherstellen";
$lang['L_FM_ALERTRESTORE1']="Soll die Datenbank ";
$lang['L_FM_ALERTRESTORE2']="mit den Inhalten der Datei";
$lang['L_FM_ALERTRESTORE3']="wiederhergestellt werden?";
$lang['L_FM_DELETE']="Ausgewählte Dateien löschen";
$lang['L_FM_ASKDELETE1']="Möchten Sie die Datei(en) ";
$lang['L_FM_ASKDELETE2']="wirklich löschen?";
$lang['L_FM_ASKDELETE3']="Möchten Sie Autodelete nach den eingestellten Regeln jetzt ausführen?";
$lang['L_FM_ASKDELETE4']="Möchten Sie alle Backup-Dateien jetzt löschen?";
$lang['L_FM_ASKDELETE5']="Möchten Sie alle Backup-Dateien mit ";
$lang['L_FM_ASKDELETE5_2']="_* jetzt löschen?";
$lang['L_FM_DELETEAUTO']="Autodelete manuell ausführen";
$lang['L_FM_DELETEALL']="Alle Backup-Dateien löschen";
$lang['L_FM_DELETEALLFILTER']="Alle löschen mit ";
$lang['L_FM_DELETEALLFILTER2']="_*";
$lang['L_FM_STARTDUMP']="Neues Backup starten";
$lang['L_FM_FILEUPLOAD']="Datei hochladen";
$lang['L_FM_DBNAME']="Datenbankname";
$lang['L_FM_FILES1']="Datenbank-Backups";
$lang['L_FM_FILES2']="Datenbank-Strukturen";
$lang['L_FM_AUTODEL1']="Autodelete: Folgende Dateien wurden aufgrund der maximalen Dateianzahl gelöscht:";
$lang['L_DELETE_FILE_SUCCESS']="Die Datei \"%s\" wurde erfolgreich gelöscht.";
$lang['L_FM_DUMPSETTINGS']="Einstellungen für das Backup";
$lang['L_FM_OLDBACKUP']="(unbekannt)";
$lang['L_FM_RESTORE_HEADER']="Wiederherstellung der Datenbank \"<strong>%s</strong>\"";
$lang['L_DELETE_FILE_ERROR']="Die Datei \"%s\" konnte nicht gelöscht werden!";
$lang['L_FM_DUMP_HEADER']="Backup";
$lang['L_DOCRONBUTTON']="Perl-Cronscript ausführen";
$lang['L_DOPERLTEST']="Perl-Module testen";
$lang['L_DOSIMPLETEST']="Perl testen";
$lang['L_PERLOUTPUT1']="Eintrag in crondump.pl für absolute_path_of_configdir";
$lang['L_PERLOUTPUT2']="Aufruf im Browser oder für externen Cronjob";
$lang['L_PERLOUTPUT3']="Aufruf in der Shell oder für die Crontab";
$lang['L_RESTORE_OF_TABLES']="Wiederherstellen bestimmter Tabellen";
$lang['L_CONVERTER']="Backup-Konverter";
$lang['L_CONVERT_FILE']="zu konvertierende Datei";
$lang['L_CONVERT_FILENAME']="Name der Zieldatei (ohne Endung)";
$lang['L_CONVERTING']="Konvertierung";
$lang['L_CONVERT_FILEREAD']="Datei '%s' wird eingelesen";
$lang['L_CONVERT_FINISHED']="Konvertierung abgeschlossen, '%s' wurde erzeugt.";
$lang['L_NO_MSD_BACKUPFILE']="Dateien anderer Programme";
$lang['L_MAX_UPLOAD_SIZE']="Maximale Dateigröße";
$lang['L_MAX_UPLOAD_SIZE_INFO']="Wenn Ihre Backup-Datei größer als das angegebene Limit ist, dann müssen Sie diese per FTP in den \"work/backup\"-Ordner hochladen.
Danach wird diese Datei hier in der Verwaltung angezeigt und lässt sich für eine Wiederherstellung auswählen.";
$lang['L_ENCODING']="Kodierung";
$lang['L_FM_CHOOSE_ENCODING']="Kodierung der Backupdatei wählen";
$lang['L_CHOOSE_CHARSET']="Leider konnte nicht automatisch ermittelt werden mit welchem Zeichensatz diese Backupdatei seinerzeit angelegt wurde.
<br>Sie müssen die Kodierung, in der Zeichenketten in dieser Datei vorliegen, manuell angeben.
<br>Danach stellt MySQLDumper die Verbindungskennung zum MySQL-Server auf den ausgewählten Zeichensatz und beginnt mit der Wiederherstellung der Daten.
<br>Sollten Sie nach der Wiederherstellung Probleme mit Sonderzeichen entdecken, so können Sie versuchen, das Backup mit einer anderen Zeichensatzauswahl wiederherzustellen.
<br>Viel Glück. ;)";
$lang['L_DOWNLOAD_FILE']="Datei herunterladen";
$lang['L_BACKUP_NOT_POSSIBLE'] = "Eine Sicherung der Systemdatenbank `%s` ist nicht möglich!";

?>