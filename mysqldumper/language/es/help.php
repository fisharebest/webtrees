<div id="content">
<h3>Sobre este proyecto </h3>
La idea para este proyecto proviene de Daniel Schlichholz.
<p>En 2004 abrió el foro <a href="http://forum.mysqldumper.de" target="_blank">MySQLDumper</a> e inmediatamente algunos aficionados a la programación se encontraron allí, para escribir y ampliar la versión inicial de los scripts de Daniel.<br>
En poco tiempo quedó establecido el proyecto de realizar un script de copia de seguridad.
<p>Si tiene Vd. alguna propuesta de mejora, notifíquela en el foro de MySQLDumper-Forum: <a href="http://forum.mysqldumper.de" target="_blank">http://forum.mysqldumper.de</a>.
<p>Le deseamos que disfrute del trabajo realizado en este proyecto.<br>
<p>
<h4>El equipo de MySQLDumper</h4>

<table><tr><td><img src="images/logo.gif" alt="MySQLDumper" border="0"></td><td valign="top">
Daniel Schlichtholz <br>
Traducción al Español: <a href="http://www.boutiquedeliberico.es" target="_blank">Javier Herrera Sanjuán</a> 
</td></tr></table>
<br>

<h3>Ayuda de MySQLDumper</h3>


<h4>Descarga</h4>
Este script puede descargarlo desde la página principal de MySQLDumper.<br>
Se recomienda que visite dicha página regularmente, para mantener el producto actualizado y poder descargar las ampliaciones que se vayan realizando.<br>
La dirección es: <a href="http://forum.mysqldumper.de" target="_blank">
http://forum.mysqldumper.de
</a>

<h4>Requisitos del sistema </h4>
<p>El script funciona en cualquier servidor (Windows, Linux, ...) que soporte una versión de PHP superior a la 4.3.4 
(con GZip instalado) <br>
y con MySQL a partir de la versión 3.23</p>
<p>Además, debe tener activado el ejecutar secuencias de comandos  JavaScript en su navegador.</p>
<a href="install.php?language=de" target="_top">
<h4>Instalación</h4>
</a>
La instalación es muy sencilla. Simplemente extraiga los ficheros (conservando la estructura de directorios) en una carpeta cualquiera.<br>
Suba (por FTP u otros medios) dichos ficheros a su servidor web. (por ejemplo, [directorio web de su servidor]/MySQLDumper)<br>
... listo!<br>
Ahora puede ejecutar MySQLDumper desde su navegador llamándolo desde la dirección "http://www.su_servidor.com/MySQLDumper", para iniciar el proceso de configuración, para el que simplemente debe seguir las instrucciones en pantalla.<br>
<br><b>Aviso:</b><br>
<i>En caso de que su servidor tenga activado el modo seguro de PHP, el script no podrá crear los directorios que necesita.<br>
Si es así, deberá hacerlo Vd. de forma manual, según las instrucciones del proceso de configuración.<br>
Tras haber creado dichos directorios, todo funcionará normalmente y sin restricciones.</i>

<a name="perl"></a>
<h4>Instrucciones para la instalación del script Perl</h4>
<p>En la mayoría de los casos, el servidor dispondrá de un directorio llamado cgi-bin (o perl) desde el que pueden ejecutarse scripts perl. <br>
  Dichos scripts se podrán acceder desde su navegador mediante la dirección http://www.su_servidor.com/cgi-bin/ . <br>
  <br>
