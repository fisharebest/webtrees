<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

use WT\Auth;

class sitemap_WT_Module extends WT_Module implements WT_Module_Config {
	const RECORDS_PER_VOLUME = 500;    // Keep sitemap files small, for memory, CPU and max_allowed_packet limits.
	const CACHE_LIFE = 1209600; // Two weeks

	// Extend WT_Module
	public function getTitle() {
		return /* I18N: Name of a module - see http://en.wikipedia.org/wiki/Sitemaps */ WT_I18N::translate('Sitemaps');
	}

	// Extend WT_Module
	public function getDescription() {
		return /* I18N: Description of the “Sitemaps” module */ WT_I18N::translate('Generate sitemap files for search engines.');
	}

	// Extend WT_Module
	public function modAction($mod_action) {
		switch ($mod_action) {
		case 'admin':
			$this->admin();
			break;
		case 'generate':
			Zend_Session::writeClose();
			$this->generate(WT_Filter::get('file'));
			break;
		default:
			header('HTTP/1.0 404 Not Found');
		}
	}

	private function generate($file) {
		if ($file == 'sitemap.xml') {
			$this->generateIndex();
		} elseif (preg_match('/^sitemap-(\d+)-([isrmn])-(\d+).xml$/', $file, $match)) {
			$this->generateFile($match[1], $match[2], $match[3]);
		} else {
			header('HTTP/1.0 404 Not Found');
		}
	}

	// The index file contains references to all the other files.
	// These files are the same for visitors/users/admins.
	private function generateIndex() {
		// Check the cache
		$timestamp = $this->getSetting('sitemap.timestamp');
		if ($timestamp > WT_TIMESTAMP - self::CACHE_LIFE) {
			$data = $this->getSetting('sitemap.xml');
		} else {
			$data = '';
			$lastmod = '<lastmod>' . date('Y-m-d') . '</lastmod>';
			foreach (WT_Tree::getAll() as $tree) {
				if ($tree->getPreference('include_in_sitemap')) {
					$n = WT_DB::prepare("SELECT COUNT(*) FROM `##individuals` WHERE i_file=?")->execute(array($tree->tree_id))->fetchOne();
					for ($i = 0; $i <= $n / self::RECORDS_PER_VOLUME; ++$i) {
						$data .= '<sitemap><loc>' . WT_SERVER_NAME . WT_SCRIPT_PATH . 'module.php?mod=' . $this->getName() . '&amp;mod_action=generate&amp;file=sitemap-' . $tree->tree_id . '-i-' . $i . '.xml</loc>' . $lastmod . '</sitemap>' . PHP_EOL;
					}
					$n = WT_DB::prepare("SELECT COUNT(*) FROM `##sources` WHERE s_file=?")->execute(array($tree->tree_id))->fetchOne();
					if ($n) {
						for ($i = 0; $i <= $n / self::RECORDS_PER_VOLUME; ++$i) {
							$data .= '<sitemap><loc>' . WT_SERVER_NAME . WT_SCRIPT_PATH . 'module.php?mod=' . $this->getName() . '&amp;mod_action=generate&amp;file=sitemap-' . $tree->tree_id . '-s-' . $i . '.xml</loc>' . $lastmod . '</sitemap>' . PHP_EOL;
						}
					}
					$n = WT_DB::prepare("SELECT COUNT(*) FROM `##other` WHERE o_file=? AND o_type='REPO'")->execute(array($tree->tree_id))->fetchOne();
					if ($n) {
						for ($i = 0; $i <= $n / self::RECORDS_PER_VOLUME; ++$i) {
							$data .= '<sitemap><loc>' . WT_SERVER_NAME . WT_SCRIPT_PATH . 'module.php?mod=' . $this->getName() . '&amp;mod_action=generate&amp;file=sitemap-' . $tree->tree_id . '-r-' . $i . '.xml</loc>' . $lastmod . '</sitemap>' . PHP_EOL;
						}
					}
					$n = WT_DB::prepare("SELECT COUNT(*) FROM `##other` WHERE o_file=? AND o_type='NOTE'")->execute(array($tree->tree_id))->fetchOne();
					if ($n) {
						for ($i = 0; $i <= $n / self::RECORDS_PER_VOLUME; ++$i) {
							$data .= '<sitemap><loc>' . WT_SERVER_NAME . WT_SCRIPT_PATH . 'module.php?mod=' . $this->getName() . '&amp;mod_action=generate&amp;file=sitemap-' . $tree->tree_id . '-n-' . $i . '.xml</loc>' . $lastmod . '</sitemap>' . PHP_EOL;
						}
					}
					$n = WT_DB::prepare("SELECT COUNT(*) FROM `##media` WHERE m_file=?")->execute(array($tree->tree_id))->fetchOne();
					if ($n) {
						for ($i = 0; $i <= $n / self::RECORDS_PER_VOLUME; ++$i) {
							$data .= '<sitemap><loc>' . WT_SERVER_NAME . WT_SCRIPT_PATH . 'module.php?mod=' . $this->getName() . '&amp;mod_action=generate&amp;file=sitemap-' . $tree->tree_id . '-m-' . $i . '.xml</loc>' . $lastmod . '</sitemap>' . PHP_EOL;
						}
					}
				}
			}
			$data = '<' . '?xml version="1.0" encoding="UTF-8" ?' . '>' . PHP_EOL . '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL . $data . '</sitemapindex>' . PHP_EOL;
			// Cache this data.
			$this->setSetting('sitemap.xml', $data);
			$this->setSetting('sitemap.timestamp', WT_TIMESTAMP);
		}
		header('Content-Type: application/xml');
		header('Content-Length: ' . strlen($data));
		echo $data;
	}

