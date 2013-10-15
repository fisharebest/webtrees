<div id="content">
<h3>Sur ce projet</h3>
L'idée pour ce projet est venue de Daniel Schlichtholz.<p>Il a ouvert en 2004 le forum <a href="http://forum.mysqldumper.de" target="_blank">MySQLDumper</a> et rapidement d'autres développeurs ont écrit de nouveaux scripts ou bien élargi les scripts de Daniel. En peu de temps le simple script de sauvegarde est devenu un imposant projet.<p>Si vous avez des propositions d'améliorations vous pouvez les communiquer dans le Forum MySQLDumper <a href="http://forum.mysqldumper.de" target="_blank">http://forum.mysqldumper.de</a>. <p>Nous vous souhaitons beaucoup de plaisir avec ce projet.<br><p><h4>L'équipe de MySQLDumper</h4>

<table><tr><td><img src="images/logo.gif" alt="MySQLDumper" border="0"></td><td valign="top">
Daniel Schlichtholz</td></tr></table>
<br>

<h3>Aide MySQLDumper</h3>

<h4>Téléchargement</h4>
Vous venez de télécharger ce script sur le page d'accueil de MySQLDumper.<br>
Nous vous conseillons de visiter régulièrement la page d'accueil, afin d'accéder aux mises à jour et au support.<br>
L'adresse est: <a href="http://forum.mysqldumper.de" target="_blank">
http://forum.mysqldumper.de
</a>

<h4>Système requis</h4>
Le script travaille sur tous les serveurs (Windows, Linux...)<br>
ayant PHP >= version 4.3.4 avec GZip, MySQL (à partir de la version 3.23), JavaScript (doit être activé).

