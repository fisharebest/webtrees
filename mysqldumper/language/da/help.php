<div id="content">
<h3>Om dette projekt</h3>
Idéen til dette projekt kommer fra Daniel Schlichtholz.<p>I 2004 skabte han et forum kaldet <a href="http://forum.mysqldumper.de" target="_blank">MySQLDumper</a> og programmører af andre programmer supplementerede Daniel's scripts.<br>I løbet af kort tid blomstrede det lille backup-script til et langt større projekt.<p>Hvis du har forslag til forbedringer kan du besøge MySQLDumper-Forum: <a href="http://forum.mysqldumper.de" target="_blank">http://forum.mysqldumper.de</a>.<p>God fornøjelse med at bruge systemet.<br><br><h4>MySQLDumper-Teamet</h4>
<table><tr><td><img src="images/logo.gif" alt="MySQLDumper" border="0"></td><td valign="top">
Daniel Schlichtholz<br>
Dansk oversættelse - AlleyKat
</td></tr></table>

<h3>MySQLDumper Hjælp</h3>

<h4>Download</h4>
Dette Script er tilgængeligt på MySQLDumper's hjemmeside.<br>
Det anbefales at du regelmæssigt besøger siden for seneste information, opdateringer og hjælp.<br>
Adressen er <a href="http://forum.mysqldumper.de" target="_blank">
http://forum.mysqldumper.de
</a>

<h4>Systemkrav</h4>
Scriptet virker med stort set alle typer servere (Windows, Linux, ...) <br>
og PHP >= Version 4.3.4 med GZip-Library, MySQL (>= 3.23), JavaScript (skal være slået til).

<a href="install.php?language=de" target="_top"><h4>Installation</h4></a>
Installationen er meget nem.
Udpak arkivet i enhver folder der er tilgængelig fra webserveren<br>
(f.eks. i rod-folderen [Server rootdir/]MySQLDumper)<br>
ændr config.php til chmod 777<br>
... færdig!<br>
Du kan starte MySQLDumper i din browser ved at skrive "http://domain.tld/MySQLDumper"
for at færdiggøre opsætningen, følg blot instruktionerne.

<br><b>Bemærk:</b><br><i>Hvis din webserver kører med indstillingen safemode=ON må MySqlDump ikke oprette foldere.<br>
Du vil derfor være nødt til at gøre det selv.<br>
Hvis det er tilfældet afbryder MySqlDump og fortæller dig hvad der skal gøres.<br>
Efter oprettelse af folderne vil MySqlDump fungere normalt</i><br>

<a name="perl"></a><h4>Hjælp med Perl scriptet</h4>

De fleste har en cgi-bin folder, hvorfra Perl kan udføres. <br>
Dette er normalt tilgængeligt via f.eks. http://www.domain.tld/cgi-bin/ <br><br>

Hvis dette er tilfældet, følg venligst disse trin.  <br><br>

1.  Åbn i MySQLDumper sidene Backup og klik "Backup Perl"   <br>
2.  Kopiér stien der står efter entry i crondump.pl for $absolute_path_of_configdir:    <br>
3. Åbn filen "crondump.pl" i editoren <br>
4. indsæt den kopierede sti dér i absolute_path_of_configdir (ingen mellemrum) <br>
5.  Gem crondump.pl <br>
6. Kopiér crondump.pl, såvel som perltest.pl og simpletest.pl til cgi-bin folderen (ASCII-modus i ftp-klient!) <br>
7. CHMOD scriptene til 755.  <br>
7b. Hvis filtypen cgi foretrækkes omdøbes alle 3 filer pl - > cgi (rename)  <br>
8.  Åbn i MySQLDumper siden Konfiguration<br>
9. Klik på Cronscript <br>
10. Ændr Perl udførelsessti til /cgi-bin/<br>
10b. Hvis Scriptene er omdøbt til *.cgi , ændr Filtype til cgi <br>
11 Gem Konfigurationen <br><br>

Klar! Scriptene er tilgængelige fra siden "Backup" <br><br>

Hvis du kan udføre Perl overalt, behøves kun følgende trin:  <br><br>

1.  Åbn i MySQLDumper siden Backup.  <br>
2.  Kopiér stien der står efter entry i crondump.pl for $absolute_path_of_configdir:    <br>
3. Åbn filen "crondump.pl" i editoren <br>
4. indsæt den kopierede sti dér i absolute_path_of_configdir (ingen mellemrum) <br>
5.  Gem crondump.pl <br>
6. CHMOD scriptene til 755.  <br> 
6b. Hvis filtypen cgi foretrækkes omdøbes alle 3 filer pl - > cgi (rename)  <br>
(evt. 10b+11 fra herover) <br><br>