	// A separate file for each family tree and each record type.
	// These files depend on access levels, so only cache for visitors.
	private function generateFile($ged_id, $rec_type, $volume) {
		// Check the cache
		$timestamp = $this->getSetting('sitemap-' . $ged_id . '-' . $rec_type . '-' . $volume . '.timestamp');
		if ($timestamp > WT_TIMESTAMP - self::CACHE_LIFE && !Auth::check()) {
			$data = $this->getSetting('sitemap-' . $ged_id . '-' . $rec_type . '-' . $volume . '.xml');
		} else {
			$tree = WT_Tree::get($ged_id);
			$data = '<url><loc>' . WT_SERVER_NAME . WT_SCRIPT_PATH . 'index.php?ctype=gedcom&amp;ged=' . $tree->tree_name_url . '</loc></url>' . PHP_EOL;
			$records = array();
			switch ($rec_type) {
			case 'i':
				$rows = WT_DB::prepare(
					"SELECT i_id AS xref, i_file AS gedcom_id, i_gedcom AS gedcom" .
					" FROM `##individuals`" .
					" WHERE i_file=?" .
					" ORDER BY i_id" .
					" LIMIT " . self::RECORDS_PER_VOLUME . " OFFSET " . ($volume * self::RECORDS_PER_VOLUME)
				)->execute(array($ged_id))->fetchAll();
				foreach ($rows as $row) {
					$records[] = WT_Individual::getInstance($row->xref, $row->gedcom_id, $row->gedcom);
				}
				break;
			case 's':
				$rows = WT_DB::prepare(
					"SELECT s_id AS xref, s_file AS gedcom_id, s_gedcom AS gedcom" .
					" FROM `##sources`" .
					" WHERE s_file=?" .
					" ORDER BY s_id" .
					" LIMIT " . self::RECORDS_PER_VOLUME . " OFFSET " . ($volume * self::RECORDS_PER_VOLUME)
				)->execute(array($ged_id))->fetchAll();
				foreach ($rows as $row) {
					$records[] = WT_Source::getInstance($row->xref, $row->gedcom_id, $row->gedcom);
				}
				break;
			case 'r':
				$rows = WT_DB::prepare(
					"SELECT o_id AS xref, o_file AS gedcom_id, o_gedcom AS gedcom" .
					" FROM `##other`" .
					" WHERE o_file=? AND o_type='REPO'" .
					" ORDER BY o_id" .
					" LIMIT " . self::RECORDS_PER_VOLUME . " OFFSET " . ($volume * self::RECORDS_PER_VOLUME)
				)->execute(array($ged_id))->fetchAll();
				foreach ($rows as $row) {
					$records[] = WT_Repository::getInstance($row->xref, $row->gedcom_id, $row->gedcom);
				}
				break;
			case 'n':
				$rows = WT_DB::prepare(
					"SELECT o_id AS xref, o_file AS gedcom_id, o_gedcom AS gedcom" .
					" FROM `##other`" .
					" WHERE o_file=? AND o_type='NOTE'" .
					" ORDER BY o_id" .
					" LIMIT " . self::RECORDS_PER_VOLUME . " OFFSET " . ($volume * self::RECORDS_PER_VOLUME)
				)->execute(array($ged_id))->fetchAll();
				foreach ($rows as $row) {
					$records[] = WT_Note::getInstance($row->xref, $row->gedcom_id, $row->gedcom);
				}
				break;
			case 'm':
				$rows = WT_DB::prepare(
					"SELECT m_id AS xref, m_file AS gedcom_id, m_gedcom AS gedcom" .
					" FROM `##media`" .
					" WHERE m_file=?" .
					" ORDER BY m_id" .
					" LIMIT " . self::RECORDS_PER_VOLUME . " OFFSET " . ($volume * self::RECORDS_PER_VOLUME)
				)->execute(array($ged_id))->fetchAll();
				foreach ($rows as $row) {
					$records[] = WT_Media::getInstance($row->xref, $row->gedcom_id, $row->gedcom);
				}
				break;
			}
			foreach ($records as $record) {
				if ($record->canShowName()) {
					$data .= '<url>';
					$data .= '<loc>' . WT_SERVER_NAME . WT_SCRIPT_PATH . $record->getHtmlUrl() . '</loc>';
					$chan = $record->getFirstFact('CHAN');
					if ($chan) {
						$date = $chan->getDate();
						if ($date->isOK()) {
							$data .= '<lastmod>' . $date->minDate()->Format('%Y-%m-%d') . '</lastmod>';
						}
					}
					$data .= '</url>' . PHP_EOL;
				}
			}
			$data = '<' . '?xml version="1.0" encoding="UTF-8" ?' . '>' . PHP_EOL . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . PHP_EOL . $data . '</urlset>' . PHP_EOL;
			// Cache this data - but only for visitors, as we don’t want
			// visitors to see data created by logged-in users.
			if (!Auth::check()) {
				$this->setSetting('sitemap-' . $ged_id . '-' . $rec_type . '-' . $volume . '.xml', $data);
				$this->setSetting('sitemap-' . $ged_id . '-' . $rec_type . '-' . $volume . '.timestamp', WT_TIMESTAMP);
			}
		}
		header('Content-Type: application/xml');
		header('Content-Length: ' . strlen($data));
		echo $data;
	}

