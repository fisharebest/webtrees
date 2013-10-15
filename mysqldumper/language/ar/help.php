<div id="content">
<h3>About this project</h3>
The idea for this project comes from Daniel Schlichtholz.<p>In 2004 he created a forum called <a href="http://forum.mysqldumper.de" target="_blank">MySQLDumper</a> and soon, programmers who wrote new scripts, supplemented Daniel's scripts.<br>After a short time the small backup-script developed into a stately project.<p>If you have any improvement suggestions you can visit the MySQLDumper-Forum: <a href="http://forum.mysqldumper.de" target="_blank">http://forum.mysqldumper.de</a>.<p>We wish you a lot of fun with this project.<br><br><h4>The MySQLDumper-Team</h4>
<table><tr><td><img src="images/logo.gif" alt="MySQLDumper" border="0"></td><td valign="top">
Daniel Schlichtholz</td></tr></table>

<h3>MySQLDumper Help</h3>

<h4>Download</h4>
This Script is available on the Homepage of MySQLDumper.<br>
It is recommanded to visit the Homepage frequently to get the latest information, updates and help.<br>
The address is <a href="http://forum.mysqldumper.de" target="_blank">
http://forum.mysqldumper.de
</a>

<h4>System Mandatories</h4>
The Script works with nearly any server (Windows, Linux, ...) <br>
and PHP >= Version 4.3.4 with GZip-Library, MySQL (>= 3.23), JavaScript (must be enabled).

<a href="install.php?language=de" target="_top"><h4>Installation</h4></a>
The installation is very easy.
Unpack the archive in any folder,
which is accessible from the Webserver<br>
(e.g. in the root directory [Server rootdir/]MySQLDumper)<br>
change config.php to chmod 777<br>
... all done!<br>
you can start MySQLDumper in your Browser by typing "http://webserver/MySQLDumper"
to complete the setup, just follow the instructions.

<br><b>Note:</b><br><i>If your webserver runs with the option safemode=ON MySqlDump mustn't create directories.<br>
You will have to do that yourself.<br>
MySqlDump breaks in that case and tells you what to do.<br>
After you created the directories MySqlDump will function normally.</i><br>

<a name="perl"></a><h4>Guidance for the Perl script</h4>

Most have a cgi-bin directory, in which Perl can be executed. <br>
This is usually by Browser over http://www.domain.de/cgi-bin/ available. <br><br>

Make the following steps for this case please.  <br><br>

1.  Call in MySQLDumper the page Backup and click "Backup Perl"   <br>
2.  Copy the path, that stands behind entry in crondump.pl for $absolute_path_of_configdir:    <br>
3. open the file "crondump.pl" in the editor <br>
4. paste the copied path there with absolute_path_of_configdir (no blanks) <br>
5.  Save crondump.pl <br>
6. copy crondump.pl, as well as perltest.pl and simpletest.pl to the cgi-bin directory (ASCII mode in the ftp-client!) <br>
7. chmod 755 to the scripts.  <br>
7b. If the ending cgi is desired, change the ending of all 3 files  pl - > cgi (rename)  <br>
8.  Call in the MySQLDumper the page Configuration<br>
9. click on Cronscript <br>
10. changes Perl execution path to /cgi-bin/<br>
10b. if the Scripts are renamed to *.cgi , change Fileextension to cgi <br>
11 save the Configuration <br><br>

Ready ! The scripts are available from the Page "Backup" <br><br>

When you can execute Perl anywhere, only following step are needed:  <br><br>

1.  Call in MySQLDumper the page Backup.  <br>
2.  Copy the path, that stands behind entry in crondump.pl for $absolute_path_of_configdir:  <br>
3. open the file "crondump.pl" in the editor <br>
4. paste the copied path there with absolute_path_of_configdir (no blanks) <br>
5.  Save crondump.pl <br>

6. chmod 755 to the scripts.  <br> 
6b. If the ending cgi is desired, change the ending of all 3 files  pl - > cgi (rename)  <br>
(ev. 10b+11 from above) <br><br>