Windowsbrugere skal ændre første linie i alle Perlscripts til stien til Perl.  <br><br>

Eksempel:  <br>

i stedet for:  #!/usr/bin/perl w <br>
bruges: #!C:\perl\bin\perl.exe w<br>

<h4>Brug</h4><ul>

<h6>Menu</h6>
I den øverste valgboks vælges din database.<br>
Alle handlinger refererer til denne database.

<h6>Hjem</h6>
Her ser du information om dit system, versionsnumre og detaljer om de konfigurerede databaser.<br>
Hvis du klikker på en database i tabellen, får du en liste over tabeller med posttællere, størrelse og senest opdateret-stempel.

<h6>Konfiguration</h6>
Her kan du redigere din konfiguration, gemme den eller indlæse standardindstillingerne.
<ul>
	<li><a name="conf1"><strong>Konfigurerede Databaser:</strong> liste over konfigurerede databaser. Den aktive database er markeret fremhøvet.</li>
	<li><a name="conf2"><strong>Tabel-Præfiks:</strong> du kan vælge et præfiks for hver database for sig. Præfikset er et filter, der kun håndterer de tabeller i et dump, som starter med dette præfiks (f.eks. alle tabeller startende med "phpBB_"). Hvis du ikke ønsker at bruge det, lad feltet være tomt.</li>
	<li><a name="conf3"><strong>GZip-komprimering:</strong> Her kan du aktivere komprimering. Det anbefales at bruge komprimering grundet den mindre filstørrelse som dermed sluger mindre diskplads.</li>
	<li><a name="conf19"></a><strong>Antal poster til backup:</strong> Dette er antallet af poster der læses samtidigt under backup, før scriptet kaldes igen. For langsommere servere kan du reducere dette parameter for at forhindre timeouts.</li>
	<li><a name="conf20"></a><strong>Antal poster til genetablering:</strong> Dette er antallet af poster der læses samtidigt under backup, før scriptet kaldes igen. For langsommere servere kan du reducere dette parameter for at forhindre timeouts.</li>
	<li><a name="conf4"></a><strong>Folder for Backupfiler:</strong> vælg din folder til backupfilerne. Hvis du vælger en ny, opretter scriptet den for dig. Du kan bruge relative eller absolutte stier.</li>
	<li><a name="conf5"></a><strong>Send dumpfil som email:</strong> Når denne indstilling er slået til, vil scriptet automatisk sende den færdige backupfil som en email med vedhæftet fil (Forsigtig!, du bør bruge komprimering med denne indstilling da dumpfilen kan være for stor til email!)</li>
	<li><a name="conf6"></a><strong>Email-adresse:</strong> Modtagers emailadresse</li>
	<li><a name="conf7"></a><strong>Email emne:</strong> Emnet på emailen</li>
	<li><a name="conf13"></a><strong>FTP-overførsel: </strong>Når denne indstilling er slået til sender scriptet automatisk den færdige backupfil via FTP.</li>
	<li><a name="conf14"><strong>FTP-server: </strong>Adresse på FTP-serveren (e.g. ftp.mybackups.de)</li>
	<li><a name="conf15"></a><strong>FTP-server port: </strong>port til FTP-serveren (normalt 21)</li>
	<li><a name="conf16"></a><strong>FTP-bruger: </strong>brugernavnet til FTP-kontoent</li>
	<li><a name="conf17"></a><strong>FTP-kodeord: </strong>kodeordet til FTP-kontoen</li>
	<li><a name="conf18"></a><strong>FTP Upload-folder: </strong>folderen hvori backupfilen gemmes (der skal være skrive-rettigheder til folderen!)</li>
	
	<li><a name="conf8"></a><strong>Automatisk sletning af backups:</strong> Når du aktiverer denne indstilling, slettes backupfiler automatisk efter følgende regler.</li>
	<li><a name="conf10"></a><strong>Slet ud fra antal filer:</strong> En værdi > 0 sletter alle filer pånær det givne antal</li>
	<li><a name="conf11"></a><strong>Sprog:</strong> vælg dit sprog til brugerfladen.</li>
</ul>

<h6>Administration</h6>
Alle handlinger listes op hér.<br>
Du kan se alle filer i backupfolderen.
For handlingerne "Genetabler" og "Slet" skal du vælge en fil først.
<UL>
	<li><strong>Genetabler:</strong> du genetablerer databasen med posterne fra den valgte backupfil.</li>
	<li><strong>Slet:</strong> du kan slette den valgte backupfil.</li>
	<li><strong>Start nyt Dump:</strong> her starter du en ny backup (dump) med dine konfigurerede parametre.</li>
</UL>

<h6>Log</h6>
Du kan læse Log-indlæg og slette dem.

<h6>Bidragsydere / Hjælp</h6>
Denne side.
</ul>