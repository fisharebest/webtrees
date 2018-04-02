<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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
declare(strict_types=1);

namespace Fisharebest\Webtrees\Http\Controllers;

use Exception;
use Fisharebest\Localization\Locale;
use Fisharebest\Localization\Locale\LocaleEnUs;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\DebugBar;
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\User;
use PDOException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for the installation wizard
 */
class SetupController extends AbstractBaseController {
	const WT_CONFIG_FILE = 'config.ini.php';

	/**
	 * Installation wizard - check user input and proceed to the next step.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function setup(Request $request): Response {
		// Config file exists?  Our work is done.
		if (file_exists(WT_DATA_DIR . self::WT_CONFIG_FILE)) {
			return new RedirectResponse(Html::url('index.php', []));
		}

		$step     = (int) $request->get('step', '1');
		$errors   = $this->serverErrors();
		$warnings = $this->serverWarnings();
		$data     = $this->extractParameters($request);

		if ($data['lang'] === '') {
			$data['lang'] = Locale::httpAcceptLanguage($_SERVER, I18N::installedLocales(), new LocaleEnUs)->languageTag();
		}

		if ($data['dbuser'] !== '') {
			$db_conn_error = $this->checkDatabaseConnection($data['dbhost'], $data['dbport'], $data['dbuser'], $data['dbpass']);
		} else {
			$db_conn_error = '';
		}

		if ($data['dbname'] !== '') {
			$db_name_error = $this->checkDatabaseName($data['dbhost'], $data['dbport'], $data['dbuser'], $data['dbpass'], $data['dbname'], $data['tblpfx']);
		} else {
			$db_name_error = '';
		}

		if ($data['wtname'] !== '') {
			$wt_user_error = $this->checkAdminUser($data['wtname'], $data['wtuser'], $data['wtpass'], $data['wtemail']);
		} else {
			$wt_user_error = '';
		}

		$data['cpu_limit']     = $this->maxExecutionTime();
		$data['db_conn_error'] = $db_conn_error;
		$data['db_name_error'] = $db_name_error;
		$data['errors']        = $errors;
		$data['locales']       = I18N::installedLocales();
		$data['memory_limit']  = $this->memoryLimit();
		$data['warnings']      = $warnings;
		$data['wt_user_error'] = $wt_user_error;

		if ($wt_user_error && $step > 5) {
			$step = 5;
		}
		if ($db_name_error && $step > 4) {
			$step = 4;
		}
		if ($db_conn_error && $step > 3) {
			$step = 3;
		}
		if (!empty($errors) && $step > 2) {
			$step = 2;
		}
		if ($this->checkLanguage($request) === false && $step > 1) {
			$step = 1;
		}

		switch ($step) {
			default:
			case 1:
				return $this->viewResponse('setup/step-1-language', $data);
			case 2:
				return $this->viewResponse('setup/step-2-server-checks', $data);
			case 3:
				return $this->viewResponse('setup/step-3-database-connection', $data);
			case 4:
				return $this->viewResponse('setup/step-4-database-name', $data);
			case 5:
				return $this->viewResponse('setup/step-5-administrator', $data);
			case 6:
				$error = $this->createConfigFile($data['dbhost'], $data['dbport'], $data['dbuser'], $data['dbpass'], $data['dbname'], $data['tblpfx'], $data['wtname'], $data['wtuser'], $data['wtpass'], $data['wtemail']);

				if ($error === '') {
					return new RedirectResponse(Html::url('index.php', []));
				} else {
					return $this->viewResponse('setup/step-6-failed', ['error' => $error]);
				}
		}
	}

	/**
	 * @param string $wtname
	 * @param string $wtuser
	 * @param string $wtpass
	 * @param string $wtemail
	 *
	 * @return string
	 */
	private function  checkAdminUser($wtname, $wtuser, $wtpass, $wtemail): string {
		if ($wtname === '' || $wtuser === '' || $wtpass === '' || $wtemail === '') {
			return I18N::translate('You must enter all the administrator account fields.');
		}

		if (mb_strlen($wtpass) < 6) {
			return I18N::translate('The password needs to be at least six characters long.');
		}

		return '';
	}


	/**
	 * Check we can write to the data folder.
	 *
	 * @param string $dbhost
	 * @param string $dbport
	 * @param string $buser
	 * @param string $dbpass
	 *
	 * @return string
	 *
	 */
	private function checkDatabaseConnection($dbhost, $dbport, $buser, $dbpass): string {
		try {
			Database::createInstance([
				'dbhost' => $dbhost,
				'dbport' => $dbport,
				'dbname' => '',
				'dbuser' => $buser,
				'dbpass' => $dbpass,
				'tblpfx' => '',
			]);
			Database::disconnect();
		} catch (PDOException $ex) {
			DebugBar::addThrowable($ex);

			return I18N::translate('Unable to connect using this username and password. Your server gave the following error.') . '<br><br>' . e($ex->getMessage()) . '<br><br>' . I18N::translate('Check the settings and try again.');
		}

		return '';
	}

