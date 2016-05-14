<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2016 webtrees development team
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
namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Controller\PageController;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Tree;

/**
 * Class SiteMapModule
 */
class SiteMapModule extends AbstractModule implements ModuleConfigInterface {
	const RECORDS_PER_VOLUME = 500; // Keep sitemap files small, for memory, CPU and max_allowed_packet limits.
	const CACHE_LIFE         = 1209600; // Two weeks

	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module - see http://en.wikipedia.org/wiki/Sitemaps */ I18N::translate('Sitemaps');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “Sitemaps” module */ I18N::translate('Generate sitemap files for search engines.');
	}

	/**
	 * This is a general purpose hook, allowing modules to respond to routes
	 * of the form module.php?mod=FOO&mod_action=BAR
	 *
	 * @param string $mod_action
	 */
	public function modAction($mod_action) {
		switch ($mod_action) {
		case 'admin':
			$this->admin();
			break;
		case 'generate':
			$this->generate(Filter::get('file'));
			break;
		default:
			http_response_code(404);
		}
	}

	/**
	 * Generate an XML file.
	 *
	 * @param string $file
	 */
	private function generate($file) {
		if ($file == 'sitemap.xml') {
			$this->generateIndex();
		} elseif (preg_match('/^sitemap-(\d+)-([isrmn])-(\d+).xml$/', $file, $match)) {
			$this->generateFile($match[1], $match[2], $match[3]);
		} else {
			http_response_code(404);
		}
	}

	/**
	 * The index file contains references to all the other files.
	 * These files are the same for visitors/users/admins.
	 */
	private function generateIndex() {
		// Check the cache
		$timestamp = $this->getSetting('sitemap.timestamp');
		if ($timestamp > WT_TIMESTAMP - self::CACHE_LIFE) {
			$data = $this->getSetting('sitemap.xml');
		} else {
			$data    = '';
			$lastmod = '<lastmod>' . date('Y-m-d') . '</lastmod>';
			foreach (Tree::getAll() as $tree) {
				if ($tree->getPreference('include_in_sitemap')) {
					$n = Database::prepare(
						"SELECT COUNT(*) FROM `##individuals` WHERE i_file = :tree_id"
					)->execute(array('tree_id' => $tree->getTreeId()))->fetchOne();
					for ($i = 0; $i <= $n / self::RECORDS_PER_VOLUME; ++$i) {
						$data .= '<sitemap><loc>' . WT_BASE_URL . 'module.php?mod=' . $this->getName() . '&amp;mod_action=generate&amp;file=sitemap-' . $tree->getTreeId() . '-i-' . $i . '.xml</loc>' . $lastmod . '</sitemap>' . PHP_EOL;
					}
					$n = Database::prepare(
						"SELECT COUNT(*) FROM `##sources` WHERE s_file = :tree_id"
					)->execute(array('tree_id' => $tree->getTreeId()))->fetchOne();
					if ($n) {
						for ($i = 0; $i <= $n / self::RECORDS_PER_VOLUME; ++$i) {
							$data .= '<sitemap><loc>' . WT_BASE_URL . 'module.php?mod=' . $this->getName() . '&amp;mod_action=generate&amp;file=sitemap-' . $tree->getTreeId() . '-s-' . $i . '.xml</loc>' . $lastmod . '</sitemap>' . PHP_EOL;
						}
					}
					$n = Database::prepare(
						"SELECT COUNT(*) FROM `##other` WHERE o_file = :tree_id AND o_type = 'REPO'"
					)->execute(array('tree_id' => $tree->getTreeId()))->fetchOne();
					if ($n) {
						for ($i = 0; $i <= $n / self::RECORDS_PER_VOLUME; ++$i) {
							$data .= '<sitemap><loc>' . WT_BASE_URL . 'module.php?mod=' . $this->getName() . '&amp;mod_action=generate&amp;file=sitemap-' . $tree->getTreeId() . '-r-' . $i . '.xml</loc>' . $lastmod . '</sitemap>' . PHP_EOL;
						}
					}
					$n = Database::prepare(
						"SELECT COUNT(*) FROM `##other` WHERE o_file = :tree_id AND o_type = 'NOTE'"
					)->execute(array('tree_id' => $tree->getTreeId()))->fetchOne();
					if ($n) {
						for ($i = 0; $i <= $n / self::RECORDS_PER_VOLUME; ++$i) {
							$data .= '<sitemap><loc>' . WT_BASE_URL . 'module.php?mod=' . $this->getName() . '&amp;mod_action=generate&amp;file=sitemap-' . $tree->getTreeId() . '-n-' . $i . '.xml</loc>' . $lastmod . '</sitemap>' . PHP_EOL;
						}
					}
					$n = Database::prepare(
						"SELECT COUNT(*) FROM `##media` WHERE m_file = :tree_id"
					)->execute(array('tree_id' => $tree->getTreeId()))->fetchOne();
					if ($n) {
						for ($i = 0; $i <= $n / self::RECORDS_PER_VOLUME; ++$i) {
							$data .= '<sitemap><loc>' . WT_BASE_URL . 'module.php?mod=' . $this->getName() . '&amp;mod_action=generate&amp;file=sitemap-' . $tree->getTreeId() . '-m-' . $i . '.xml</loc>' . $lastmod . '</sitemap>' . PHP_EOL;
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

	/**
	 * A separate file for each family tree and each record type.
	 * These files depend on access levels, so only cache for visitors.
	 *
	 * @param int    $ged_id
	 * @param string $rec_type
	 * @param string $volume
	 */
	private function generateFile($ged_id, $rec_type, $volume) {
		$tree = Tree::findById($ged_id);
		// Check the cache
		$timestamp = $this->getSetting('sitemap-' . $ged_id . '-' . $rec_type . '-' . $volume . '.timestamp');
		if ($timestamp > WT_TIMESTAMP - self::CACHE_LIFE && !Auth::check()) {
			$data = $this->getSetting('sitemap-' . $ged_id . '-' . $rec_type . '-' . $volume . '.xml');
		} else {
			$data    = '<url><loc>' . WT_BASE_URL . 'index.php?ctype=gedcom&amp;ged=' . $tree->getNameUrl() . '</loc></url>' . PHP_EOL;
			$records = array();
			switch ($rec_type) {
			case 'i':
				$rows = Database::prepare(
					"SELECT i_id AS xref, i_gedcom AS gedcom" .
					" FROM `##individuals`" .
					" WHERE i_file = :tree_id" .
					" ORDER BY i_id" .
					" LIMIT :limit OFFSET :offset"
				)->execute(array(
					'tree_id' => $ged_id,
					'limit'   => self::RECORDS_PER_VOLUME,
					'offset'  => self::RECORDS_PER_VOLUME * $volume,
				))->fetchAll();
				foreach ($rows as $row) {
					$records[] = Individual::getInstance($row->xref, $tree, $row->gedcom);
				}
				break;
			case 's':
				$rows = Database::prepare(
					"SELECT s_id AS xref, s_gedcom AS gedcom" .
					" FROM `##sources`" .
					" WHERE s_file = :tree_id" .
					" ORDER BY s_id" .
					" LIMIT :limit OFFSET :offset"
				)->execute(array(
					'tree_id' => $ged_id,
					'limit'   => self::RECORDS_PER_VOLUME,
					'offset'  => self::RECORDS_PER_VOLUME * $volume,
				))->fetchAll();
				foreach ($rows as $row) {
					$records[] = Source::getInstance($row->xref, $tree, $row->gedcom);
				}
				break;
			case 'r':
				$rows = Database::prepare(
					"SELECT o_id AS xref, o_gedcom AS gedcom" .
					" FROM `##other`" .
					" WHERE o_file = :tree_id AND o_type = 'REPO'" .
					" ORDER BY o_id" .
					" LIMIT :limit OFFSET :offset"
				)->execute(array(
					'tree_id' => $ged_id,
					'limit'   => self::RECORDS_PER_VOLUME,
					'offset'  => self::RECORDS_PER_VOLUME * $volume,
				))->fetchAll();
				foreach ($rows as $row) {
					$records[] = Repository::getInstance($row->xref, $tree, $row->gedcom);
				}
				break;
			case 'n':
				$rows = Database::prepare(
					"SELECT o_id AS xref, o_gedcom AS gedcom" .
					" FROM `##other`" .
					" WHERE o_file = :tree_id AND o_type = 'NOTE'" .
					" ORDER BY o_id" .
					" LIMIT :limit OFFSET :offset"
				)->execute(array(
					'tree_id' => $ged_id,
					'limit'   => self::RECORDS_PER_VOLUME,
					'offset'  => self::RECORDS_PER_VOLUME * $volume,
				))->fetchAll();
				foreach ($rows as $row) {
					$records[] = Note::getInstance($row->xref, $tree, $row->gedcom);
				}
				break;
			case 'm':
				$rows = Database::prepare(
					"SELECT m_id AS xref, m_gedcom AS gedcom" .
					" FROM `##media`" .
					" WHERE m_file = :tree_id" .
					" ORDER BY m_id" .
					" LIMIT :limit OFFSET :offset"
				)->execute(array(
					'tree_id' => $ged_id,
					'limit'   => self::RECORDS_PER_VOLUME,
					'offset'  => self::RECORDS_PER_VOLUME * $volume,
				))->fetchAll();
				foreach ($rows as $row) {
					$records[] = Media::getInstance($row->xref, $tree, $row->gedcom);
				}
				break;
			}
			foreach ($records as $record) {
				if ($record->canShowName()) {
					$data .= '<url>';
					$data .= '<loc>' . WT_BASE_URL . $record->getHtmlUrl() . '</loc>';
					$chan = $record->getFirstFact('CHAN');
					if ($chan) {
						$date = $chan->getDate();
						if ($date->isOK()) {
							$data .= '<lastmod>' . $date->minimumDate()->Format('%Y-%m-%d') . '</lastmod>';
						}
					}
					$data .= '</url>' . PHP_EOL;
				}
			}
			$data = '<' . '?xml version="1.0" encoding="UTF-8" ?' . '>' . PHP_EOL . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . PHP_EOL . $data . '</urlset>' . PHP_EOL;
			// Cache this data - but only for visitors, as we don’t want
			// visitors to see data created by signed-in users.
			if (!Auth::check()) {
				$this->setSetting('sitemap-' . $ged_id . '-' . $rec_type . '-' . $volume . '.xml', $data);
				$this->setSetting('sitemap-' . $ged_id . '-' . $rec_type . '-' . $volume . '.timestamp', WT_TIMESTAMP);
			}
		}
		header('Content-Type: application/xml');
		header('Content-Length: ' . strlen($data));
		echo $data;
	}

	/**
	 * Edit the configuration
	 */
	private function admin() {
		$controller = new PageController;
		$controller
			->restrictAccess(Auth::isAdmin())
			->setPageTitle($this->getTitle())
			->pageHeader();

		// Save the updated preferences
		if (Filter::post('action') == 'save') {
			foreach (Tree::getAll() as $tree) {
				$tree->setPreference('include_in_sitemap', Filter::postBool('include' . $tree->getTreeId()));
			}
			// Clear cache and force files to be regenerated
			Database::prepare(
				"DELETE FROM `##module_setting` WHERE setting_name LIKE 'sitemap%'"
			)->execute();
		}

		$include_any = false;

		?>
		<ol class="breadcrumb small">
			<li><a href="admin.php"><?php echo I18N::translate('Control panel'); ?></a></li>
			<li><a href="admin_modules.php"><?php echo I18N::translate('Module administration'); ?></a></li>
			<li class="active"><?php echo $controller->getPageTitle(); ?></li>
		</ol>
		<h1><?php echo $controller->getPageTitle(); ?></h1>
		<?php

		echo
		'<p>',
			/* I18N: The www.sitemaps.org site is translated into many languages (e.g. http://www.sitemaps.org/fr/) - choose an appropriate URL. */
			I18N::translate('Sitemaps are a way for webmasters to tell search engines about the pages on a website that are available for crawling. All major search engines support sitemaps. For more information, see <a href="http://www.sitemaps.org/">www.sitemaps.org</a>.') .
			'</p>',
		'<p>', /* I18N: Label for a configuration option */ I18N::translate('Which family trees should be included in the sitemaps'), '</p>',
			'<form method="post" action="module.php?mod=' . $this->getName() . '&amp;mod_action=admin">',
		'<input type="hidden" name="action" value="save">';
		foreach (Tree::getAll() as $tree) {
			echo '<div class="checkbox"><label><input type="checkbox" name="include', $tree->getTreeId(), '" ';
			if ($tree->getPreference('include_in_sitemap')) {
				echo 'checked';
				$include_any = true;
			}
			echo '>', $tree->getTitleHtml(), '</label></div>';
		}
		echo
		'<input type="submit" value="', I18N::translate('save'), '">',
		'</form>',
		'<hr>';

		if ($include_any) {
			$site_map_url1 = WT_BASE_URL . 'module.php?mod=' . $this->getName() . '&amp;mod_action=generate&amp;file=sitemap.xml';
			$site_map_url2 = rawurlencode(WT_BASE_URL . 'module.php?mod=' . $this->getName() . '&mod_action=generate&file=sitemap.xml');
			echo
				'<p>', I18N::translate('To tell search engines that sitemaps are available, you should add the following line to your robots.txt file.'), '</p>',
				'<pre>Sitemap: ', $site_map_url1, '</pre>',
				'<hr>',
				'<p>', I18N::translate('To tell search engines that sitemaps are available, you can use the following links.'), '</p>',
				'<ul>',
				// This list comes from http://en.wikipedia.org/wiki/Sitemaps
				'<li><a href="http://www.bing.com/webmaster/ping.aspx?siteMap=' . $site_map_url2 . '">Bing</a></li>',
				'<li><a href="http://www.google.com/webmasters/tools/ping?sitemap=' . $site_map_url2 . '">Google</a></li>',
				'</ul>';

		}
	}

	/** {@inheritdoc} */
	public function getConfigLink() {
		return 'module.php?mod=' . $this->getName() . '&amp;mod_action=admin';
	}
}
