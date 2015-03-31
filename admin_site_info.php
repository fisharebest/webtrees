<?php
namespace Fisharebest\Webtrees;

/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

define('WT_SCRIPT_NAME', 'admin_site_info.php');
require './includes/session.php';

$controller = new PageController;
$controller
	->restrictAccess(Auth::isAdmin())
	->setPageTitle(I18N::translate('Server information'))
	->pageHeader();

$variables = Database::prepare("SHOW VARIABLES")->fetchAssoc();
array_walk($variables, function(&$x) { $x = str_replace(',', ', ', $x); });

ob_start();
phpinfo(INFO_ALL & ~INFO_CREDITS & ~INFO_LICENSE);
preg_match('%<style type="text/css">(.*?)</style>.*?(<body>.*</body>)%s', ob_get_clean(), $matches);
$style = $matches[1];
$html  = $matches[2];

?>

<ol class="breadcrumb small">
	<li><a href="admin.php"><?php echo I18N::translate('Control panel'); ?></a></li>
	<li class="active"><?php echo $controller->getPageTitle(); ?></li>
</ol>

<h1><?php echo $controller->getPageTitle(); ?></h1>

<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h2 class="panel-title">
					<?php echo I18N::translate('Time'); ?>
				</h2>
			</div>
			<div class="panel-body">
				<?php echo /* I18N: The local time on the server */ I18N::translate('Server time'); ?> —
				<?php echo format_timestamp(WT_SERVER_TIMESTAMP); ?><br>
				<?php echo /* I18N: The local time on the client/browser */ I18N::translate('Client time'); ?> —
				<?php echo format_timestamp(WT_CLIENT_TIMESTAMP); ?><br>
				<?php echo /* I18N: Timezone - http://en.wikipedia.org/wiki/UTC */ I18N::translate('UTC'); ?> —
				<?php echo format_timestamp(WT_TIMESTAMP); ?>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h2 class="panel-title">
					<?php echo I18N::translate('PHP information'); ?>
				</h2>
			</div>
			<div class="panel-body">
				<style type="text/css" scoped>
					<?php echo $style; ?>
					table { width: 100%; }
					td.v { word-break: break-all; }
				</style>
				<?php echo $html; ?>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h2 class="panel-title">
					<?php echo I18N::translate('MySQL variables'); ?>
				</h2>
			</div>
			<div class="panel-body">
				<dl>
					<?php foreach ($variables as $variable => $value): ?>
						<dt><?php echo Filter::escapeHtml($variable); ?></dt>
						<dd><?php echo Filter::escapeHtml($value); ?></dd>
					<?php endforeach; ?>
				</dl>
			</div>
		</div>
	</div>
</div>