Si este es su caso, siga las instrucciones siguientes:<br>
<br>
1. Ejecute MySQLDumper desde su navegador, y vaya a la página de and click "Backup Perl"<br>
2. Copie el camino que aparece en la sección de propiedades del Cronscript Perl, al lado de $absolute_path_of_configdir: . <br>
3. Abra el fichero "crondump.pl" en un editor cualquiera de texto.<br>
4. Pegue el camino copiado en la entrada absolute_path_of_configdir (sin espacios vacíos). Hay una muestra dos líneas por encima. <br>
5. Guarde "crondump.pl".<br>
6. Copie los ficheros "crondump.pl", así como "perltest.pl" y "simpletest.pl" en el directorio cgi-bin (hágalo en modo ASCII si usa FTP).<br>
7. Dé a dichos ficheros los derechos 0x755 (use chmod desde shell o desde su programa de FTP).<br>
7b. En caso de ser neesaria la extensión "cgi" para los scripts, cámbiela en los tres ficheros de "pl" a "cgi" (cambiar nombre).<br>
8. Vaya a la página de
<?php echo $lang['config']?> 
en MySQLDumper.<br>
9. Elija el apartado Cronscript.<br>
10. Cambie el Camino al Cronscript a "/cgi-bin/" (sin comillas). <br>
10b. Si ha renombrado los scripts a ".cgi", cambie la extensión de los scripts a ".cgi".<br>
11. Guarde la configuración.<br>
<br>
Ya ha terminado. Ahora puede llamar los scripts desde la página de
<?php echo $lang['config']?>
. Le recomendamos que pruebe primero tanto Perl como los Módulos Perl, usando los botones apropiados para ello. Si no funciona alguno de los dos, es probable que no pueda utilizar su script.<br>
<br>
Aquellos usuarios que pueden ejecutar Perl en cualquier directorio, pueden alternativamente, seguir los pasos siguientes (más sencillos):<br>
<br>
1. Ejecute MySQLDumper desde su navegador, y vaya a la página de
<?php echo $lang['config']?>
<br>
2. Copie el camino que aparece en la sección de propiedades del Cronscript Perl, al lado de $absolute_path_of_configdir: . <br>
3. Abra el fichero "crondump.pl" en un editor cualquiera de texto.<br>
4. Pegue el camino copiado en la entrada absolute_path_of_configdir (sin espacios vacíos). Hay una muestra dos líneas por encima. <br>
5. Guarde "crondump.pl" (si lo ha editado en local, súbalo nuevamente al servidor).<br>
6. Dé a los ficheros "crondump.pl", así como "perltest.pl" y "simpletest.pl", los derechos 0x755 (use chmod desde shell o desde su programa de FTP).<br>
7. En caso de ser neesaria la extensión "cgi" para los scripts, cámbiela en los tres ficheros de "pl" a "cgi" (cambiar nombre).<br>
8. Si ha renombrado los scripts a ".cgi", vaya a la página de
<?php echo $lang['config']?>
en MySQLDumper, elija el apartado Cronscript y cambie la extensión de los scripts a ".cgi". Guarde la configuración.<br>
  <br>

  <b>Nota:</b> Tanto los usuarios de Windows como los usuarios de servidores con configuraciones no estándar, deberán cambiar en los tres scripts la primera línea para reflejar el camino correcto de Perl. Por ejemplo: <br>
en vez de: #!/usr/bin/perl -w <br>
ponga: #!C:\perl\bin\perl.exe -w <br>
</p>
<h4>Instrucciones de uso</h4>

<h6>Menú</h6>
<p>En el desplegable superior se encuentra la lista de bases de datos disponibles para trabajar con ellas. Tenga en cuenta que no necesariamente tendrá permiso para trabajar con todas ellas, sólo aquellas en las que su usuario tenga permisos podrán ser realmente accedidas. Las demás le darán simplemente un error.<br />
Todas las acciones se refieren siempre a la base de datos seleccionada en este desplegable.</p>

