<?php
$lang['L_INSTALLFINISHED']="<br>die Installation ist abgeschlossen   --> <a href=\"index.php\">starte MySQLDumper</a><br>";
$lang['L_INSTALL_TOMENU']="zum Hauptmenü";
$lang['L_INSTALLMENU']="Hauptmenü";
$lang['L_STEP']="Schritt";
$lang['L_INSTALL']="Installation";
$lang['L_UNINSTALL']="Deinstallation";
$lang['L_TOOLS']="Tools";
$lang['L_EDITCONF']="Konfiguration bearbeiten";
$lang['L_OSWEITER']="ohne Speichern weiter";
$lang['L_ERRORMAN']="<strong>Fehler beim Schreiben der Konfiguration!</strong><br>Bitte editieren Sie die Datei ";
$lang['L_MANUELL']="manuell";
$lang['L_CREATEDIRS']="erstelle Verzeichnisse";
$lang['L_INSTALL_CONTINUE']="mit der Installation fortfahren";
$lang['L_CONNECTTOMYSQL']=" zu MySQL verbinden ";
$lang['L_DBPARAMETER']="Datenbank-Parameter";
$lang['L_CONFIGNOTWRITABLE']="Die Datei \"config.php\" ist nicht beschreibbar.
Geben Sie ihr mit einem FTP-Programm entsprechende Rechte, z. B. den CHMod-Wert 0777.";
$lang['L_DBCONNECTION']="Datenbank-Verbindung";
$lang['L_CONNECTIONERROR']="Fehler: Es konnte keine Verbindung herstellt werden.";
$lang['L_CONNECTION_OK']="Datenbank-Verbindung wurde hergestellt.";
$lang['L_SAVEANDCONTINUE']="speichern und Installation fortsetzen";
$lang['L_CONFBASIC']="Grundeinstellungen";
$lang['L_INSTALL_STEP2FINISHED']="Die Einstellungen wurden erfolgreich gesichert.";
$lang['L_INSTALL_STEP2_1']="Installation mit Standardkonfiguration fortsetzen";
$lang['L_LASTSTEP']="Abschluss der Installation";
$lang['L_FTPMODE']="Verzeichnisse per FTP erzeugen (safe_mode)";
$lang['L_IDOMANUAL']="Ich erstelle die Verzeichnisse manuell";
$lang['L_DOFROM']="ausgehend von";
$lang['L_FTPMODE2']="Erstelle die Verzeichnisse per FTP:";
$lang['L_CONNECT']="verbinden";
$lang['L_DIRS_CREATED']="Die Verzeichnisse wurden ordnungsgemäß erstellt.";
$lang['L_CONNECT_TO']="verbinde zu";
$lang['L_CHANGEDIR']="Wechsel ins Verzeichnis";
$lang['L_CHANGEDIRERROR']="Wechsel ins Verzeichnis nicht möglich";
$lang['L_FTP_OK']="FTP-Parameter sind ok";
$lang['L_CREATEDIRS2']="Verzeichnisse erstellen";
$lang['L_FTP_NOTCONNECTED']="FTP-Verbindung nicht hergestellt!";
$lang['L_CONNWITH']="Verbindung mit";
$lang['L_ASUSER']="als Benutzer";
$lang['L_NOTPOSSIBLE']="nicht möglich";
$lang['L_DIRCR1']="erstelle Arbeitsverzeichnis";
$lang['L_DIRCR2']="erstelle Backup-Verzeichnis";
$lang['L_DIRCR4']="erstelle Log-Verzeichnis";
$lang['L_DIRCR5']="erstelle Konfigurationsverzeichnis";
$lang['L_INDIR']="bin im Verzeichnis";
$lang['L_CHECK_DIRS']="Verzeichnisse überprüfen";
$lang['L_DISABLEDFUNCTIONS']="Abgeschaltete Funktionen";
$lang['L_NOFTPPOSSIBLE']="Es stehen keine FTP-Funktionen zur Verfügung!";
$lang['L_NOGZPOSSIBLE']="Es stehen keine Kompressions-Funktionen zur Verfügung!";
$lang['L_UI1']="Es werden alle Arbeitsverzeichnisse incl. den darin enthaltenen Backups gelöscht.";
$lang['L_UI2']="Sind Sie sicher, dass Sie das möchten?";
$lang['L_UI3']="Nein, sofort abbrechen";
$lang['L_UI4']="ja, bitte fortfahren";
$lang['L_UI5']="lösche Arbeitsverzeichnis";
$lang['L_UI6']="alles wurde erfolgreich gelöscht.";
$lang['L_UI7']="Bitte löschen Sie das Skriptverzeichnis";
$lang['L_UI8']="eine Ebene nach oben";
$lang['L_UI9']="Ein Fehler trat auf, löschen war nicht möglich</p>Fehler bei Verzeichnis ";
$lang['L_IMPORT']="Konfiguration importieren";
$lang['L_IMPORT3']="Die Konfiguration wurde geladen...";
$lang['L_IMPORT4']="Die Konfiguration wurde gesichert.";
$lang['L_IMPORT5']="MySQLDumper starten";
$lang['L_IMPORT6']="Installations-Menü";
$lang['L_IMPORT7']="Konfiguration hochladen";
$lang['L_IMPORT8']="zurück zum Upload";
$lang['L_IMPORT9']="Dies ist keine Konfigurationssicherung!";
$lang['L_IMPORT10']="Die Konfiguration wurde erfolgreich hochgeladen...";
$lang['L_IMPORT11']="<strong>Fehler: </strong>Es gab Probleme beim Schreiben der sql_statements.";
$lang['L_IMPORT12']="<strong>Fehler: </strong>Es gab Probleme beim Schreiben der config.php.";
$lang['L_INSTALL_HELP_PORT']="(leer = Standardport)";
$lang['L_INSTALL_HELP_SOCKET']="(leer = Standardsocket)";
$lang['L_TRYAGAIN']="noch einmal versuchen";
$lang['L_SOCKET']="Socket";
$lang['L_PORT']="Port";
$lang['L_FOUND_DB']="gefundene DB: ";
$lang['L_FM_FILEUPLOAD']="Datei hochladen";
$lang['L_PASS']="Passwort";
$lang['L_NO_DB_FOUND_INFO']="Die Verbindung zur Datenbank konnte erfolgreich hergestellt werden.<br>
Ihre Zugangsdaten sind gültig und wurden vom MySQL-Server akzeptiert.<br>
Leider konnte MySQLDumper keine Datenbank finden.<br>
Die automatische Erkennung per Programm ist bei manchen Hostern gesperrt.<br>
Sie müssen Ihre Datenbank nach dem Abschluß der Installation unter dem Menüpunkt \"Konfiguration\" \"Verbindungsparameter einblenden\" angeben.<br>
Bitte begeben Sie sich nach Abschluß der Installation umgehend dort hin und tragen den Namen Ihrer Datenbank dort ein.";
$lang['L_SAFEMODEDESC']="Da PHP auf diesem Server mit der Option \"safe_mode=on\" ausgeführt wird, müssen folgende Verzeichnisse von Hand angelegt werden (dies können Sie mit Ihrem FTP-Programm erledigen):


";
$lang['L_ENTER_DB_INFO']="Klicken Sie zuerst auf den Button \"zu MySQL verbinden\". Nur wenn daraufhin keine Datenbank erkannt werden konnte, ist hier eine Angabe notwendig.";


?>