	/**
	 * Check we can write to the data folder.
	 *
	 * @param string $dbhost
	 * @param string $dbport
	 * @param string $dbuser
	 * @param string $dbpass
	 * @param string $dbname
	 * @param string $tblpfx
	 *
	 * @return string
	 *
	 */
	private function checkDatabaseName($dbhost, $dbport, $dbuser, $dbpass, $dbname, $tblpfx): string {
		// The character ` is not valid in database or table names (even if escaped).
		// The form should prevent the user from entering them.
		if ($dbname === '' || strpos($dbname, '`') !== false) {
			return 'Invalid database name';
		}

		if (strpos($tblpfx, '`') !== false) {
			return 'Invalid table prefix';
		}

		try {
			define('WT_TBLPREFIX', $tblpfx);
			Database::createInstance([
				'dbhost' => $dbhost,
				'dbport' => $dbport,
				'dbname' => '',
				'dbuser' => $dbuser,
				'dbpass' => $dbpass,
				'tblpfx' => '',
			]);
			Database::exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` COLLATE utf8_unicode_ci");
			Database::exec("USE `{$dbname}`");
			Database::disconnect();
		} catch (PDOException $ex) {
			DebugBar::addThrowable($ex);

			return I18N::translate('Unable to connect using this username and password. Your server gave the following error.') . '<br><br>' . e($ex->getMessage()) . '<br><br>' . I18N::translate('Check the settings and try again.');
		}

		return '';
	}

	/**
	 * Check we can write to the data folder.
	 *
	 * @param string $data_dir
	 *
	 * @return bool
	 */
	private function checkFolderIsWritable(string $data_dir): bool {
		$text1 = uniqid();
		try {
			file_put_contents($data_dir . 'test.txt', $text1);
			$text2 = file_get_contents(WT_DATA_DIR . 'test.txt');
			unlink(WT_DATA_DIR . 'test.txt');
		} catch (Exception $ex) {
			DebugBar::addThrowable($ex);

			return false;
		}

		return $text1 === $text2;
	}

	/**
	 * Check the language parameters are OK.
	 *
	 * @param Request $request
	 *
	 * @return bool
	 */
	private function checkLanguage(Request $request): bool {
		try {
			I18N::init($request->get('lang'));
		} catch (Exception $ex) {
			DebugBar::addThrowable($ex);

			return false;
		}

		return true;
	}

	/**
	 * @param string $dbhost
	 * @param string $dbport
	 * @param string $dbuser
	 * @param string $dbpass
	 * @param string $dbname
	 * @param string $tblpfx
	 * @param string $wtname
	 * @param string $wtuser
	 * @param string $wtpass
	 * @param string $wtemail
	 *
	 * @return string
	 */
	private function createConfigFile($dbhost, $dbport, $dbuser, $dbpass, $dbname, $tblpfx, $wtname, $wtuser, $wtpass, $wtemail): string {

		try {
			// Create/update the database tables.
			Database::createInstance([
				'dbhost' => $dbhost,
				'dbport' => $dbport,
				'dbname' => $dbname,
				'dbuser' => $dbuser,
				'dbpass' => $dbpass,
				'tblpfx' => $tblpfx,
			]);
			Database::updateSchema('\Fisharebest\Webtrees\Schema', 'WT_SCHEMA_VERSION', 30);

			// If we are re-installing, then this user may already exist.
			$admin = User::findByIdentifier($wtemail);
			if ($admin === null) {
				$admin = User::findByIdentifier($wtuser);
			}
			// Create the user
			if ($admin === null) {
				$admin = User::create($wtuser, $wtname, $wtemail, $wtpass)
					->setPreference('language', WT_LOCALE)
					->setPreference('visibleonline', '1');
			} else {
				$admin->setPassword($_POST['wtpass']);
			}
			// Make the user an administrator
			$admin
				->setPreference('canadmin', '1')
				->setPreference('verified', '1')
				->setPreference('verified_by_admin', '1');

			// Write the config file. We already checked that this would work.
			$config_ini_php =
				'; <' . '?php exit; ?' . '> DO NOT DELETE THIS LINE' . PHP_EOL .
				'dbhost="' . addcslashes($dbhost, '"') . '"' . PHP_EOL .
				'dbport="' . addcslashes($dbport, '"') . '"' . PHP_EOL .
				'dbuser="' . addcslashes($dbuser, '"') . '"' . PHP_EOL .
				'dbpass="' . addcslashes($dbpass, '"') . '"' . PHP_EOL .
				'dbname="' . addcslashes($dbname, '"') . '"' . PHP_EOL .
				'tblpfx="' . addcslashes($tblpfx, '"') . '"' . PHP_EOL;

			file_put_contents(WT_DATA_DIR . 'config.ini.php', $config_ini_php);

			// Done - start using webtrees!
			return '';
		} catch (PDOException $ex) {
			DebugBar::addThrowable($ex);

			return $ex->getMessage();
		}
	}

	/**
	 * @param Request $request
	 *
	 * @return string[]
	 */
	private function extractParameters(Request $request): array {
		return [
			'lang'    => $request->get('lang', ''),
			'dbhost'  => $request->get('dbhost', 'localhost'),
			'dbport'  => $request->get('dbport', '3306'),
			'dbuser'  => $request->get('dbuser', ''),
			'dbpass'  => $request->get('dbpass', ''),
			'dbname'  => $request->get('dbname', ''),
			'tblpfx'  => $request->get('tblpfx', 'wt_'),
			'wtname'  => $request->get('wtname', ''),
			'wtuser'  => $request->get('wtuser', ''),
			'wtpass'  => $request->get('wtpass', ''),
			'wtemail' => $request->get('wtemail', ''),
		];
	}

	/**
	 * The server's memory limit
	 *
	 * @return int
	 */
	private function maxExecutionTime(): int {
		return (int) ini_get('max_execution_time');
	}

	/**
	 * The server's memory limit (in MB).
	 *
	 * @return int
	 */
	private function memoryLimit(): int {
		$memory_limit = ini_get('memory_limit');

		switch (substr($memory_limit, -1)) {
			case 'k':
			case 'K':
				$memory_limit = substr($memory_limit, 0, -1) / 1024;
				break;
			case 'm':
			case 'M':
				$memory_limit = substr($memory_limit, 0, -1);
				break;
			case 'g':
			case 'G':
				$memory_limit = substr($memory_limit, 0, -1) * 1024;
				break;
			case 't':
			case 'T':
				$memory_limit = substr($memory_limit, 0, -1) * 1024 * 1024;
				break;
			default:
				$memory_limit = $memory_limit / 1024 / 1024;
		}

		return (int) $memory_limit;
	}

	/**
	 * A list of major server issues.
	 *
	 * @return array
	 */
	private function serverErrors(): array {
		$extensions = ['mbstring', 'iconv', 'pcre', 'pdo', 'pdo_mysql', 'session'];
		$functions  = ['parse_ini_file'];
		$errors     = [];

		if (!$this->checkFolderIsWritable(WT_DATA_DIR)) {
			$errors[] = '<code>' . e(realpath(WT_DATA_DIR)) . '</code><br>' . I18N::translate('Oops! webtrees was unable to create files in this folder.') . '<br>' . I18N::translate('This usually means that you need to change the folder permissions to 777.') . '<br>' . I18N::translate('You must change this before you can continue.');
		}

		foreach ($extensions as $extension) {
			if (!extension_loaded($extension)) {
				$errors[] = I18N::translate('PHP extension “%s” is disabled. You cannot install webtrees until this is enabled. Please ask your server’s administrator to enable it.', $extension);
			}
		}

		$disable_functions = preg_split('/ *, */', ini_get('disable_functions'));
		foreach ($functions as $function) {
			if (in_array($function, $disable_functions)) {
				$errors[] = /* I18N: %s is a PHP function/module/setting */ I18N::translate('%s is disabled on this server. You cannot install webtrees until it is enabled. Please ask your server’s administrator to enable it.', $function . '()');
			}
		}

		return $errors;
	}

	/**
	 * A list of minor server issues.
	 *
	 * @return array
	 */
	private function serverWarnings(): array {
		$extensions = [
			'gd'        => /* I18N: a program feature */ I18N::translate('creating thumbnails of images'),
			'xml'       => /* I18N: a program feature */ I18N::translate('reporting'),
			'simplexml' => /* I18N: a program feature */ I18N::translate('reporting'),
		];
		$settings = [
			'file_uploads' => /* I18N: a program feature */ I18N::translate('file upload capability')
		];
		$warnings = [];

		foreach ($extensions as $extension => $features) {
			if (!extension_loaded($extension)) {
				$warnings[] = I18N::translate('PHP extension “%1$s” is disabled. Without it, the following features will not work: %2$s. Please ask your server’s administrator to enable it.', $extension, $features);
			}
		}

		foreach ($settings as $setting => $features) {
			if (!ini_get($setting)) {
				$warnings[] = I18N::translate('PHP setting “%1$s” is disabled. Without it, the following features will not work: %2$s. Please ask your server’s administrator to enable it.', $setting, $features);
			}
		}

		return $warnings;
	}

	/**
	 * Create a response object from a view.
	 *
	 * @param string  $name
	 * @param mixed[] $data
	 * @param int     $status
	 *
	 * @return Response
	 */
	protected function viewResponse($name, $data, $status = Response::HTTP_OK): Response {
		$html = view('layouts/setup', [
			'content' => view($name, $data),
		]);

		return new Response($html, $status);
	}
}
