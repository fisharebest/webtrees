<?php
// *****************************************************************************
// This file holds all available languages
// *****************************************************************************

// Array initialization
$lang=Array();

// *****************************************************************************
// Add language to array. Must match directory name of the language.
GetLanguageArray();

// *****************************************************************************
// Language name in its own language.
$lang['en']='English';
$lang['de']='Deutsch';
$lang['es']='Español';
$lang['fr']='Français';
$lang['it']='Italiano';
$lang['nl']='Nederlands';
$lang['sw']='Svenska';
$lang['de_du']='Deutsch (mit Anredeform "du")';
$lang['pt_br']='Portuguese - BR';
$lang['tr']='Türkçe';
$lang['da']='Dansk';
$lang['lu']='Luxemburg';
$lang['pl']='Polski';
$lang['ch']='Schweizer Deutsch';
$lang['ar']='Arabic';
$lang['vn']='Vietnamese';
$lang['el']='Ελληνικά';

// *****************************************************************************
// Add the installation entries here, and you're done with this file. :-)

$lang['L_TOOLS1']['de']='MySQLDumper deinstallieren';
$lang['L_TOOLS2']['de']='Vorhandene Konfigurationssicherung importieren';
$lang['L_TOOLS3']['de']='Konfigurationssicherung hochladen und importieren';
$lang['L_TOOLS4']['de']='Konfigurationssicherung herunterladen';

$lang['L_TOOLS1']['de_du']='MySQLDumper deinstallieren';
$lang['L_TOOLS2']['de_du']='Vorhandene Konfigurationssicherung importieren';
$lang['L_TOOLS3']['de_du']='Konfigurationssicherung hochladen und importieren';
$lang['L_TOOLS4']['de_du']='Konfigurationssicherung herunterladen';

$lang['L_TOOLS1']['en']='Uninstall MySQLDumper';
$lang['L_TOOLS2']['en']='Import existing configuration backup';
$lang['L_TOOLS3']['en']='Upload configuration backup and import';
$lang['L_TOOLS4']['en']='Download Configuration Backup';

$lang['L_TOOLS1']['es']='Desinstalar MySQLDumper';
$lang['L_TOOLS2']['es']='Importar configuración existente';
$lang['L_TOOLS3']['es']='Subir copia de la configuración e importar';
$lang['L_TOOLS4']['es']='Crear y descargar una copia de la configuración';

$lang['L_TOOLS1']['fr']='Désinstaller MySQLDumper';
$lang['L_TOOLS2']['fr']='Importation d\'une copie de sauvegarde existante';
$lang['L_TOOLS3']['fr']='Télécharger une copie de sauvegarde sur le serveur ';
$lang['L_TOOLS4']['fr']='Télécharger une copie de sauvegarde';

$lang['L_TOOLS1']['it']='MySQLDumper disinstallare';
$lang['L_TOOLS2']['it']='Importare l`attuale backup di configurazione';
$lang['L_TOOLS3']['it']='Prelevare ed importare il backup di configurazione';
$lang['L_TOOLS4']['it']='Scaricare backup di configurazione';

$lang['L_TOOLS1']['nl']='MySQLDumper deinstalleren';
$lang['L_TOOLS2']['nl']='Bestaande configuratie backup importeren';
$lang['L_TOOLS3']['nl']='Upload configuratie backup en importeren';
$lang['L_TOOLS4']['nl']='Download configuratie backup';

$lang['L_TOOLS1']['sw']='Avinstallera MySQLDumper';
$lang['L_TOOLS2']['sw']='Importera existerande konfigureringsbackup';
$lang['L_TOOLS3']['sw']='Ladda upp och importera konfigureringsbackup';
$lang['L_TOOLS4']['sw']='Ladda ner konfigureringsbackup';

$lang['L_TOOLS1']['pt_br']='Desinstalar MySQLDumper';
$lang['L_TOOLS2']['pt_br']='Importar backup de configuração existente';
$lang['L_TOOLS3']['pt_br']='Enviar backup da configuração e importar';
$lang['L_TOOLS4']['pt_br']='Baixar backup da configuração';

$lang['L_TOOLS1']['tr']='MySQLDumperi kaldır';
$lang['L_TOOLS2']['tr']='Ayar dosyasını içeri aktar';
$lang['L_TOOLS3']['tr']='Ayar dosyasını yükle ve içeri aktar';
$lang['L_TOOLS4']['tr']='Ayar dosyasını indir';

$lang['L_TOOLS1']['da']='Afinstallér MySQLDumper';
$lang['L_TOOLS2']['da']='Importér eksisterende konfigurationsbackup';
$lang['L_TOOLS3']['da']='Upload og importér konfigurationsbackup';
$lang['L_TOOLS4']['da']='Download konfigurationsbackup';

$lang['L_TOOLS1']['lu']='MySQLDumper deinstallieren';
$lang['L_TOOLS2']['lu']='Vorhandene Konfigurationssicherung importieren';
$lang['L_TOOLS3']['lu']='Konfigurationssicherung hochladen und importieren';
$lang['L_TOOLS4']['lu']='Konfigurationssicherung herunterladen';

$lang['L_TOOLS1']['pl']='Odinstaluj MySQLDumper';
$lang['L_TOOLS2']['pl']='Zaimportuj istniejące ustawienia backupu';
$lang['L_TOOLS3']['pl']='Prześil i zaimportuj ustawienia backupu';
$lang['L_TOOLS4']['pl']='Ściągnij ustawienia backupu';

$lang['L_TOOLS1']['ch']='MySQLDumper deinstallieren';
$lang['L_TOOLS2']['ch']='Vorhandene Konfigurationssicherung importieren';
$lang['L_TOOLS3']['ch']='Konfigurationssicherung hochladen und importieren';
$lang['L_TOOLS4']['ch']='Konfigurationssicherung herunterladen';

$lang['L_TOOLS1']['ar']='Uninstall MySQLDumper';
$lang['L_TOOLS2']['ar']='Import existing configuration backup';
$lang['L_TOOLS3']['ar']='Upload configuration backup and import';
$lang['L_TOOLS4']['ar']='Download Configuration Backup';

$lang['L_TOOLS1']['vn']='Uninstall MySQLDumper';
$lang['L_TOOLS2']['vn']='Import existing configuration backup';
$lang['L_TOOLS3']['vn']='Upload configuration backup and import';
$lang['L_TOOLS4']['vn']='Download Configuration Backup';

$lang['L_TOOLS1']['el']='Απεγκατάσταση MySQLDumper';
$lang['L_TOOLS2']['el']='Εισαγωγή υπάρχουσας αποθηκευμένης ρύθμισης';
$lang['L_TOOLS3']['el']='Φόρτωση αποθηκευμένης ρύθμισης και εισαγωγή της';
$lang['L_TOOLS4']['el']='Μεταφόρτωση αποθηκευμένης ρύθμισης';

// *****************************************************************************
// Language defaults to english.

if (!in_array($config['language'],$lang['languages'])) $config['language']='en';
include_once('./language/'.$config['language'].'/lang.php');
?>