Windowsuser must change the first line of all Perlscripts, to the path of Perl.  <br><br>

Example:  <br>

instead of:  #!/usr/bin/perl w <br>
now #!C:\perl\bin\perl.exe w<br>

<h4>Operating</h4><ul>

<h6>Menu</h6>
In the select box above you choose your database.<br>
All actions refer to this database.

<h6>Home</h6>
Here you get information about your system, the version numbers and details about the configured databases.<br>
If you click on a database in the table, you get a list of tables with record counts, size and last update stamp.

<h6>Configuration</h6>
Here you can edit your configuration, save it or load the default settings.
<ul>
	<li><a name="conf1"><strong>Configured Databases:</strong> list of configured databases. The active database is in bold.</li>
	<li><a name="conf2"><strong>Table-Prefix:</strong> you can choose a prefix for each database seperated. The prefix is a filter, which only handle the tables in a dump, that start with this prefix (e.g. all tables starting with "phpBB_"). If you don't want to use it, leave this field empty.</li>
	<li><a name="conf3"><strong>GZip-Compression:</strong> Here you can activate the compression. It is recommended to work with compression because of the smaller size of files, netherless disk space is ever rarely.</li>
	<li><a name="conf19"></a><strong>Records count for backup:</strong> These are the number of records which are being read simultaneously while the backup, before the script makes the callback. For slow server you can reduce this parameter to prevent timeouts.</li>
	<li><a name="conf20"></a><strong>Records count for restore:</strong> These are the number of records which are being read simultaneously while the backup, before the script makes the callback. For slow server you can reduce this parameter to prevent timeouts.</li>
	<li><a name="conf4"></a><strong>Directory for Backup files:</strong> choose your directory for the backup files. If you choose a new one, the script will create it for you. You can use relative or absolute paths.</li>
	<li><a name="conf5"></a><strong>Send dumpfile as email:</strong> When this option is enabled, the script will automatically send the finished backup file as an email with an attachment (be careful!, you should use compression with this option because the dumpfile may be too large for email!)</li>
	<li><a name="conf6"></a><strong>Email address:</strong> Recipient's email address</li>
	<li><a name="conf7"></a><strong>Email subject:</strong> The subject of the email</li>
	<li><a name="conf13"></a><strong>FTP-Transfer: </strong>When this option is enabled, the script will automatically send the finished backup file via FTP.</li>
	<li><a name="conf14"><strong>FTP Server: </strong>the Address of the FTP-Servers (e.g. ftp.mybackups.de)</li>
	<li><a name="conf15"></a><strong>FTP Server Port: </strong>the Port for the FTP-Server (normally 21)</li>
	<li><a name="conf16"></a><strong>FTP User: </strong>the username for the FTP-Account</li>
	<li><a name="conf17"></a><strong>FTP Passwort: </strong>the password for the FTP-Account</li>
	<li><a name="conf18"></a><strong>FTP Upload-Ordner: </strong>the folder for saving the backup file (there must be Upload-Rights!)</li>
	
	<li><a name="conf8"></a><strong>automatic deletion of backups:</strong> When you activate this options, backup files will be deleted automatically by the following rules.</li>
	<li><a name="conf10"></a><strong>Delete by number of files:</strong> A Value > 0 deletes all files except the given value</li>
	<li><a name="conf11"></a><strong>Langauge:</strong> choose your language for the interface.</li>
</ul>

<h6>Management</h6>
All the actions are listed here.<br>
You see all files in the backup directory.
For the actions "Restore" and "Delete" you have to select a file first.
<UL>
	<li><strong>Restore:</strong> you restore the database with the records of the selected backupfile.</li>
	<li><strong>Delete:</strong> you can delete the selected backup file.</li>
	<li><strong>Start new Dump:</strong> here you  start a new backup (dump) with your configured parameters.</li>
</UL>

<h6>Log</h6>
You can read the Log entries and delete them.

<h6>Credits / Help</h6>
This page.
</ul>