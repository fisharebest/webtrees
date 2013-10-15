<?php
if (!@ob_start("ob_gzhandler")) @ob_start();
include ( './inc/header.php' );
include ( MSD_PATH . 'language/' . $config['language'] . '/lang.php' );
include ( MSD_PATH . 'language/' . $config['language'] . '/lang_help.php' );
echo MSDHeader(0);
echo headline($lang['L_CREDITS']);
include ( MSD_PATH . 'language/' . $config['language'] . '/help.php' );
?>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<?php
echo MSDFooter();
ob_end_flush();