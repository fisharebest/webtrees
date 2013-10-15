<div id="content">
<h3>Riguarda questo progetto</h3>
L'idea di questo progetto viene da Daniel Schlichtholz.<p>
Nel 2004 ha aperto il seguente forum <a href="http://forum.mysqldumper.de" target="_blank">MySQLDumper</a>
<br>
in seguito ha incontrato dei programmatori liberi professionisti che elaboravano nuovi script e completavano quelli di Daniel.<br>
In brevissimo tempo nasceva da un piccolo backupscript un considerevolo progetto.<br>

<p>Se hai proposte di miglioramento rivolgiti al MySQLDumper-Forum: <a href="http://forum.mysqldumper.de" target="_blank">http://forum.mysqldumper.de</a>
<p>Ti auguriamo buon divertimento con questo progetto.<br><p><h4>Il Team di MySQLDumper</h4>
<table><tr><td><img src="images/logo.gif" alt="MySQLDumper" 
width="160" height="42" border="1"></td><td valign="top">
Daniel Schlichtholz</td></tr></table>
<br>

<h3> MySQLDumper Aiuto</h3>

<h4>Scarica</h4>
Da questo script ottenete la homepage di MySQLDumper.<br>
Si raccomanda di visitate frequentemente la homepage per ottenere sempre le ultime informazioni, aggiornamenti ed aiuto.<br>
L'indirizzo è <a href="http://forum.mysqldumper.de" target="_blank">http://forum.mysqldumper.de
</a>

<h4>Presupposto del sistema</h4>
Lo script lavora su tutti i server (Windows, Linux,…)<br>
e PHP versione >= 4.3.4 con assistenza GZip, MySQL (>= 3.23), 

Javascript (deve essere attivato).<br> 

<a href="install.php?language=de" target="_top"><h4>Installazione</h4></a>
L'installazione è molto facile. Spacchettate l'archivio in una cartella a vostra scelta e caricatela sul vostro spazio web provider (server).<br>
(per esempio  [rootdir/] MySQLDumper )<br>
…fatto!<br>

Adesso potete aprire MySQLDumper nel vostro Browser “chiamando http://webserver/MySQLDumper„ in seguito potete finire la installazione seguendo gli avvisi di istruzione.<br>
<br><b>Nota:</b><br><i>Se il vostro web server funziona con il safemode=ON, gli script del MySqlDump non possono costruire gli elenchi.<br>
Lo dovete fare manualmente, poichè MySqlDump mette i dati in ordine negli elenchi.<br>
Lo script si ferma automaticamente con una adeguata istruzione.<br>
Quando avete effettuato le indicazioni con le adeguate istruzioni.<br> 
MySqlDump funzionerà normalmente.</i>

<a name="perl"></a><h4> Istruzioni Perlskript </h4>
Molti hanno un elenco con cartella cgi-bin in cui puo essere efettuato Perl. <br>
A maggior parte dei casi questo e raggingibile tramite Browser http://www.domain.de/cgi-bin/. <br>
<br>
In questo caso consigliamo di effettuare i seguenti passi:<br>
<br>
1. Chiama nel MySQLDumper la pagina Backup e clicca "Backup Perl". <br>
2. Copia il percorso, che è scritto nella crondump.pl per $absolute_path_of_configdir: <br>
3. Apri il dato "crondump.pl" in editor <br>
4. Porta il percorso dove c`è absolute_path_of_configdir (senza spazi) <br>
5. Salva crondump.pl <br>
6. Copia crondump.pl, perltest.pl e simpletest.pl nel elenco cgi-bin (nel modo Ascii con FTP) <br>
7. Metti i permessi dei dati in CHMOD 755 <br>
7b. Se preferite cgi come nome del dato, allora cambiate tutti i 3 dati da pl -> cgi (rinominare) <br>
8. Chiama la configurazione nel MySQLDumper <br>
9. Scegli la pagina Cronscript <br>
10. Cambia l`esecuzione del percorso di Perl in /cgi-bin/ <br>
10b. Se gli script hanno .pl, cambiali in .cgi <br>
11. Salva la configurazione. <br>
<br>
Hai finito, i tuoi script si fanno caricare nella tua pagina backup.<br>
<br>
Chi puo usare Perl in tutti gli elenchi deve seguire semplicemente i seguenti passi:<br>
<br>
1. Chiama nel MySQLDumper la pagina Backup. <br>
2. Copia il percorso, che è scritto nella crondump.pl per $absolute_path_of_configdir. <br>
3. Apri il dato "crondump.pl" in editor <br>
4. Porta il percorso dove c`è absolute_path_of_configdir (senza spazi) <br>
5. Salva crondump.pl <br>
6. Metti i permessi dei dati a CHMOD 755 <br>
6b. Se preferite cgi, cambiate tutti i 3 dati da pl -> cgi (rinominare) <br>
(ev. 10b+11 da sopra)<br>
<br>
Se usate Windows dovete cambiare in tutti i dati la prima riga;dove trovate il percorso di Perl. Esempio: <br>
aposto di: #!/usr/bin/perl -w <br>
adesso #!C:\perl\bin\perl.exe -w <br>

<h4>Come si usa</h4><ul>

<h6>Menu</h6>
Nella lista di sopra configurate la banca dati.<br>
Tutte le azioni seguono la configurazione fatta nella banca dati.<br>

<h6>Pagina iniziale</h6>
Qui potete sapere qualcosa sul vostro sistema, installazioni diverseversioni e dettagli sulla configurazione delle banche dati.<br>
Quando selezionate il nome della banca dati vedete una lista delle tabelle specificato in quantità,grandezza e l`ultima data di aggiornamento.<br>

