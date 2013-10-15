<div id="content">
<h3>Om detta projekt</h3>
Daniel Schlichtholz hade iden för detta projekt.<p>2004 startade han forumet <a href="http://forum.mysqldumper.de" target="_blank">MySQLDumper</a> och väldigt snabbt infann sig ett antal hobby-programmerare som skrev nya skripter och anpassade de skripter som Daniel hade skrivit.<br>På kortaste tid uppstod ett omfångsrikt projekt.<p>Välkommen att yttra dina förbättringsförslag i MySQLDumper-forumet <a href="http://forum.mysqldumper.de" target="_blank">http://forum.mysqldumper.de</a>.<p>Vi önskar mycket nytta och nöje med detta projekt.<br><p><h4>MySQLDumper-teamet</h4>

<table><tr><td><img src="images/logo.gif" alt="MySQLDumper" border="0"></td><td valign="top">
Daniel Schlichtholz</td></tr></table>
<br>

<h3>MySQLDumper hjälp</h3>

<h4>Download</h4>
Detta skript kan laddas hem från MySQLDumper-projektets hemsida.<br>
Vi rekommenderar att du besöker sajten regelbundet för hjälp och uppdateringar.<br>
Adressen lyder: <a href="http://www.mysqldumper.de" target="_blank">
http://www.mysqldumper.de</a>

<h4>Systemförutsättningar</h4>
Skriptet fungerar på alla servrar (Windows, Linux, ...) <br>
med PHP >= version 4.3.4 med stöd för GZip, MySQL (från version 3.23), JavaScript (måste aktiveras).

<a href="install.php?language=de" target="_top"><h4>Installation</h4></a>
Installationen är mycket enkel.
Packa upp arkivet till en valfri mapp.<br>
Ladda upp alla filerna till din webbserver (t.ex. till serverns högsta nivå [server mapp/]MySQLDumper)<br>
... färdigt!<br>
Du kan nu starta MySQLDumper i webbläsaren med "http://mein-webserver/MySQLDumper"<br>
för att avsluta installationen. Följ helt enkelt instruktionerna.<br>
<br><b>Hänvisning:</b><br><i>Om servern är inställd på PHP-Safemode så får skriptet ej skapa mappar på servern.<br>
Detta måste du då göra manuellt eftersom MySqlDump sparar data i mappar.<br> 
Skriptet stoppas och motsvarande anvisning visas!<br>
Efter det att du har skapat mapparna (se anvisningen) kan du fortsätta helt normalt och utan inskränkningar.</i>

<a name="perl"></a><h4>Instruktion till perlskriptet</h4>
De flesta har en cgi-bin mapp i vilken CGI-skript kan utföras.<br>
Denna mapp kan nås med webbläsaren via http://www.domain.se/cgi-bin/ . <br>
<br>
Om detta är fallet så bör följande steg genomföras:<br><br>

1. Starta sidan Backup i MySQLDumper and click "Backup Perl". <br>
2. Kopiera sökvägen som visas efter crondump.pl för $absolute_path_of_configdir: . <br>
3. Öppna filen "crondump.pl" i Anteckningar.<br>
4. Kopiera in skriptsökvägen vid absolute_path_of_configdir (utan mellanslag).<br>
5. Spara crondump.pl .<br>
6. Kopiera över crondump.pl samt perltest.pl och simpletest.pl till cgi-bin-mappen (använd ASCII-läge i FTP-programmet).<br>
7. Ställ in rättigheten 755 (CHMOD). <br>
7b. Om filändelsen cgi önskas ändrar du ändelsen pl -> cgi hos alla tre filerna. <br>
8. Starta konfigureringen i MySQLDumper.<br>
9. Välj sidan Cronscript. <br>
10. Ändra Perl-skriptsökvägen till /cgi-bin/ .<br>
10b. Om skripterna har ändelsen .pl ändrar du dem till .cgi .<br>
11. Spara konfigureringen. <br><br>

Färdigt! Skripterna kan nu startas från sidan Backupertig.<br><br>

Om du kan utföra Perl-skript i alla mappar så räcker följande ändringar:<br><br>

1. Starta sidan Backup i MySQLDumper. <br>
2. Kopiera sökvägen som visas efter crondump.pl för $absolute_path_of_configdir: . <br>
3. Öppna filen "crondump.pl" i Anteckningar. <br>
4. Kopiera in skriptsökvägen vid absolute_path_of_configdir (utan mellanslag). <br>
5. Spara crondump.pl .<br>
6. Ställ in rättigheten 755 (CHMOD). <br>
6b. Om filändelsen cgi önskas ändrar du ändelsen pl -> cgi hos alla tre filerna. <br>
(ev. stegen 10b + 11 längre upp)<br>
<br>