<a href="install.php?language=fr" target="_top"><h4>Installation</h4></a>
L'installation est simple.
Décompresser l'archive dans un répertoire quelconque.<br>
Envoyer tous les fichiers sur votre espace web. (exemple:Dans le niveau le plus bas [Répertoire du serveur/]MySQLDumper)<br>
... c'est terminé!<br>
Maintenant il suffit d'appeler MySQLDumper en saisissant l'adresse suivante dans votre navigateur "http://mon-site-web/MySQLDumper"<br>
afin de terminer l'installation. Il suffit maintenant de suivre les instructions.<br>
<br><b>Remarque:</b><br><i>Si votre espace web à la fonction PHP Safemode activé, le script ne pourra pas créer les répertoires.<br>
Vous devrez le faire manuellement afin que MySqlDump puisse sauvegarder les données dans les répertoires.<br> 
Le script s'arrêtera avec un message en conséquence!<br>
Après avoir crée les répertoires (d'après les informations reçues), le programme fonctionnera normalement et sans restrictions.</i>

<a name="perl"></a><h4>Mode d'emploi du script Perl</h4>
La plupart ont un répertoire cgi-bin, qui permet d'exécuter en Perl. <br>
Vous pouvez y accéder dans la majeur partie des cas en saisissant l'adresse suivante dans votre navigateur http://www.domaine.com/cgi-bin/. <br>
<br>
Dans ce cas veuillez suivre les étapes suivantes:<br><br>

1. Appeler la page 'Sauvegarde' dans MySQLDumper. <br>
2. Copier le chemin qui se trouve derrière le texte: crondump.pl pour $absolute_path_of_configdir: <br>
3. Ouvrir le fichier "crondump.pl" dans un éditeur <br>
4. et transmettre le chemin que vous venez de copier près de absolute_path_of_configdir (sans espace) <br>
5. Sauvegarder crondump.pl <br>
6. Copier crondump.pl, ainsi que perltest.pl et simpletest.pl dans le répertoire cgi-bin (Mode-ASCII par FTP) <br>
7. Donner les droits CHMOD 755 <br>
7b. Si l'extension .cgi est désirée, changer pour les trois fichiers l'extension de .pl vers .cgi (action renommer) <br>
8. Appeller la configuraion dans MySQLDumper<br>
9. Choisir la page script Cron <br>
10. Changer le chemin d'exécution Perl vers /cgi-bin/ <br>
10b. Si les scripts ont l'extension .cgi, changer l'extension vers .cgi <br>
11. et sauvegarder la configuration <br><br>

Voila, c'est terminé, les scripts s'exécutent maintenant d'après la page de sauvegarde.<br><br>

Si vous pouvez exécuter Perl de tous les fichiers, il vous suffit de suivre les étapes suivantes:<br><br>

1. Appeler dans MySQLDumper la page 'Sauvegarde' et cliquer "Backup Perl". <br>
2. Copier le chemin qui se trouve derrière le texte: crondump.pl pour $absolute_path_of_configdir: <br>
3. Ouvrir le fichier "crondump.pl" dans un éditeur <br>
4. et transmettre le chemin que vous avez copié près de absolute_path_of_configdir (sans espace) <br>
5. Sauvegarder crondump.pl <br>
6. Donner les droits CHMOD 755 <br>
6b. Si l'extension .cgi est désirée, changer pour les trois fichiers l'extension de .pl vers .cgi (action renommer)  <br>
(eventuellement 10b+11 ci-desus)<br>
<br>

Les utilisateurs de Windows doivent changer pour tous les scripts la première ligne où est stipulé le chemin du programme Perl. Exemple: <br>
à la place de: #!/usr/bin/perl -w <br>
remplacer par: #!C:\perl\bin\perl.exe -w <br>

<h4>Navigation</h4><ul>

<h6>Menu déroulant</h6>
Dans le menu déroulant vous sélectionnez la base de données.<br>
Toutes les actions suivantes se rapportent cette base de données.

<h6>Page d'accueil</h6>
Vous trouverez ici des informations sur votre système, les différentes versions installées et des détails sur la configuration des bases de données.<br>
La sélection d'une base de données vous donnent de plus amples informations sur le nombre de tables, le nombre de paquets, la taille et la dernière mise à jour.

<h6>Configuration</h6>
Ici vous pouvez éditer, sauvegarder votre configuration ou bien réinstaller la configuration standard.
<ul><br>
	<li><a name="conf1"></a><strong>Configurer la base de données:</strong> Liste des bases de données configurées. La base de données active est listée en <b>caractères gras</b>. </li>
	<li><a name="conf2"></a><strong>Préfix des tables:</strong> Ici vous pouvez définir (pour chaque base de donnée) un préfix. C'est un filtre, qui permet de sélectionner lors de la sauvegarde les tables contenant le préfix défini (exemple: Toutes les tables qui commencent avec "phpBB_"). Si vous désirer sauvgarder toutes les tables, laisser ce champ libre.</li>
	<li><a name="conf3"></a><strong>Compression GZip:</strong> Ici vous pouvez activer la compression de fichier. Nous vous conseillons la compression, car les fichiers sont plus petit et nécessitent moins de place.</li>
	<li><a name="conf5"></a><strong>Courriel avec pièces jointes:</strong> Si vous avez activé cette option, vous recevrez après la création de la copie de sauvegarde un courriel avec la copie de sauvegarde en pièce jointe (Attention, la compression doit être impérativement activée, sinon la pièce jointe risque d'être trop volumineuse et ne pourra eventuellement pas être envoyée avec le courriel!)</li>
	<li><a name="conf6"></a><strong>Adresse électronique:</strong> Destinataire du courriel</li>
	<li><a name="conf7"></a><strong>Expéditeur du courriel:</strong> C'est l'adresse de l'expéditeur qui apparaitra dans le courriel</li>
	<li><a name="conf13"></a><strong>Transfert FTP: </strong>Si cette option est activée, après la création de la copie de sauvegarde, celle-ci sera envoyée par FTP sur un serveur.</li>
	<li><a name="conf14"></a><strong>Serveur FTP: </strong>L'adresse du serveur FTP (exemple: ftp.mybackups.com)</li>
	<li><a name="conf15"></a><strong>Port du serveur FTP: </strong>Port du serveur FTP (en général 21)</li>
	<li><a name="conf16"></a><strong>Utilisateur FTP: </strong>Le nom de l'utilisateur du compte FTP</li>
	<li><a name="conf17"></a><strong>Mot de passe FTP: </strong>Le mot de passe du compte FTP </li>
	<li><a name="conf18"></a><strong>Répertoire de téléchargement FTP: </strong>Le répertoire dans lequel la copie de sauvegarde doit être copiée (il doit exister des droits afin de pouvoir télécharger vers le serveur!)</li>
	<li><a name="conf8"></a><strong>Suppression automatique de la copie de sauvegarde:</strong> Si cette option est activée, les sauvegardes les plus anciennes seront supprimée d'après les options choisies. La combinaison d'options n'est pas possible.</li>
	<li><a name="conf10"></a><strong>Nombre de copie de sauvegarde:</strong> La valeur > 0 supprime toutes les copies de sauvegardes les plus anciennes excepté le nombre défini dans ce champ</li>
	<li><a name="conf11"></a><strong>Langue:</strong> Ici vous définissez la langue de votre interface.</li>
</ul>

<h6>Administration</h6>
Ici s'exécutent les actions.<br>
Toutes les copies de sauvegarde sont visibles ici.
Pour les actions de restauration ou de suppression on doit sélectionner un fichier.
<UL>
	<li><strong>Restauration:</strong> La copie de sauvegarde sélectionnée sera restaurée.</li>
	<li><strong>Suppression:</strong> La copie de sauvegarde sélectionnée sera supprimée.</li>
	<li><strong>Exécuter une nouvelle copie de sauvegarde:</strong> Exécution d'une nouvelle copie de sauvegarde (Dump) d'après les paramètres du menu "Configuration".</li>
</UL>

<h6>Journal</h6>
Le journal vous permet de voir et de supprimer les entrées.

<h6>Crédits / Aide</h6>
Cette page.
</ul>