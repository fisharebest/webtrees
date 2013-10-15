<?php
if (!defined('MSD_VERSION')) die('No direct access.');
@unlink($config['paths']['root'].'.htaccess');
@unlink($config['paths']['root'].'.htpasswd');
$action='status';

// todo -> give user info about success or failure of deleting action