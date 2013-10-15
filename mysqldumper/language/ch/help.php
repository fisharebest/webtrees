<div id="content">
<h3>Über das Projäkt</h3>
D Idee für das Projäkt isch vom Daniel Schlichtholz cho.<p>Er hät 2004 das Forum <a href="http://forum.mysqldumper.de" target="_blank">MySQLDumper</a>, eröffnet und scho bald händ sich Hobby-Programmierer, wo neui Skripts schribe und diä vom Daniel erwiteret händ, iigfunde.<br>I kürzischter Ziit isch us däm chline Backupskript es stattlichs Projäkt entschtande.<p>Wänn Du Vorschläg zur Verbesserig häsch, dänn wänd Di as MySQLDumper-Forum <a href="http://forum.mysqldumper.de" target="_blank">http://forum.mysqldumper.de</a>.<p>Mir wünsched Dir vill Vergnüege mit däm Projäkt.<br><p><h4>S MySQLDumper-Team</h4>

<table><tr><td><img src="images/logo.gif" alt="MySQLDumper" border="0"></td><td valign="top">
Daniel Schlichtholz</td></tr></table>
<br>

<h3>MySQLDumper Hilf</h3>

<h4>Download</h4>
Das Script chömed Ihr uf de Homepage vom MySQLDumper über.<br>
Es wäri guet, diä Homepage regelmässig zbsueche, für Updates und
Hilfschtellige zübercho.<br>
D Adrässe isch: <a href="http://www.mysqldumper.de" target="_blank">
http://www.mysqldumper.de</a>

<h4>Syschtemvorussetzig</h4>
S Script schaffet uf jedem Server (Windows, Linux, ...) <br>
mit PHP >= Version 4.3.4 mit GZip-Unterstützig, MySQL (ab Version 3.23), JavaScript (mues aktiviert sii).

<a href="install.php?language=de" target="_top"><h4>Installation</h4></a>
D Installation isch ganz eifach.
Entpacked s Archiv imene beliebige Ordner.<br>
Ladet alli Dateie uf Eure Webserver ufe. (z. B. i di underschti Ebeni in [Server Webverzeichnis/]MySQLDumper)<br>
... fertig!<br>
Ihr chönd MySQLDumper ez im Webbrowser dur "http://mein-webserver/MySQLDumper" ufrüefe,<br>
zum d Installation abschlüsse. Halted Eu eifach ad Instruktione.<br>
<br><b>Hinwiis:</b><br><i>Falls uf Eurem Server de PHP-Safemode igschalte isch, dörf s Script keini
Verzeichniss mache.<br>
Das müender dänn vo Hand nahole, will MySqlDump diä Date gordnet i
Verzeichniss ableit.<br> 
S Script bricht mit ere entsprächende Aawisig ab!<br>
Wänn Ihr diä Verzeichniss (em Hiwiis entsprächend) gmacht händ, laufts normal und ohni Iischränkige.</i>

<a name="perl"></a><h4>Perlskript Aaleitig</h4>
Di Meischte händ es cgi-bin Verzeichnis, wo drin Perl ausgfüert wärde cha. <br>
Das isch meischtens per Browser über http://www.domain.de/cgi-bin/ erreichbar. <br>
<br>
Für dä Fall bitte folgendi Schritt durefüere:<br><br>

1. Rüef im MySQLDumper d Siite Backup uf und klick uf "Backup Perl". <br>
2. Kopier de Pfad, wo hinder em Iitrag im crondump.pl für $absolute_path_of_configdir: schtaht. <br>
3. Mach diä Datei "crondump.pl" im Editor uuf.<br>
4. Träg de kopierti Pfad det bi absolute_path_of_configdir ii (kei Läärzeiche).<br>
5. Spichere crondump.pl .<br>
6. Kopier crondump.pl, perltest.pl und simpletest.pl is cgi-bin-Verzeichnis (Ascii-Modus im FTP).<br>
7. Gib de Dateie d Rächt 755. <br>
7b. Wänn d Endig cgi gwünscht isch, ändere bi allne 3 Dateie d Endig vo pl -> cgi (umbenänne). <br>
8. Rüef d Konfiguration im MySQLDumper uf.<br>
9. Wähl d Siite Cronscript. <br>
10. Ändere Perl Uusfüerigspfad i /cgi-bin/ .<br>
10b. Wänn d Scripts .pl händ, ändere diä Dateiendig uf .cgi .<br>
11. Spichere d Konfiguration. <br><br>

Fertig, diä Skripts lönd sich vo ez aa uf de Backupsiite aufrüefe.<br><br>

Wär Perl i allne Verzeichnis ausfüere cha, däm langet folgendi Schritt:<br><br>

1. Rüef im MySQLDumper d Siite Backup uf. <br>
2. Kopier de Pfad, wo hinder em Iitrag im crondump.pl für $absolute_path_of_configdir: schtaht. <br>
3. Mach d Datei "crondump.pl" im Editor uuf. <br>
4. Träg de kopierti Pfad det bi absolute_path_of_configdir ii (kei Läärzeiche). <br>
5. Spichere crondump.pl .<br>
6. Gib de Dateie d Rächt 755. <br>
6b. Wänn d Endig cgi gwünscht isch, ändere bi allne 3 Dateie d Endung von pl -> cgi (umbenänne). <br>
(ev. 10b+11 von oben)<br>
<br>

