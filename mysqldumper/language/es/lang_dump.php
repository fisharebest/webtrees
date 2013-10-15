<?php
$lang['L_DUMP_HEADLINE']="Creando copia de seguridad...";
$lang['L_GZIP_COMPRESSION']="La compresión GZip";
$lang['L_SAVING_TABLE']="Guardando tabla ";
$lang['L_OF']="de";
$lang['L_ACTUAL_TABLE']="Tabla actual";
$lang['L_PROGRESS_TABLE']="Progreso de la tabla actual";
$lang['L_PROGRESS_OVER_ALL']="Progreso total";
$lang['L_ENTRY']="Registro";
$lang['L_DONE']="Finalizado!";
$lang['L_DUMP_SUCCESSFUL']=" ha sido realizado con éxito.";
$lang['L_UPTO']="hasta";
$lang['L_EMAIL_WAS_SEND']="Se ha enviado un email a ";
$lang['L_BACK_TO_CONTROL']="seguir";
$lang['L_BACK_TO_OVERVIEW']="vista de base de datos";
$lang['L_DUMP_FILENAME']="Archivo de backup: ";
$lang['L_WITHPRAEFIX']="con prefijo";
$lang['L_DUMP_NOTABLES']="No se han encontrado tablas en la base de datos `<b>%s</b>` ";
$lang['L_DUMP_ENDERGEBNIS']="<b>%s</b> Tablas con un total de <b>%s</b> registros, han sido guardadas con éxito.<br>";
$lang['L_MAILERROR']="Se ha producido un error al intentar enviar el email!";
$lang['L_EMAILBODY_ATTACH']="En el fichero adjunto encontrará la copia de seguridad de su base de datos MySQL.<br>Copia de seguridad de la base de datos `%s`
<br><br>Se ha creado el siguiente archivo:<br><br>%s <br><br><br>Saludos de<br><br>MySQLDumper<br>";
$lang['L_EMAILBODY_MP_NOATTACH']="Se ha realizado un backup de archivos múltiples.<br>Los archivos no se adjuntan a este email!<br>Copia de seguridad de la base de datos `%s`
<br><br>Los siguientes archivos han sido adjuntados:<br><br>%s
<br><br><br>Saludos de<br><br>MySQLDumper<br>";
$lang['L_EMAILBODY_MP_ATTACH']="Se ha realizado un backup de archivos múltiples.<br>Los archivos se adjuntan a emails separados!<br>Copia de seguridad de la base de datos `%s`
<br><br>Los siguientes archivos han sido adjuntados:<br><br>%s <br><br><br>Saludos de<br><br>MySQLDumper<br>";
$lang['L_EMAILBODY_FOOTER']="<br><br><br>Saludos de<br><br>MySQLDumper<br>";
$lang['L_EMAILBODY_TOOBIG']="La copia de seguridad ha sobrepasado el tamaño máximo de %s y por lo tanto no ha sido adjuntada.<br>Copia de seguridad de la base de datos `%s`
<br><br>Se ha creado el siguiente archivo:<br><br>%s <br><br><br>Saludos de<br><br>MySQLDumper<br>";
$lang['L_EMAILBODY_NOATTACH']="No se adjunta el archivo de copia de seguridad.<br>Copia de seguridad de la base de datos `%s`
<br><br>Se ha creado el siguiente archivo:<br><br>%s <br><br><br>Saludos de<br><br>MySQLDumper<br>";
$lang['L_EMAIL_ONLY_ATTACHMENT']=" ... solamente el fichero adjunto";
$lang['L_TABLESELECTION']="Elección de tablas";
$lang['L_SELECTALL']="seleccionar todas
";
$lang['L_DESELECTALL']="seleccionar todas";
$lang['L_STARTDUMP']="iniciar copia de seguridad";
$lang['L_LASTBUFROM']="última actualización el";
$lang['L_NOT_SUPPORTED']="Esta copia de seguridad no comprende esta función.";
$lang['L_MULTIDUMP']="Copia múltiple, copia de seguridad de <b>%d</b> bases de datos resalizada.";
$lang['L_FILESENDFTP']="envío del archivo vía FTP... tenga un poco de paciencia, por favor. ";
$lang['L_FTPCONNERROR']="Conexión no establecida! Conectarse a ";
$lang['L_FTPCONNERROR1']=" con el usuario ";
$lang['L_FTPCONNERROR2']=" ha sido imposible";
$lang['L_FTPCONNERROR3']="El envío por FTP ha fallado! ";
$lang['L_FTPCONNECTED1']="Conectado con ";
$lang['L_FTPCONNECTED2']=" en ";
$lang['L_FTPCONNECTED3']=" escritos";
$lang['L_NR_TABLES_SELECTED']="- con %s tablas seleccionadas";
$lang['L_NR_TABLES_OPTIMIZED']="<span class=\"small\">%s tablas optimizadas.</span>";
$lang['L_DUMP_ERRORS']="<p class=\"error\">Ha(n) ocurrido %s error(es): <a href=\"log.php?r=3\">visualizar</a></p>";
$lang['L_FATAL_ERROR_DUMP']="¡Error fatal: las instrucciones para crear la tabla '%s' en la base de datos '%s' no se pueden leer!";


?>