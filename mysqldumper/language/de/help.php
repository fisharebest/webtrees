<div id="content">
<h3>Über dieses Projekt</h3>
Die Idee für dieses Projekt kam von Daniel Schlichtholz.<p>Er eröffnete 2004 das Forum <a href="http://forum.mysqldumper.de" target="_blank">MySQLDumper</a>, und schon bald fanden sich Hobby-Programmierer, die neue Skripte schrieben und die von Daniel erweiterten.<br>Innerhalb kürzester Zeit entstand aus dem kleinen Backupskript ein stattliches Projekt.<p>Wenn Du Vorschläge zur Verbesserung hast, dann wende Dich an das MySQLDumper-Forum <a href="http://forum.mysqldumper.de" target="_blank">http://forum.mysqldumper.de</a>.<p>Wir wünschen Dir viel Vergnügen mit diesem Projekt.<br><p><h4>Das MySQLDumper-Team</h4>

<table><tr><td><img src="images/logo.gif" alt="MySQLDumper" border="0"></td><td valign="top">
Daniel Schlichtholz</td></tr></table>
<br>

<h3>MySQLDumper Hilfe</h3>

<h4>Download</h4>
Dieses Script erhaltet Ihr auf der Homepage von MySQLDumper.<br>
Es empfiehlt sich, die Homepage regelmäßig zu besuchen, um Updates und
Hilfestellungen zu erlangen.<br>
Die Adresse lautet: <a href="http://www.mysqldumper.de" target="_blank">
http://www.mysqldumper.de</a>

<h4>Systemvoraussetzung</h4>
Das Script arbeitet auf jedem Server (Windows, Linux, ...) <br>
mit PHP >= Version 4.3.4 mit GZip-Unterstützung, MySQL (ab Version 3.23), JavaScript (muss aktiviert sein).

<a href="install.php?language=de" target="_top"><h4>Installation</h4></a>
Die Installation geht einfach von statten.
Entpackt das Archiv in einen beliebigen Ordner.<br>
Ladet alle Dateien auf Euren Webserver hoch. (z. B. in die unterste Ebene in [Server Webverzeichnis/]MySQLDumper)<br>
... fertig!<br>
Ihr könnt MySQLDumper nun im Webbrowser durch "http://mein-webserver/MySQLDumper" aufrufen,<br>
um die Installation abzuschließen. Folgt einfach den Instruktionen.<br>
<br><b>Hinweis:</b><br><i>Falls auf Eurem Server der PHP-Safemode eingeschaltet ist, darf das Script keine
Verzeichnisse erstellen.<br>
Dies müsst Ihr dann von Hand nachholen, da MySqlDump die Daten geordnet in
Verzeichnissen ablegt.<br> 
Das Script bricht mit einer entsprechenden Anweisung ab!<br>
Nachdem Ihr die Verzeichnisse (dem Hinweis entsprechend) erstellt habt, läuft es normal und ohne Einschränkungen.</i>

<a name="perl"></a><h4>Perlskript Anleitung</h4>
Die Meisten haben ein cgi-bin Verzeichnis, in dem Perl ausgeführt werden kann. <br>
Dies ist meist per Browser über http://www.domain.de/cgi-bin/ erreichbar. <br>
<br>
Für diesen Fall bitte folgende Schritte durchführen:<br><br>

1. Rufe im MySQLDumper die Seite Backup auf und klicke auf "Backup Perl". <br>
2. Kopiere den Pfad, der hinter Eintrag in crondump.pl für $absolute_path_of_configdir: steht. <br>
3. Öffne die Datei "crondump.pl" im Editor.<br>
4. Trage den kopierten Pfad dort bei absolute_path_of_configdir ein (keine Leerzeichen).<br>
5. Speichere crondump.pl .<br>
6. Kopiere crondump.pl, sowie perltest.pl und simpletest.pl ins cgi-bin-Verzeichnis (Ascii-Modus im FTP).<br>
7. Gebe den Dateien die Rechte 755. <br>
7b. Wenn die Endung cgi gewünscht ist, ändere bei allen 3 Dateien die Endung von pl -> cgi (umbenennen). <br>
8. Rufe die Konfiguration im MySQLDumper auf.<br>
9. Wähle die Seite Cronscript. <br>
10. Ändere Perl Ausführungspfad in /cgi-bin/ .<br>
10b. Wenn die Scripte .pl haben, ändere die Dateiendung auf .cgi .<br>
11. Speichere die Konfiguration. <br><br>

Fertig, die Skripte lassen sich nun von der Backupseite aufrufen.<br><br>

Wer Perl in allen Verzeichnissen ausführen kann, dem reichen folgende Schritte:<br><br>

1. Rufe im MySQLDumper die Seite Backup auf. <br>
2. Kopiere den Pfad, der hinter Eintrag in crondump.pl für $absolute_path_of_configdir: steht. <br>
3. Öffne die Datei "crondump.pl" im Editor. <br>
4. Trage den kopierten Pfad dort bei absolute_path_of_configdir ein (keine Leerzeichen). <br>
5. Speichere crondump.pl .<br>
6. gebe den Datein die Rechte 755. <br>
6b. Wenn die Endung cgi gewünscht ist, ändere bei allen 3 Dateien die Endung von pl -> cgi (umbenennen). <br>
(ev. 10b+11 von oben)<br>
<br>

