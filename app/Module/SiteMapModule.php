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
namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class SiteMapModule
 */
class SiteMapModule extends AbstractModule implements ModuleConfigInterface {
	const RECORDS_PER_VOLUME = 500; // Keep sitemap files small, for memory, CPU and max_allowed_packet limits.
	const CACHE_LIFE         = 1209600; // Two weeks

	/**
	 * How should this module be labelled on tabs, menus, etc.?
	 *
	 * @return string
	 */
	public function getTitle() {
		return /* I18N: Name of a module - see http://en.wikipedia.org/wiki/Sitemaps */
			I18N::translate('Sitemaps');
	}

	/**
	 * A sentence describing what this module does.
	 *
	 * @return string
	 */
	public function getDescription() {
		return /* I18N: Description of the “Sitemaps” module */
			I18N::translate('Generate sitemap files for search engines.');
	}

	/**
	 * The URL to a page where the user can modify the configuration of this module.
	 *
	 * @return string
	 */
	public function getConfigLink() {
		return route('module', ['module' => $this->getName(), 'action' => 'Admin']);
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function getAdminAction(Request $request): Response {
		$this->layout = 'layouts/administration';

		$sitemap_url = route('module', ['module' => 'sitemap', 'action' => 'Index']);

		// This list comes from http://en.wikipedia.org/wiki/Sitemaps
		$submit_urls = [
			'Bing/Yahoo' => Html::url('https://www.bing.com/webmaster/ping.aspx', ['siteMap' => $sitemap_url]),
			'Google'     => Html::url('https://www.google.com/webmasters/tools/ping', ['sitemap' => $sitemap_url]),
		];

		return $this->viewResponse('modules/sitemap/config', [
			'all_trees'   => Tree::getAll(),
			'sitemap_url' => $sitemap_url,
			'submit_urls' => $submit_urls,
			'title'       => $this->getTitle(),
		]);
	}

	/**
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function postAdminAction(Request $request): RedirectResponse {
		foreach (Tree::getAll() as $tree) {
			$include_in_sitemap = (bool) $request->get('sitemap' . $tree->getTreeId());
			$tree->setPreference('include_in_sitemap', (string) $include_in_sitemap);
		}

		FlashMessages::addMessage(I18N::translate('The preferences for the module “%s” have been updated.', $this->getTitle()), 'success');

		return new RedirectResponse($this->getConfigLink());
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function getIndexAction(Request $request): Response {
		$timestamp = (int) $this->getPreference('sitemap.timestamp');

		if ($timestamp > WT_TIMESTAMP - self::CACHE_LIFE) {
			$content = $this->getPreference('sitemap.xml');
		} else {
			$count_individuals = Database::prepare(
				"SELECT i_file, COUNT(*) FROM `##individuals` GROUP BY i_file"
			)->execute()->fetchAssoc();

			$count_media = Database::prepare(
				"SELECT m_file, COUNT(*) FROM `##media` GROUP BY m_file"
			)->execute()->fetchAssoc();

			$count_notes = Database::prepare(
				"SELECT o_file, COUNT(*) FROM `##other` WHERE o_type='NOTE' GROUP BY o_file"
			)->execute()->fetchAssoc();

			$count_repositories = Database::prepare(
				"SELECT o_file, COUNT(*) FROM `##other` WHERE o_type='REPO' GROUP BY o_file"
			)->execute()->fetchAssoc();

			$count_sources = Database::prepare(
				"SELECT s_file, COUNT(*) FROM `##sources` GROUP BY s_file"
			)->execute()->fetchAssoc();

			$content = view('modules/sitemap/sitemap-index.xml', [
				'all_trees'          => Tree::getAll(),
				'count_individuals'  => $count_individuals,
				'count_media'        => $count_media,
				'count_notes'        => $count_notes,
				'count_repositories' => $count_repositories,
				'count_sources'      => $count_sources,
				'last_mod'           => date('Y-m-d'),
				'records_per_volume' => self::RECORDS_PER_VOLUME,
			]);

			$this->setPreference('sitemap.xml', $content);
		}

		return new Response($content, Response::HTTP_OK, [
			'Content-Type' => 'application/xml',
		]);
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function getFileAction(Request $request): Response {
		$file = $request->get('file', '');

		if (!preg_match('/^(\d+)-([imnrs])-(\d+)$/', $file, $match))		 {
			throw new NotFoundHttpException('Bad sitemap file');
		}

		$timestamp = (int) $this->getPreference('sitemap-' . $file . '.timestamp');

		if ($timestamp > WT_TIMESTAMP - self::CACHE_LIFE) {
			$content = $this->getPreference('sitemap-' . $file . '.xml');
		} else {
			$tree = Tree::findById((int) $match[1]);

			if ($tree === null) {
				throw new NotFoundHttpException('No such tree');
			}

			$records = $this->sitemapRecords($tree, $match[2], self::RECORDS_PER_VOLUME,
				self::RECORDS_PER_VOLUME * $match[3]);

			$content = view('modules/sitemap/sitemap-file.xml', ['records' => $records]);

			$this->setPreference('sitemap.xml', $content);
		}

		return new Response($content, Response::HTTP_OK, [
			'Content-Type' => 'application/xml',
		]);
	}

	/**
	 * @param Tree   $tree
	 * @param string $type
	 * @param int    $limit
	 * @param int    $offset
	 *
	 * @return array
	 */
	private function sitemapRecords(Tree $tree, string $type, int $limit, int $offset): array {
		switch ($type) {
			case 'i':
				$records = $this->sitemapIndividuals($tree, $limit, $offset);
				break;

			case 'm':
				$records = $this->sitemapMedia($tree, $limit, $offset);
				break;

			case 'n':
				$records = $this->sitemapNotes($tree, $limit, $offset);
				break;

			case 'r':
				$records = $this->sitemapRepositories($tree, $limit, $offset);
				break;

			case 's':
				$records = $this->sitemapSources($tree, $limit, $offset);
				break;

			default:
				throw new NotFoundHttpException('Invalid record type: ' . $type);
		}

		// Skip records that no longer exist.
		$records = array_filter($records);

		// Skip private records.
		$records = array_filter($records, function (GedcomRecord $record) {
			return $record->canShow();
		});

		return $records;
	}

	/**
	 * @param Tree $tree
	 * @param int  $limit
	 * @param int  $offset
	 *
	 * @return array
	 */
	private function sitemapIndividuals(Tree $tree, int $limit, int $offset): array {
		$rows = Database::prepare(
			"SELECT i_id AS xref, i_gedcom AS gedcom" .
			" FROM `##individuals`" .
			" WHERE i_file = :tree_id" .
			" ORDER BY i_id" .
			" LIMIT :limit OFFSET :offset"
		)->execute([
			'tree_id' => $tree->getTreeId(),
			'limit'   => $limit,
			'offset'  => $offset,
		])->fetchAll();

		$records = [];

		foreach ($rows as $row) {
			$records[] = Individual::getInstance($row->xref, $tree, $row->gedcom);
		}

		return $records;
	}

	/**
	 * @param Tree $tree
	 * @param int  $limit
	 * @param int  $offset
	 *
	 * @return array
	 */
	private function sitemapMedia(Tree $tree, int $limit, int $offset): array {
		$rows = Database::prepare(
			"SELECT m_id AS xref, m_gedcom AS gedcom" .
			" FROM `##media`" .
			" WHERE m_file = :tree_id" .
			" ORDER BY m_id" .
			" LIMIT :limit OFFSET :offset"
		)->execute([
			'tree_id' => $tree->getTreeId(),
			'limit'   => $limit,
			'offset'  => $offset,
		])->fetchAll();

		$records = [];

		foreach ($rows as $row) {
			$records[] = Media::getInstance($row->xref, $tree, $row->gedcom);
		}

		return $records;
	}

	/**
	 * @param Tree $tree
	 * @param int  $limit
	 * @param int  $offset
	 *
	 * @return array
	 */
	private function sitemapNotes(Tree $tree, int $limit, int $offset): array {
		$rows = Database::prepare(
			"SELECT o_id AS xref, o_gedcom AS gedcom" .
			" FROM `##other`" .
			" WHERE o_file = :tree_id AND o_type = 'NOTE'" .
			" ORDER BY o_id" .
			" LIMIT :limit OFFSET :offset"
		)->execute([
			'tree_id' => $tree->getTreeId(),
			'limit'   => $limit,
			'offset'  => $offset,
		])->fetchAll();

		$records = [];

		foreach ($rows as $row) {
			$records[] = Note::getInstance($row->xref, $tree, $row->gedcom);
		}

		return $records;
	}

	/**
	 * @param Tree $tree
	 * @param int  $limit
	 * @param int  $offset
	 *
	 * @return array
	 */
	private function sitemapRepositories(Tree $tree, int $limit, int $offset): array {
		$rows = Database::prepare(
			"SELECT o_id AS xref, o_gedcom AS gedcom" .
			" FROM `##other`" .
			" WHERE o_file = :tree_id AND o_type = 'REPO'" .
			" ORDER BY o_id" .
			" LIMIT :limit OFFSET :offset"
		)->execute([
			'tree_id' => $tree->getTreeId(),
			'limit'   => $limit,
			'offset'  => $offset,
		])->fetchAll();

		$records = [];

		foreach ($rows as $row) {
			$records[] = Repository::getInstance($row->xref, $tree, $row->gedcom);
		}

		return $records;
	}

	/**
	 * @param Tree $tree
	 * @param int  $limit
	 * @param int  $offset
	 *
	 * @return array
	 */
	private function sitemapSources(Tree $tree, int $limit, int $offset): array {
		$rows = Database::prepare(
			"SELECT s_id AS xref, s_gedcom AS gedcom" .
			" FROM `##sources`" .
			" WHERE s_file = :tree_id" .
			" ORDER BY s_id" .
			" LIMIT :limit OFFSET :offset"
		)->execute([
			'tree_id' => $tree->getTreeId(),
			'limit'   => $limit,
			'offset'  => $offset,
		])->fetchAll();

		$records = [];

		foreach ($rows as $row) {
			$records[] = Source::getInstance($row->xref, $tree, $row->gedcom);
		}

		return $records;
	}
}