	private function admin() {
		$controller = new WT_Controller_Page();
		$controller
			->restrictAccess(Auth::isAdmin())
			->setPageTitle($this->getTitle())
			->pageHeader();

		// Save the updated preferences
		if (WT_Filter::post('action') == 'save') {
			foreach (WT_Tree::getAll() as $tree) {
				$tree->setPreference('include_in_sitemap', WT_Filter::postBool('include' . $tree->tree_id));
			}
			// Clear cache and force files to be regenerated
			WT_DB::prepare(
				"DELETE FROM `##module_setting` WHERE setting_name LIKE 'sitemap%'"
			)->execute();
		}

		$include_any = false;
		echo
		'<h3>', $this->getTitle(), '</h3>',
		'<p>',
			/* I18N: The www.sitemaps.org site is translated into many languages (e.g. http://www.sitemaps.org/fr/) - choose an appropriate URL. */
			WT_I18N::translate('Sitemaps are a way for webmasters to tell search engines about the pages on a website that are available for crawling.  All major search engines support sitemaps.  For more information, see <a href="http://www.sitemaps.org/">www.sitemaps.org</a>.') .
			'</p>',
		'<p>', WT_I18N::translate('Which family trees should be included in the sitemaps?'), '</p>',
			'<form method="post" action="module.php?mod=' . $this->getName() . '&amp;mod_action=admin">',
		'<input type="hidden" name="action" value="save">';
		foreach (WT_Tree::getAll() as $tree) {
			echo '<p><input type="checkbox" name="include', $tree->tree_id, '"';
			if ($tree->getPreference('include_in_sitemap')) {
				echo ' checked="checked"';
				$include_any = true;
			}
			echo '>', $tree->tree_title_html, '</p>';
		}
		echo
		'<input type="submit" value="', WT_I18N::translate('save'), '">',
		'</form>',
		'<hr>';

		if ($include_any) {
			$site_map_url1 = WT_SERVER_NAME . WT_SCRIPT_PATH . 'module.php?mod=' . $this->getName() . '&amp;mod_action=generate&amp;file=sitemap.xml';
			$site_map_url2 = rawurlencode(WT_SERVER_NAME . WT_SCRIPT_PATH . 'module.php?mod=' . $this->getName() . '&mod_action=generate&file=sitemap.xml');
			echo
				'<p>', WT_I18N::translate('To tell search engines that sitemaps are available, you should add the following line to your robots.txt file.'), '</p>',
				'<pre>Sitemap: ', $site_map_url1, '</pre>',
				'<hr>',
				'<p>', WT_I18N::translate('To tell search engines that sitemaps are available, you can use the following links.'), '</p>',
				'<ul>',
				// This list comes from http://en.wikipedia.org/wiki/Sitemaps
				'<li><a target="_blank" href="http://www.bing.com/webmaster/ping.aspx?siteMap=' . $site_map_url2 . '">Bing</a></li>',
				'<li><a target="_blank" href="http://www.google.com/webmasters/tools/ping?sitemap=' . $site_map_url2 . '">Google</a></li>',
				'</ul>';

		}
	}

	// Implement WT_Module_Config
	public function getConfigLink() {
		return 'module.php?mod=' . $this->getName() . '&amp;mod_action=admin';
	}
}