<ul>
	<h6><?php echo $lang['home']?></h6>
	<p>Aquí encontrará algunas propiedades de su sistema, como las versiones instaladas y algunos detalles de la base de datos.<br />
    Los botones superiores le permitirán acceder a las diferentes opciones, que tendrán más o menos sentido según el nivel de privilegios de su usuario de base de datos:</p>
	<ul><li><b><?php echo $lang['Statusinformationen']?></b> le mostrará las informaciones genéricas, pudiendo además acceder a algunas de ellas en particular, para ampliarlas.</li>
	  <li><b><?php echo $lang['dbs']?></b> le llevará a la lista de las mismas, pudiendo crear otras nuevas. Si hace click en alguna de ellas, se le llevará a un menú avanzado dónde se le mostrarán las tablas que contiene y distintas opciones para con ellas. </li>
	  <li><b><?php echo $lang['mysqlvars']?></b> le mostrará respectivamente los procesos, el estado y las opciones y definiciones del servidor de base de datos MySQL. </li>
	  <li><b><?php echo $lang['mysqlsys']?></b> le permitirá acceder a una pseudoconsola del servidor de bases de datos y realizar operaciones complejas con el mismo. Nota: para poder utilizar dichas opciones, deberá tener los privilegios adecuados en el servidor MySQL.</li>
	</ul>
	<p>Otra de las opciones importantes de este menú, es la creación o modificación de los ficheros .htaccess. Dichos ficheros gestionan directamente la seguridad de los directorios y es importante por ejemplo, que no cualquier visitante pueda acceder a los datos de su base de datos mediante este programa. Por ello se recomienda encarecidamente utilizar dicha opción (o cualquier otra de que disponga) para proteger esta aplicación de usos indebidos. No obstante tenga en cuenta que hacerlo de forma errónea puede impedirle a Vd. mismo el acceso. Si le sucede esto, no se preocupe, acceda al directorio mediante FTP o mediante su gestor de archivos habitual, y elimine el fichero .htaccess para desprotegerlo y poder volver a acceder normalmente al mismo.</p>
	<h6><?php echo $lang['config']?></h6>
	<p>Aquí puede cambiar todos los datos de configuración del programa, guardar una copia de seguridad de la configuración, cargar una configuración preexistente, o volver a los valores iniciales de instalación.</p>
	<ul>
		<li><b><?php echo $lang['dbs']?></b> le permite cambiar los parámetros de conexión (haga click en mostrar / esconder) del usuario de base de datos.  Si hay más de una base de datos, puede elegir hacer un volcado múltiple e incluir más de una bases de datos en la copia de seguridad (la base de datos actual se muestra siempre en <b>negrita</b>). Además, podrá seleccionar las tablas que serán incluidas en la copia de seguridad mediante un prefijo.</li>
		<ul>
		  <li><a name="conf1"></a><b><?php echo $lang['help_db']?></b> muestra un listado de todas las bases de datos accesibles. Si ha especificado en los parámetros de conexión, que solamente se muestre un tipo una base de datos, solamente aparecerá ésta. Si hay más de una base de datos accesible, puede elegir hacer un volcado múltiple e incluir más de una bases de datos en la copia de seguridad (la base de datos actual se muestra siempre en <b>negrita</b>). Además, podrá seleccionar las tablas de cada base de datos que deben ser incluidas en la copia de seguridad mediante un prefijo, excluyendo las que no contengan el mismo.</li>
		  <li><a name="conf2"></a><?php echo $lang['help_praefix']?> es el prefijo que puede especificar para seleccionar tablas de una base de datos. Por ejemplo puede especificar solamente aquellas tablas que empiecen con el prefijo "phpBB_". Si desea hacer una copia de seguridad de toda la base de datos, deje este campo en blanco.</li>
		</ul>
		<li><b><?php echo $lang['general']?></b> sirve para elegir las características genéricas de las copias de seguridad (compresión, memoria, velocidad [ALERTA: la velocidad excesiva puede provocar que el servidor deje de responder (timeout)], archivos de registro o logs, si se deben optimizar las tablas antes de hacer la copia, etc...) y de la restauración de datos (si se deben vaciar las tablas antes de hacerla, si se debe detener la importación en caso de errores).</li>
		<ul>
		  <li><a name="conf3"></a><b><?php echo $lang['gzip']?></b> permite activar la compresión de los archivos. Se recomienda activarla, si el módulo GZIP está disponible en su servidor, ya que el tamaño de los archivos se reduce sensiblemente.</li>
		  <li><a name="conf4"></a><?php echo $lang['empty_db_before_restore']?>
       permite vaciar el contenido de la base de datos totalmente antes de realizar la recuperación de datos de una copia de seguridad existente. Es recomendable en caso de recuperar una serie de tablas que se hayan corrompido. En caso de duda, se recomienda dejarlo desactivado, puesto que si realiza una recuperación parcial de algunas tablas, se eliminarían antes todas las tablas existentes, aunque no se encuentren presentes en la copia de seguridad a recuperar.</li>
		</ul>
		<li><b><?php echo $lang['config_interface']?></b> permite elegir las características gráficas de la interfaz del programa. Puede elegir idioma, tema, definir algunos tamaños de ventana, incluso decidir si desea que aparezca el nombre del servidor en que se encuentra en este momento, y en qué lugar. La elección del navegador que utiliza es importante, ya que si lo hace de forma incorrecta, el programa no funcionará correctamente. </li>
		<ul>
			<li><a name="conf11"></a><b><?php echo $lang['help_lang']?>:</b> aquí puede seleccionar el idioma para el interfaz gráfico.</li>
		</ul>
		<li><b><?php echo $lang['config_autodelete']?></b> define los parámetros que determinan si se van a eliminar archivos de copia de seguridad de forma automática o no.</li>
		<ul>
			<li><a name="conf8"></a><b><?php echo $lang['help_ad1']?>:</b> activa o desactiva la eliminación augomática. Si está activado, se eliminarán los archivos necesarios (según las reglas definidas a continuación) antes de iniciar una nueva copia de seguridad. Es una opción útil para ahorrar espacio en el servidor, pero le recomendamos que no la active antes de haber podido probar el funcionamiento correcto del programa.</li>
			<li><a name="conf10"></a><b><?php echo $lang['help_ad3']?>:</b> un valor mayor que cero elimina todos los archivos de copia de seguridad en exceso del número especificado, bien en total, bien para cada base de datos distinta.</li>
		</ul>
		<li><b><?php echo $lang['config_email']?></b> define los parámetros que determinan si se va a enviar un email tras haber completado una copia de seguridad, así como si se deberá adjuntar dicha copia de seguridad y en qué forma.</li>
		<ul>
			<li><a name="conf8"></a><b><?php echo $lang['help_mail1']?>:</b> activa o desactiva el envío de un email al haberse terminado la copia de seguridad, sea con o sin éxito.</li>
			<li><a name="conf9"></a><b><?php echo $lang['help_mail2']?>:</b> es la dirección de email a dónde se enviará el mensaje.</li>
			<li><a name="conf10"></a><b><?php echo $lang['help_mail3']?>:</b> es la dirección de email desde donde se enviará el mensaje. Recuerde permitirle el paso a través de su filtro anti-spam, si dispone de uno.</li>
		</ul>
		<li><b><?php echo $lang['config_ftp']?></b> permite definir una (o varias) transferencias por FTP del archivo de copia de seguridad una vez terminada la misma. Si se activa, se deben especificar las parámetros necesarios para realizar la conexión. Además, deberá tener los derechos apropiados en el servidor de destino de la copia.</li>
		<ul>
			<li><a name="conf13"></a><b><?php echo $lang['help_ftptransfer']?>:</b> activa o desactiva el envío de la copia de seguridad realizada con éxito, por FTP.</li>
			<li><a name="conf14"></a><b><?php echo $lang['help_ftpserver']?>:</b> es la dirección del servidor de FTP destinatario del archivo (por ejemplo ftp.misbackups.com).</li>
			<li><a name="conf15"></a><b><?php echo $lang['help_ftpport']?>:</b> es el puerto de conexión del servidor FTP (generalmente, el puerto 21).</li>
			<li><a name="conf16"></a><b><?php echo $lang['help_ftpuser']?>:</b> es el nombre de usuario con el que debe realizarse la conexión.</li>
			<li><a name="conf17"></a><b><?php echo $lang['help_ftppass']?>:</b> es el password a utilizar para establecer la conexión.</li>
			<li><a name="conf18"></a><b><?php echo $lang['help_ftpdir']?>:</b> es el directorio de destino del archivo a almacenar. Puede ser un camino absoluto o relativo (pero debe tener derechos de escritura en el mismo).</li>
		</ul>
		<li><b><?php echo $lang['config_cronperl']?></b> permite definir las características de la copia de seguridad utilizando el script Perl. La mayoría de las opciones son muy parecidas a las establecidas para el interfaz gráfico, pero para el script Perl, que actúa de forma independiente.</li>
	</ul>
	<h6><?php echo $lang['dump']?></h6>
	<p>Esta es la parte más importante del programa. Desde aquí puede realizar una copia de seguridad de sus datos según las opciones establecidas anteriormente. Además, podrá seleccionar (si así lo desea) solamente algunas tablas para hacer dicha copia, de forma que no todos los datos de la base de datos sean copiados. </p>
	<h6><?php echo $lang['restore']?></h6>
	<p>Desde esta opción, podrá restaurar una copia de seguridad existente, a la base de datos seleccionada actualmente. </p>
	<h6><?php echo $lang['file_manage']?></h6>
	<p>En esta página se encuentran los archivos de copia de seguridad generados por el programa.<br>
  Podrá eliminarlos de uno en uno o en grupo, ejecutar el borrado automático de forma manual, descargar los archivos o subir un archivos para poder restaurarlo posteriormente.</p>
	<h6><?php echo $lang['mini-sql']?></h6>
	<p>Aquí podrá ejecutar comandos SQL contra la base de datos, así como consultar la estructura de sus tablas. Para usuarios avanzados de MySQL. </p>
	<h6><?php echo $lang['log']?></h6>
	<p>Aquí encontrará los informes de las operaciones realizadas y podrá borrarlos si así lo desea.</p>
	<h6><?php echo $lang['credits']?></h6>
	<p>La página actual.</p>
</ul>