<h6>Configurazione</h6>
Qui potete elaborare, salvare o ripristinare la vostra configurazione.
<ul><br>
	<li><a name="conf1"></a><strong>Banca dati configurata:</strong> la lista delle banche dati configurati. La banca dati attiva viene visualizata in <b>bold</b>. </li>
	<li><a name="conf2"></a><strong>Prefix-tabelle:</strong> qui potete (per ogni banca dati) mettere un prefix.Questo è un filtro che nel Dumps viene considerato solo nelle tabelle con il prefix (esempio: tutte le tabelle che cominciano con "phpBB_" ). Se volete salvare tutta la banca dati lasciate libera questa casella.</li>
	<li><a name="conf3"></a><strong>compressione-GZip:</strong> Qui potete selezionare la compressione. È consigliato selezionare questa opzione, perche cosi i dati vengono ristretti per risparmiare spazio sul disco rigido .</li>
	<li><a name="conf5"></a><strong>Email con Dumpfile:</strong> Se avete selezionato questa opzione viene spedita una e-mail con allegato dopo il backup(attenzione, la compressione in questo caso e consigliata, altrimenti la e-mail non potrebbe essere spedita!)</li>
	<li><a name="conf6"></a><strong>Indrizzo-e-mail:</strong> Mittente della e-mail</li>
	<li><a name="conf7"></a><strong>Destinatario della e-mail:</strong> questo indrizzo e visibile nella e-mail come destinatario</li>
	<li><a name="conf13"></a><strong>FTP-Transfer: </strong>Se è selezionata questa opzione, al termine del backup viene spedito il dato tramite FTP.</li>
	<li><a name="conf14"></a><strong>FTP Server: </strong>l`indirizzo del server-FTP (esempio: ftp.mybackups.it)</li>
	<li><a name="conf15"></a><strong>FTP Server Port: </strong>la porta del FTP-server (in regola 21)</li>
	<li><a name="conf16"></a><strong>FTP User: </strong>nome del conto-FTP in uso  </li>
	<li><a name="conf17"></a><strong>FTP parola d`ordine (password): </strong>La parola d`ordine del conto FTP </li>
	<li><a name="conf18"></a><strong>FTP cartella - upload: </strong>l`elenco in cui viene spedito il dato del backup (dovete avere il permesso per scaricare (upload)!)</li>
	<li><a name="conf8"></a><strong>Cancellare automaticamente i backups:</strong> 
Quando è selezionata questa opzione vengono cancellati automaticamente i backups piu vecchi..</li>
	<li><a name="conf10"></a><strong>Quantità dei dati backup:</strong> Un valore  > 0 cancella tutti i backup, fino al valore selezionato</li>
	<li><a name="conf11"></a><strong>Lingua:</strong> qui scegli la lingua per l`interfaccia.</li>
</ul>

<h6>Amministrazione</h6>
qui vengono efettuate le vostre azioni.<br>
Ti vengono mostrati tutti i dati nell` elenco backup. Per le azioni "Restore" e "Delete" deve essere selezionato un dato.<br>
<UL>
	<li><strong>Restore:</strong> la banca dati viene aggiornata con il backup scelto.</strong></li>
	<li><strong>Delete:</strong> cancellare il backup selezionato.</strong></li>
	<li><strong>Partenza nuovo backup:</strong> qui fai partire un nuovo backup (Dump) secondo</strong> </li>
         <li>i parametri della tua configurazione.</li>
</UL>

<h6>Log</h6>
Qui puoi vedere e cancellare i log (connessioni effettuati).<br>

<h6>Credito / Auito</h6>
questa pagina