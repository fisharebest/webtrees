<?php
$lang['L_DUMP_HEADLINE']="Crea backup...";
$lang['L_GZIP_COMPRESSION']="Compressione-GZip";
$lang['L_SAVING_TABLE']="Salva tabella ";
$lang['L_OF']="da";
$lang['L_ACTUAL_TABLE']="Tabella attuale";
$lang['L_PROGRESS_TABLE']="Processo della tabella";
$lang['L_PROGRESS_OVER_ALL']="Progresso totale";
$lang['L_ENTRY']="Registrazione";
$lang['L_DONE']="Completato!";
$lang['L_DUMP_SUCCESSFUL']="è stato creato con successo.";
$lang['L_UPTO']="fino a";
$lang['L_EMAIL_WAS_SEND']="L`e-mail è stata spedita con successo a ";
$lang['L_BACK_TO_CONTROL']="continua";
$lang['L_BACK_TO_OVERVIEW']="Riassunto database";
$lang['L_DUMP_FILENAME']="File di backup: ";
$lang['L_WITHPRAEFIX']="con prefisso";
$lang['L_DUMP_NOTABLES']="Impossibile trovare tabelle `<b>%s</b>` nel database.";
$lang['L_DUMP_ENDERGEBNIS']="Sono state salvate <b>%s</b> tabelle con <b>%s</b> record.<br>";
$lang['L_MAILERROR']="Spiacente, nell`inviare l`e-mail si è verificato un errore!";
$lang['L_EMAILBODY_ATTACH']="Nell`allegato trovi il backup del tuo database MySQL.<br>Backup del database `%s`
<br><br>Il seguente file è stato creato:<br><br>%s <br><br>Buona giornata<br><br>MySQLDumper<br>";
$lang['L_EMAILBODY_MP_NOATTACH']="È stato creato un backup multipart.<br>Il backup non viene spedito come allegato!<br>Backup del database `%s`
<br><br>I seguenti file sono stati creati:<br><br>%s<br><br><br>Buona giornata<br><br>MySQLDumper<br>";
$lang['L_EMAILBODY_MP_ATTACH']="È stato creato un backup multipart.<br>Il backup viene spedito con e-mail separate, con allegati!<br>Backup del database`%s`
<br><br>I seguenti file sono stati creati:<br><br>%s<br><br><br>Buona giornata<br><br>MySQLDumper<br>";
$lang['L_EMAILBODY_FOOTER']="`<br><br>Buona giornata<br><br>MySQLDumper<br>";
$lang['L_EMAILBODY_TOOBIG']="Il backup supera la grandezza massima di %s perciò i file non sono stati allegati.<br>Backup del database `%s`
<br><br>I seguenti file sono stati creati:<br><br>%s
<br><br>Buona giornata<br><br>MySQLDumper<br>";
$lang['L_EMAILBODY_NOATTACH']="È stato creato un backup.<br>Il backup non viene spedito come allegato!<br>Backup del database `%s`
<br><br>I seguenti file sono stati creati:<br><br>%s
<br><br>Buona giornata<br><br>MySQLDumper<br>";
$lang['L_EMAIL_ONLY_ATTACHMENT']="Allegati del backup";
$lang['L_TABLESELECTION']="Seleziona tabelle";
$lang['L_SELECTALL']="seleziona tutto";
$lang['L_DESELECTALL']="selezionare tutto";
$lang['L_STARTDUMP']="Fai partire il backup";
$lang['L_LASTBUFROM']="ultimo update dal";
$lang['L_NOT_SUPPORTED']="Questo backup non supporta questa funzione.";
$lang['L_MULTIDUMP']="Multidump: Sono stati salvati <b>%d</b> database.";
$lang['L_FILESENDFTP']="Invio del file via FTP in corso... un attimo di pazienza prego. ";
$lang['L_FTPCONNERROR']="Connessione FTP non riuscita! Connessione con ";
$lang['L_FTPCONNERROR1']="come utente ";
$lang['L_FTPCONNERROR2']="non possibile";
$lang['L_FTPCONNERROR3']="FTP-Upload errato! ";
$lang['L_FTPCONNECTED1']="Connesso con ";
$lang['L_FTPCONNECTED2']="sul ";
$lang['L_FTPCONNECTED3']="trasferimento completato con successo";
$lang['L_NR_TABLES_SELECTED']="- con %s tabelle selezionate";
$lang['L_NR_TABLES_OPTIMIZED']="<span class=\"small\">%s tabelle sono state ottimizzate.</span>";
$lang['L_DUMP_ERRORS']="<p class=\"error\">%s errori riscontrati: <a href=\"log.php?r=3\">controllare gli errori</a></p>";
$lang['L_FATAL_ERROR_DUMP']="Errore fatale: l'istruzione di creazione della tabella '%s' nel database '%s' non è leggibile! <br> Controlla se ci sono dei errori nella tabella.";


?>