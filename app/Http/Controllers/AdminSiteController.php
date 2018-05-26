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

use DirectoryIterator;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\File;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Theme;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use PDO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for site administration.
 */
class AdminSiteController extends AbstractBaseController {
	protected $layout = 'layouts/administration';

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function analyticsForm(Request $request): Response {
		$title = /* I18N: e.g. http://www.google.com/analytics */
			I18N::translate('Tracking and analytics');

		return $this->viewResponse('admin/site-analytics', [
			'title' => $title,
		]);
	}

	/**
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function analyticsSave(Request $request): RedirectResponse {
		Site::setPreference('BING_WEBMASTER_ID', $request->get('BING_WEBMASTER_ID'));
		Site::setPreference('GOOGLE_WEBMASTER_ID', $request->get('GOOGLE_WEBMASTER_ID'));
		Site::setPreference('GOOGLE_ANALYTICS_ID', $request->get('GOOGLE_ANALYTICS_ID'));
		Site::setPreference('PIWIK_URL', $request->get('PIWIK_URL'));
		Site::setPreference('PIWIK_SITE_ID', $request->get('PIWIK_SITE_ID'));
		Site::setPreference('STATCOUNTER_PROJECT_ID', $request->get('STATCOUNTER_PROJECT_ID'));
		Site::setPreference('STATCOUNTER_SECURITY_ID', $request->get('STATCOUNTER_SECURITY_ID'));

		FlashMessages::addMessage(I18N::translate('The website preferences have been updated.'), 'success');
		$url = route('admin-control-panel');

		return new RedirectResponse($url);
	}

	/**
	 * Show old user files in the data folder.
	 *
	 * @return Response
	 */
	public function cleanData(): Response {
		$protected = [
			'.htaccess',
			'.gitignore',
			'index.php',
			'config.ini.php',
		];

		// If we are storing the media in the data folder (this is the default), then don’t delete it.
		foreach (Tree::getAll() as $tree) {
			$MEDIA_DIRECTORY = $tree->getPreference('MEDIA_DIRECTORY');
			list($folder) = explode('/', $MEDIA_DIRECTORY);

			if ($folder !== '..') {
				$protected[] = $folder;
			}
		}

		$entries = [];

		foreach (new DirectoryIterator(WT_DATA_DIR) as $file) {
			$entries[] = $file->getFilename();
		}
		$entries = array_diff($entries, [
			'.',
			'..',
		]);

		return $this->viewResponse('admin/clean-data', [
			'title'     => I18N::translate('Clean up data folder'),
			'entries'   => $entries,
			'protected' => $protected,
		]);
	}