Windowsuser müssen bei allen Scripten die erste Zeile ändern, dort steht der Pfad von Perl. Beispiel: <br>
statt: #!/usr/bin/perl -w <br>
jetzt: #!C:\perl\bin\perl.exe -w <br>

<h4>Bedienung</h4><ul>

<h6>Menü</h6>
In der obigen Auswahlliste stellt Ihr die Datenbank ein.<br>
Alle Aktionen beziehen sich auf die hier eingestellte Datenbank.

<h6>Startseite</h6>
Hier erfahrt Ihr Einiges über Euer System, die verschiedenen, installierten
Versionen und Details über die konfigurierten Datenbanken.<br>
Wenn Ihr auf den Datenbanknamen klickt, so seht Ihr eine Auflistung der Tabellen
mit der Anzahl der Einträge, der Größe und das letzte Aktualisierungsdatum.

<h6>Konfiguration</h6>
Hier könnt Ihr eure Konfiguration bearbeiten, abspeichern oder die Ausgangskonfiguration
wieder herstellen.
<ul><br>
	<li><a name="conf1"></a><strong>Konfigurierte Datenbanken:</strong> die Auflistung der konfigurierten Datenbanken. Die aktive Datenbank wird in <b>bold</b> gelistet. </li>
	<li><a name="conf2"></a><strong>Tabellen-Präfix:</strong> hier könnt Ihr (für jede Datenbank) einen Präfix angeben. Dies ist ein Filter, der bei Dumps nur die Tabellen berücksichtigt, die mit diesem Präfix beginnen (z.B. alle Tabellen, die mit "phpBB_" beginnen). Wenn alle Tabellen dieser Datenbank gespeichert werden sollen, 
so lasst das Feld einfach leer.</li>
	<li><a name="conf3"></a><strong>GZip-Kompression:</strong> Hier kann die Kompression aktiviert werden. Empfehlenswert ist die Aktivierung, da die Dateien doch wesentlich kleiner werden und Speicherplatz immer rar ist.</li>
	<li><a name="conf5"></a><strong>Email mit Dumpfile:</strong> Ist diese Option aktiviert, so wird nach abgeschlossenem Backup eine Email mit dem Dump als Anhang verschickt (Vorsicht, Kompression sollte unbedingt an sein, sonst wird der Anhang zu gross und kann evtl. nicht versandt werden!).</li>
	<li><a name="conf6"></a><strong>Email-Adresse:</strong> Empfängeradresse für die Email.</li>
	<li><a name="conf7"></a><strong>Absender der Email:</strong> diese Adresse taucht als Absender in der Email auf.</li>
	<li><a name="conf13"></a><strong>FTP-Transfer: </strong>Ist diese Option aktiviert, so wird nach abgeschlossenem Backup die Backupdatei per FTP versandt.</li>
	<li><a name="conf14"></a><strong>FTP Server: </strong>Die Adresse des FTP-Servers (z. B. ftp.mybackups.de).</li>
	<li><a name="conf15"></a><strong>FTP Server Port: </strong>Der Port des FTP-Servers (in der Regel 21).</li>
	<li><a name="conf16"></a><strong>FTP User: </strong>Der Benutzername des FTP-Accounts. </li>
	<li><a name="conf17"></a><strong>FTP Passwort: </strong>Das Passwort des FTP-Accounts. </li>
	<li><a name="conf18"></a><strong>FTP Upload-Ordner: </strong>Das Verzeichnis, in das die Backupdatei soll (es müssen Upload-Berechtigungen bestehen!).</li>
	<li><a name="conf8"></a><strong>Automatisches Löschen der Backups:</strong> Wenn diese Option aktiviert ist, werden ältere Backups nach den folgenden Regeln automatisch gelöscht.</li>
	<li><a name="conf10"></a><strong>Anzahl von Backupdateien:</strong> Ein Wert > 0 löscht alle Backupdateien, bis auf die hier angegebe Zahl.</li>
	<li><a name="conf11"></a><strong>Sprache:</strong> hier legst du die Sprache für das Interface fest.</li>
</ul>

<h6>Verwaltung</h6>
Hier werden die eigenlichen Aktionen durchgeführt.<br>
Es werden Dir alle Dateien im Backup-Verzeichnis angezeigt.
Für die Aktionen "Restore" und "Delete" muss eine Datei selektiert sein.
<UL>
	<li><strong>Restore:</strong> Hiermit wird die Datenbank mit der ausgewählten Backupdatei aktualisiert.</li>
	<li><strong>Delete:</strong> Hiermit kannst Du die selektierte Backupdatei löschen.</li>
	<li><strong>Neues Backup starten:</strong> Hier startest Du ein neues Backup (Dump) nach den in der Konfiguration eingestellten Parametern.</li>
</UL>

<h6>Log</h6>
Hier kannst Du die Logeinträge sehen und löschen.
<h6>Credits / Hilfe</h6>
diese Seite.
</ul>