Windows-användare måste ändra den första raden i alla skripter, där står sökvägen för Perl. Exempel: <br>
hitta: #!/usr/bin/perl -w <br>
ändra till: #!C:\perl\bin\perl.exe -w <br>

<h4>Användning</h4><ul>

<h6>Meny</h6>
I ovan nämnda lista ställer du in databasen.<br>
Alla aktioner är beroende av inställningarna som görs här.

<h6>Startsidan</h6>
Här visas diverse information om ditt system, de olika installerade versionerna och detaljer om de konfigurerade datbaserna.<br>
Om du klickar på databasens namn så visas lista över tabellerna med antalet poster, storleken och datumet för senaste aktualisering.

<h6>Konfigurering</h6>
Här kan du redigera, spara eller återställa din konfigurering.
<ul><br>
	<li><a name="conf1"></a><strong>Konfigurerade databaser:</strong> en lista över konfigurerade databaser. Den aktiva databasen visas med <b>fet</b> text. </li>
	<li><a name="conf2"></a><strong>Tabell-prefix:</strong> här kan du ange ett prefix (för varje databas). Detta är ett filter som filtrar ut tabeller som börjar med detta prefix (t.ex. alla tabeller som börjar med "phpBB_") när en dump görs. Lämna fältet tomt om alla tabeller skall sparas.</li>
	<li><a name="conf3"></a><strong>GZip-komprimering:</strong> här kan du aktivera komprimeringen. Vi rekommenderar att komprimering aktiveras eftersom filerna blir mycket mindre och platsen på hårddisken är dyr.</li>
	<li><a name="conf5"></a><strong>Email med dump-filen:</strong> om denna option aktiveras så sänds den färdiga dumpen som email-attachment (VARNING: aktivera ovillkorligen kompression, annars kan attachmentet blir för stort och kan eventuellt ej skickas!).</li>
	<li><a name="conf6"></a><strong>Email-adress:</strong> mottagaradressen för dessa email.</li>
	<li><a name="conf7"></a><strong>Emailets avsändare:</strong> denna adress används som avsändaradress.</li>
	<li><a name="conf13"></a><strong>FTP-överföring:</strong> om denna option aktiveras så överförs den färdiga dumpen via FTP.</li>
	<li><a name="conf14"></a><strong>FTP-server:</strong> FTP-serverns adress (t.ex. ftp.mindomain.se).</li>
	<li><a name="conf15"></a><strong>FTP-serverns port:</strong> FTP-serverns port (normalt sett 21).</li>
	<li><a name="conf16"></a><strong>FTP-användare:</strong> användarnamnet för FTP-kontot.</li>
	<li><a name="conf17"></a><strong>FTP-lösenord:</strong> lösenordet för FTP-kontot.</li>
	<li><a name="conf18"></a><strong>FTP upload-mapp:</strong> mappen i vilken backup-filen skall sparas (servern måste tillåtas skriva till denna mapp!).</li>
	<li><a name="conf8"></a><strong>Automatisk radering av backup:</strong> om denna option aktiveras så raderas äldre backup-filer automatiskt enligt följande regler.</li>
	<li><a name="conf10"></a><strong>Antal backup-filer:</strong> ett värde > 0 raderar alla filer utöver antalet i detta värde.</li>
	<li><a name="conf11"></a><strong>Spåk:</strong> här ställer du in språket för gränssnittet.</li>
</ul>

<h6>Förvaltning</h6>
Här utförs de egentliga aktionerna.<br>
Alla filerna i backup-mappen visas.
Du måste välja/markera en fil för att kunna utföra aktionerna "Återställ" och "Radera".
<UL>
	<li><strong>Återställ:</strong> med denna funktion aktualiseras databasen med vald backup-fil.</li>
	<li><strong>Radera:</strong> med denna funktion kan du radera vald backup-fil.</li>
	<li><strong>Starta ny backup:</strong> med denna funktion startar du en ny backup (dump) i enlighet med de parametrar som ställts in i konfigureringen.</li>
</UL>

<h6>Logg</h6>
Här kan du se och radera posterna i loggen.
<h6>Credits / Hjälp</h6>
denna sida.
</ul>