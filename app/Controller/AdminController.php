<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2017 webtrees development team
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
namespace Fisharebest\Webtrees\Controller;

use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module;

/**
 * Controller for the administration pages
 */
class AdminController extends PageController {
	/**
	 * Generate a list of module names which exist in the database but not on disk.
	 *
	 * @return string[]
	 */
	public function deletedModuleNames() {
		$database_modules = Database::prepare("SELECT module_name FROM `##module`")->fetchOneColumn();
		$disk_modules     = Module::getInstalledModules('disabled');

		return array_diff($database_modules, array_keys($disk_modules));
	}

	/**
	 * Generate a list of potential problems with the server.
	 *
	 * @return string[]
	 */
	public function serverWarnings() {
		$php_support_url   = 'https://secure.php.net/supported-versions.php';
		$php_major_version = explode('.', PHP_VERSION)[0];
		$today             = date('Y-m-d');
		$warings           = [];

		if ($php_major_version === 70 && $today >= '201-12-03' || $php_major_version === 71 && $today >= '2019-12-01') {
			$warings[] =
				I18N::translate('Your web server is using PHP version %s, which is no longer receiving security updates. You should upgrade to a later version as soon as possible.', PHP_VERSION) .
				' <a href="' . $php_support_url . '">' . $php_support_url . '</a>';
		} elseif ($php_major_version === 70 && $today >= '2017-12-03' || $php_major_version === 71 && $today >= '2018-12-01') {
			$warings[] = I18N::translate('Your web server is using PHP version %s, which is no longer maintained. You should upgrade to a later version.', PHP_VERSION) . ' <a href="' . $php_support_url . '">' . $php_support_url . '</a>';
		}

		return $warings;
	}
}