	/**
	 * Delete old user files in the data folder.
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function cleanDataAction(Request $request): RedirectResponse {
		$to_delete = (array) $request->get('to_delete');
		$to_delete = array_filter($to_delete);

		foreach ($to_delete as $path) {
			// Show different feedback message for files and folders.
			$is_dir = is_dir(WT_DATA_DIR . $path);

			if (File::delete(WT_DATA_DIR . $path)) {
				if ($is_dir) {
					FlashMessages::addMessage(I18N::translate('The folder %s has been deleted.', e($path)), 'success');
				} else {
					FlashMessages::addMessage(I18N::translate('The file %s has been deleted.', e($path)), 'success');
				}
			} else {
				if ($is_dir) {
					FlashMessages::addMessage(I18N::translate('The folder %s could not be deleted.', e($path)), 'danger');
				} else {
					FlashMessages::addMessage(I18N::translate('The file %s could not be deleted.', e($path)), 'danger');
				}
			}
		}

		return new RedirectResponse(route('admin-control-panel'));
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function languagesForm(Request $request): Response {
		$language_tags = [];
		foreach (I18N::activeLocales() as $active_locale) {
			$language_tags[] = $active_locale->languageTag();
		}

		$title = I18N::translate('Languages');

		return $this->viewResponse('admin/site-languages', [
			'language_tags' => $language_tags,
			'title'         => $title,
		]);
	}

	/**
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function languagesSave(Request $request): RedirectResponse {
		Site::setPreference('LANGUAGES', implode(',', $request->get('LANGUAGES', [])));

		FlashMessages::addMessage(I18N::translate('The website preferences have been updated.'), 'success');
		$url = route('admin-control-panel');

		return new RedirectResponse($url);
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function logs(Request $request): Response {
		$earliest = Database::prepare("SELECT IFNULL(DATE(MIN(log_time)), CURDATE()) FROM `##log`")->execute([])->fetchOne();
		$latest   = Database::prepare("SELECT IFNULL(DATE(MAX(log_time)), CURDATE()) FROM `##log`")->execute([])->fetchOne();

		$action   = $request->get('action', '');
		$from     = $request->get('from', $earliest);
		$to       = $request->get('to', $latest);
		$type     = $request->get('type', '');
		$text     = $request->get('text', '');
		$ip       = $request->get('ip', '');
		$username = $request->get('username', '');
		$gedc     = $request->get('gedc');

		$from = max($from, $earliest);
		$to   = min(max($from, $to), $latest);

		$user_options = ['' => ''];
		foreach (User::all() as $tmp_user) {
			$user_options[$tmp_user->getUserName()] = $tmp_user->getUserName();
		}

		$tree_options = ['' => ''] + Tree::getNameList();

		$title = I18N::translate('Website logs');

		return $this->viewResponse('admin/site-logs', [
			'action'       => $action,
			'earliest'     => $earliest,
			'from'         => $from,
			'gedc'         => $gedc,
			'ip'           => $ip,
			'latest'       => $latest,
			'tree_options' => $tree_options,
			'title'        => $title,
			'to'           => $to,
			'text'         => $text,
			'type'         => $type,
			'username'     => $username,
			'user_options' => $user_options,
		]);
	}

	/**
	 * @param Request $request
	 *
	 * @return JsonResponse
	 */
	public function logsData(Request $request): JsonResponse {
		$from     = $request->get('from');
		$to       = $request->get('to');
		$type     = $request->get('type', '');
		$text     = $request->get('text', '');
		$ip       = $request->get('ip', '');
		$username = $request->get('username', '');
		$gedc     = $request->get('gedc');
		$search   = $request->get('search', []);
		$search   = isset($search['value']) ? $search['value'] : '';

		$start  = (int) $request->get('start');
		$length = (int) $request->get('length');
		$order  = $request->get('order', []);
		$draw   = (int) $request->get('draw');

		$sql =
			"SELECT SQL_CACHE SQL_CALC_FOUND_ROWS log_id, log_time, log_type, log_message, ip_address, IFNULL(user_name, '<none>') AS user_name, IFNULL(gedcom_name, '<none>') AS gedcom_name" .
			" FROM `##log`" .
			" LEFT JOIN `##user` USING (user_id)" . // user may be deleted
			" LEFT JOIN `##gedcom` USING (gedcom_id)" . // gedcom may be deleted
			" WHERE 1";

		$args = [];
		if ($search) {
			$sql            .= " AND log_message LIKE CONCAT('%', :search, '%')";
			$args['search'] = $search;
		}
		if ($from) {
			$sql          .= " AND log_time >= :from";
			$args['from'] = $from;
		}
		if ($to) {
			$sql        .= " AND log_time < TIMESTAMPADD(DAY, 1 , :to)"; // before end of the day
			$args['to'] = $to;
		}
		if ($type) {
			$sql          .= " AND log_type = :type";
			$args['type'] = $type;
		}
		if ($text) {
			$sql          .= " AND log_message LIKE CONCAT('%', :text, '%')";
			$args['text'] = $text;
		}
		if ($ip) {
			$sql        .= " AND ip_address LIKE CONCAT('%', :ip, '%')";
			$args['ip'] = $ip;
		}
		if ($username) {
			$sql          .= " AND user_name LIKE CONCAT('%', :user, '%')";
			$args['user'] = $username;
		}
		if ($gedc) {
			$sql          .= " AND gedcom_name = :gedc";
			$args['gedc'] = $gedc;
		}

		if ($order) {
			$sql .= " ORDER BY ";
			foreach ($order as $key => $value) {
				if ($key > 0) {
					$sql .= ',';
				}
				// Columns in datatables are numbered from zero.
				// Columns in MySQL are numbered starting with one.
				switch ($value['dir']) {
					case 'asc':
						$sql .= (1 + $value['column']) . " ASC ";
						break;
					case 'desc':
						$sql .= (1 + $value['column']) . " DESC ";
						break;
				}
			}
		} else {
			$sql .= " ORDER BY 1 ASC";
		}

		if ($length) {
			$sql            .= " LIMIT :limit OFFSET :offset";
			$args['limit']  = $length;
			$args['offset'] = $start;
		}

		// This becomes a JSON list, not array, so need to fetch with numeric keys.
		$data = Database::prepare($sql)->execute($args)->fetchAll(PDO::FETCH_NUM);

		foreach ($data as &$datum) {
			$datum[2] = e($datum[2]);
			$datum[3] = '<span dir="auto">' . e($datum[3]) . '</span>';
			$datum[4] = '<span dir="auto">' . e($datum[4]) . '</span>';
			$datum[5] = '<span dir="auto">' . e($datum[5]) . '</span>';
			$datum[6] = '<span dir="auto">' . e($datum[6]) . '</span>';
		}

		// Total filtered/unfiltered rows
		$recordsFiltered = (int) Database::prepare("SELECT FOUND_ROWS()")->fetchOne();
		$recordsTotal    = (int) Database::prepare("SELECT COUNT(*) FROM `##log`")->fetchOne();

		return new JsonResponse([
			'draw'            => $draw,
			'recordsTotal'    => $recordsTotal,
			'recordsFiltered' => $recordsFiltered,
			'data'            => $data,
		]);
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function logsDelete(Request $request): Response {
		$from     = $request->get('from');
		$to       = $request->get('to');
		$type     = $request->get('type', '');
		$text     = $request->get('text', '');
		$ip       = $request->get('ip', '');
		$username = $request->get('username', '');
		$gedc     = $request->get('gedc');
		$search   = $request->get('search', []);
		$search   = isset($search['value']) ? $search['value'] : '';

		$sql =
			"DELETE `##log` FROM `##log`" .
			" LEFT JOIN `##user` USING (user_id)" . // user may be deleted
			" LEFT JOIN `##gedcom` USING (gedcom_id)" . // gedcom may be deleted
			" WHERE 1";

		$args = [];
		if ($search) {
			$sql            .= " AND log_message LIKE CONCAT('%', :search, '%')";
			$args['search'] = $search;
		}
		if ($from) {
			$sql          .= " AND log_time >= :from";
			$args['from'] = $from;
		}
		if ($to) {
			$sql        .= " AND log_time < TIMESTAMPADD(DAY, 1 , :to)"; // before end of the day
			$args['to'] = $to;
		}
		if ($type) {
			$sql          .= " AND log_type = :type";
			$args['type'] = $type;
		}
		if ($text) {
			$sql          .= " AND log_message LIKE CONCAT('%', :text, '%')";
			$args['text'] = $text;
		}
		if ($ip) {
			$sql        .= " AND ip_address LIKE CONCAT('%', :ip, '%')";
			$args['ip'] = $ip;
		}
		if ($username) {
			$sql          .= " AND user_name LIKE CONCAT('%', :user, '%')";
			$args['user'] = $username;
		}
		if ($gedc) {
			$sql          .= " AND gedcom_name = :gedc";
			$args['gedc'] = $gedc;
		}

		Database::prepare($sql)->execute($args);

		return new Response('');
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function logsExport(Request $request): Response {
		$from     = $request->get('from');
		$to       = $request->get('to');
		$type     = $request->get('type', '');
		$text     = $request->get('text', '');
		$ip       = $request->get('ip', '');
		$username = $request->get('username', '');
		$gedc     = $request->get('gedc');

		$sql =
			"SELECT SQL_CACHE SQL_CALC_FOUND_ROWS log_id, log_time, log_type, log_message, ip_address, IFNULL(user_name, '<none>') AS user_name, IFNULL(gedcom_name, '<none>') AS gedcom_name" .
			" FROM `##log`" .
			" LEFT JOIN `##user` USING (user_id)" . // user may be deleted
			" LEFT JOIN `##gedcom` USING (gedcom_id)" . // gedcom may be deleted
			" WHERE 1";

		$args = [];
		if ($from) {
			$sql          .= " AND log_time >= :from";
			$args['from'] = $from;
		}
		if ($to) {
			$sql        .= " AND log_time < TIMESTAMPADD(DAY, 1 , :to)"; // before end of the day
			$args['to'] = $to;
		}
		if ($type) {
			$sql          .= " AND log_type = :type";
			$args['type'] = $type;
		}
		if ($text) {
			$sql          .= " AND log_message LIKE CONCAT('%', :text, '%')";
			$args['text'] = $text;
		}
		if ($ip) {
			$sql        .= " AND ip_address LIKE CONCAT('%', :ip, '%')";
			$args['ip'] = $ip;
		}
		if ($username) {
			$sql          .= " AND user_name LIKE CONCAT('%', :user, '%')";
			$args['user'] = $username;
		}
		if ($gedc) {
			$sql          .= " AND gedcom_name = :gedc";
			$args['gedc'] = $gedc;
		}

		$sql .= " ORDER BY log_id";

		$rows = Database::prepare($sql )->execute($args)->fetchAll();

		$data = '';

		foreach ($rows as $row) {
			$data .=
				'"' . $row->log_time . '",' .
				'"' . $row->log_type . '",' .
				'"' . str_replace('"', '""', $row->log_message) . '",' .
				'"' . $row->ip_address . '",' .
				'"' . str_replace('"', '""', $row->user_name) . '",' .
				'"' . str_replace('"', '""', $row->gedcom_name) . '"' .
				"\n";
		}

		$response = new Response($data);
		$response->headers->set('Content-Type', 'text/plain');
		$response->headers->set('Content-Disposition', 'attachment; filename="webtrees-logs.csv');

		return $response;
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function mailForm(Request $request): Response {
		$mail_ssl_options       = $this->mailSslOptions();
		$mail_transport_options = $this->mailTransportOptions();

		$title = I18N::translate('Sending email');

		return $this->viewResponse('admin/site-mail', [
			'mail_ssl_options'       => $mail_ssl_options,
			'mail_transport_options' => $mail_transport_options,
			'title'                  => $title,
		]);
	}

	/**
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function mailSave(Request $request): RedirectResponse {
		Site::setPreference('SMTP_ACTIVE', $request->get('SMTP_ACTIVE'));
		Site::setPreference('SMTP_FROM_NAME', $request->get('SMTP_FROM_NAME'));
		Site::setPreference('SMTP_HOST', $request->get('SMTP_HOST'));
		Site::setPreference('SMTP_PORT', $request->get('SMTP_PORT'));
		Site::setPreference('SMTP_AUTH', $request->get('SMTP_AUTH'));
		Site::setPreference('SMTP_AUTH_USER', $request->get('SMTP_AUTH_USER'));
		Site::setPreference('SMTP_SSL', $request->get('SMTP_SSL'));
		Site::setPreference('SMTP_HELO', $request->get('SMTP_HELO'));
		if ($request->get('SMTP_AUTH_PASS', '') !== '') {
			Site::setPreference('SMTP_AUTH_PASS', $request->get('SMTP_AUTH_PASS'));
		}

		FlashMessages::addMessage(I18N::translate('The website preferences have been updated.'), 'success');
		$url = route('admin-control-panel');

		return new RedirectResponse($url);
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function preferencesForm(Request $request): Response {
		$all_themes = Theme::themeNames();

		$title = I18N::translate('Website preferences');

		return $this->viewResponse('admin/site-preferences', [
			'all_themes' => $all_themes,
			'title'      => $title,
		]);
	}

	/**
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function preferencesSave(Request $request): RedirectResponse {
		$INDEX_DIRECTORY = $request->get('INDEX_DIRECTORY');
		if (substr($INDEX_DIRECTORY, -1) !== '/') {
			$INDEX_DIRECTORY .= '/';
		}
		if (File::mkdir($INDEX_DIRECTORY)) {
			Site::setPreference('INDEX_DIRECTORY', $INDEX_DIRECTORY);
		} else {
			FlashMessages::addMessage(I18N::translate('The folder %s does not exist, and it could not be created.', e($INDEX_DIRECTORY)), 'danger');
		}

		Site::setPreference('MEMORY_LIMIT', $request->get('MEMORY_LIMIT'));
		Site::setPreference('MAX_EXECUTION_TIME', (string) (int) $request->get('MAX_EXECUTION_TIME'));
		Site::setPreference('ALLOW_USER_THEMES', (string) (bool) $request->get('ALLOW_USER_THEMES'));
		Site::setPreference('THEME_DIR', $request->get('THEME_DIR'));
		Site::setPreference('ALLOW_CHANGE_GEDCOM', (string) (bool) $request->get('ALLOW_CHANGE_GEDCOM'));
		Site::setPreference('SESSION_TIME', (string) (int) $request->get('SESSION_TIME'));
		Site::setPreference('TIMEZONE', $request->get('TIMEZONE'));

		FlashMessages::addMessage(I18N::translate('The website preferences have been updated.'), 'success');
		$url = route('admin-control-panel');

		return new RedirectResponse($url);
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function registrationForm(Request $request): Response {
		$title = I18N::translate('Sign-in and registration');

		$registration_text_options = $this->registrationTextOptions();

		return $this->viewResponse('admin/site-registration', [
			'registration_text_options' => $registration_text_options,
			'title'                     => $title,
		]);
	}

	/**
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function registrationSave(Request $request): RedirectResponse {
		Site::setPreference('WELCOME_TEXT_AUTH_MODE', $request->get('WELCOME_TEXT_AUTH_MODE'));
		Site::setPreference('WELCOME_TEXT_AUTH_MODE_' . WT_LOCALE, $request->get('WELCOME_TEXT_AUTH_MODE_4'));
		Site::setPreference('USE_REGISTRATION_MODULE', (string) (bool) $request->get('USE_REGISTRATION_MODULE'));
		Site::setPreference('SHOW_REGISTER_CAUTION', (string) (bool) $request->get('SHOW_REGISTER_CAUTION'));

		FlashMessages::addMessage(I18N::translate('The website preferences have been updated.'), 'success');
		$url = route('admin-control-panel');

		return new RedirectResponse($url);
	}

	/**
	 * Show the server information page.
	 *
	 * @return Response
	 */
	public function serverInformation(): Response {
		$mysql_variables = Database::prepare("SHOW VARIABLES")->fetchAssoc();
		$mysql_variables = array_map(function ($text) {
			return str_replace(',', ', ', $text);
		}, $mysql_variables);

		ob_start();
		phpinfo(INFO_ALL & ~INFO_CREDITS & ~INFO_LICENSE);
		$phpinfo = ob_get_clean();
		preg_match('%<body>(.*)</body>%s', $phpinfo, $matches);
		$phpinfo = $matches[1];

		return $this->viewResponse('admin/server-information', [
			'title'           => I18N::translate('Server information'),
			'phpinfo'         => $phpinfo,
			'mysql_variables' => $mysql_variables,
		]);
	}

	/**
	 * A list SSL modes (e.g. for an edit control).
	 *
	 * @return string[]
	 */
	private function mailSslOptions() {
		return [
			'none' => I18N::translate('none'),
			/* I18N: Secure Sockets Layer - a secure communications protocol*/
			'ssl'  => I18N::translate('ssl'),
			/* I18N: Transport Layer Security - a secure communications protocol */
			'tls'  => I18N::translate('tls'),
		];
	}

	/**
	 * A list SSL modes (e.g. for an edit control).
	 *
	 * @return string[]
	 */
	private function mailTransportOptions() {
		$options = [
			'internal' => I18N::translate('Use PHP mail to send messages'),
			'sendmail' => /* I18N: "sendmail" is the name of some mail software */
				I18N::translate('Use sendmail to send messages'),
			'external' => I18N::translate('Use SMTP to send messages'),
		];

		if (!function_exists('proc_open')) {
			unset($options['sendmail']);
		}

		return $options;
	}

	/**
	 * A list of registration rules (e.g. for an edit control).
	 *
	 * @return string[]
	 */
	private function registrationTextOptions() {
		return [
			0 => I18N::translate('No predefined text'),
			1 => I18N::translate('Predefined text that states all users can request a user account'),
			2 => I18N::translate('Predefined text that states admin will decide on each request for a user account'),
			3 => I18N::translate('Predefined text that states only family members can request a user account'),
			4 => I18N::translate('Choose user defined welcome text typed below'),
		];
	}
}