Windowsuser müend bi allne Scripts di erscht Ziile ändere, det schtaht de Pfad vo Perl. Bispiil: <br>
statt: #!/usr/bin/perl -w <br>
jetzt: #!C:\perl\bin\perl.exe -w <br>

<h4>Bedienig</h4><ul>

<h6>Menü</h6>
I de obige Auswahllischte stellet Ihr d Datenbank ii.<br>
Alli Aktione beziend sich uf diä da igschtellti Datenbank.

<h6>an Aafang</h6>
Da erfahred Ihr Einiges über Eues System, di verschidene, installierte
Versione und Details über di konfigurierte Datebanke.<br>
Wänn Ihr uf de Datebankname klicked, so gsehnd Ihr en Uflischtig vo de Tabälle
mit de Aazahl vo de Iiträg, vo de Grössi und sletschte Aktualisierigsdatum.

<h6>Konfiguration</h6>
Da chönd Ihr Eueri Konfiguration bearbeite, abspichere oder d Uusgangskonfiguration
wieder mache.
<ul><br>
	<li><a name="conf1"></a><strong>Konfigurierti Datebanke:</strong> d Uuflischtig vo de konfigurierte Datebanke. Di aktivi Datebank wird in <b>bold</b> gelistet. </li>
	<li><a name="conf2"></a><strong>Tabellen-Präfix:</strong> da chönd Ihr (für jedi Datebank) en Präfix aagäh. Das isch en Filter, wo bi Dumps nur diä Tabälle berücksichtigt, wo mit däm Präfix aafanged (z.B. alli Tabälle, wo mit "phpBB_" aafanged). Wänn alli Tabälle vo dere Datebank gspicheret wärden sölled, 
lönd eifach s Fäld läär.</li>
	<li><a name="conf3"></a><strong>GZip-Kompression:</strong> Da cha d Kompression aktiviert wärde. Empfehlenswert isch d Aktivierig, will d Dateie  wesentlich chliner wärdet und Spicherplatz immer rar isch.</li>
	<li><a name="conf5"></a><strong>Email mit Dumpfile:</strong> Isch diä Option aktiviert,  wird nach em abgschlossne Backup en Email mit dem Dump als Aahang verschickt (Vorsicht, Kompression sötti umbedingt aa sii, susch wird dr Aahang zgross und cha evtl. nöd verschickt wärde!).</li>
	<li><a name="conf6"></a><strong>Email-Adrässe:</strong> Empfängeradrässe für d Email.</li>
	<li><a name="conf7"></a><strong>Absänder vo de Email:</strong> diä Adrässe taucht als Absänder i de Email uf.</li>
	<li><a name="conf13"></a><strong>FTP-Transfer: </strong>Isch diä Option aktiviert, wird nach em abgschlossne Backup d Backupdatei per FTP verschickt.</li>
	<li><a name="conf14"></a><strong>FTP Server: </strong>D Adrässe vom FTP-Server (z. B. ftp.mybackups.de).</li>
	<li><a name="conf15"></a><strong>FTP Server Port: </strong>Der Port vom FTP-Server (i de Regle 21).</li>
	<li><a name="conf16"></a><strong>FTP User: </strong>Der Benutzername des FTP-Accounts. </li>
	<li><a name="conf17"></a><strong>FTP Passwort: </strong>Das Passwort des FTP-Accounts. </li>
	<li><a name="conf18"></a><strong>FTP Upload-Ordner: </strong>Das Verzeichnis, wo diä Backupdatei häre söll (es müend Upload-Berechtigunge beschtah!).</li>
	<li><a name="conf8"></a><strong>Automatisches Lösche vo de Backups:</strong> Wänn diä Option aktiviert isch, wärdet älteri Backups nach de folgende Regle automatisch gelöscht.</li>
	<li><a name="conf10"></a><strong>Aazahl vo Backupdateie:</strong> En Wert > 0 löscht alli Backupdateie, bis uf diä da agebni Zahl.</li>
	<li><a name="conf11"></a><strong>Sprach:</strong> Da legsch d Sprach fürs Interface fescht.</li>
</ul>

<h6>Verwaltig</h6>
Da wärdet di eigetliche Aktionen duregfüert.<br>
Es wärdet Dir alli Dateie im Backup-Verzeichnis aazeiget.
Für d Aktione "Restore" und "Delete" mues e Datei selektiert sii.
<UL>
	<li><strong>Restore:</strong> Da demit wird d Datenbank mit de usgwählte Backupdatei aktualisiert.</li>
	<li><strong>Delete:</strong> Da demit chasch Du di selektierti Backupdatei lösche.</li>
	<li><strong>Neues Backup starten:</strong> Da schtartisch es neus Backup (Dump) nach de iigschtellte Parametern i de Konfiguration.</li>
</UL>

<h6>Log</h6>
Da chasch d Logiiträg aaluege und lösche.
<h6>Credits / Hilfe</h6>
diese Seite.
</ul>