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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Module\BatchUpdate\BatchUpdateBasePlugin;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use stdClass;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class BatchUpdateModule
 */
class BatchUpdateModule extends AbstractModule implements ModuleConfigInterface
{
    protected $layout = 'layouts/administration';

    /**
     * How should this module be labelled on tabs, menus, etc.?
     *
     * @return string
     */
    public function getTitle(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Batch update');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function getDescription(): string
    {
        /* I18N: Description of the “Batch update” module */
        return I18N::translate('Apply automatic corrections to your genealogy data.');
    }

    /**
     * Main entry point
     *
     * @param Request   $request
     * @param User      $user
     * @param Tree|null $tree
     *
     * @return Response
     * @throws \Exception
     */
    public function getAdminAction(Request $request, User $user, Tree $tree = null): Response
    {
        // We need a tree to work with.
        if ($tree === null) {
            throw new NotFoundHttpException();
        }

        // Admins only.
        if (!Auth::isAdmin($user)) {
            throw new AccessDeniedHttpException();
        }

        $plugin = $request->get('plugin', '');
        $xref   = $request->get('xref', '');

        $plugins = $this->getPluginList();
        $plugin  = $plugins[$plugin] ?? null;

        $curr_xref = '';
        $prev_xref = '';
        $next_xref = '';

        // Don't do any processing until a plugin is chosen.
        if ($plugin !== null) {
            $plugin->getOptions($request);

            $all_data = $this->allData($plugin, $tree);

            // Make sure that our requested record really does need updating.
            // It may have been updated in another session, or may not have
            // been specified at all.
            if (array_key_exists($xref, $all_data) && $plugin->doesRecordNeedUpdate($this->getRecord($all_data[$xref], $tree))) {
                $curr_xref = $xref;
            }

            // The requested record doesn't need updating - find one that does
            if ($curr_xref === '') {
                $curr_xref = $this->findNextXref($plugin, $xref, $all_data, $tree);
            }
            if ($curr_xref === '') {
                $curr_xref = $this->findPrevXref($plugin, $xref, $all_data, $tree);
            }

            // If we've found a record to update, get details and look for the next/prev
            if ($curr_xref !== '') {
                $prev_xref = $this->findPrevXref($plugin, $xref, $all_data, $tree);
                $next_xref = $this->findNextXref($plugin, $xref, $all_data, $tree);
            }
        }

        return $this->viewResponse('modules/batch_update/admin', [
            'auto_accept' => (bool) $user->getPreference('auto_accept'),
            'plugins'     => $plugins,
            'curr_xref'   => $curr_xref,
            'next_xref'   => $next_xref,
            'plugin'      => $plugin,
            'record'      => GedcomRecord::getInstance($curr_xref, $tree),
            'prev_xref'   => $prev_xref,
            'title'       => I18N::translate('Batch update'),
            'tree'        => $tree,
            'trees'       => Tree::getNameList(),
        ]);
    }

    /**
     * Perform an update
     *
     * @param Request   $request
     * @param User      $user
     * @param Tree|null $tree
     *
     * @return RedirectResponse
     * @throws \Exception
     */
    public function postAdminAction(Request $request, User $user, Tree $tree = null): RedirectResponse
    {
        // We need a tree to work with.
        if ($tree === null) {
            throw new NotFoundHttpException();
        }

        // Admins only.
        if (!Auth::isAdmin($user)) {
            throw new AccessDeniedHttpException();
        }

        $plugin = $request->get('plugin', '');
        $update = $request->get('update', '');
        $xref   = $request->request->get('xref', '');

        $plugins = $this->getPluginList();
        $plugin  = $plugins[$plugin] ?? null;

        if ($plugin === null) {
            throw new NotFoundHttpException();
        }
        $plugin->getOptions($request);

        $all_data = $this->allData($plugin, $tree);

        $parameters = $request->request->all();
        unset($parameters['csrf']);
        unset($parameters['update']);

        switch ($update) {
            case 'one':
                $record = $this->getRecord($all_data[$xref], $tree);
                if ($plugin->doesRecordNeedUpdate($record)) {
                    $new_gedcom = $plugin->updateRecord($record);
                    $record->updateRecord($new_gedcom, !$plugin->chan);
                }

                $parameters['xref'] = $this->findNextXref($plugin, $xref, $all_data, $tree);
                break;

            case 'all':
                foreach ($all_data as $xref => $value) {
                    $record = $this->getRecord($value, $tree);
                    if ($plugin->doesRecordNeedUpdate($record)) {
                        $new_gedcom = $plugin->updateRecord($record);
                        $record->updateRecord($new_gedcom, !$plugin->chan);
                    }
                }
                $parameters['xref'] = '';
                break;
        }

        $url = route('module', $parameters);

        return new RedirectResponse($url);
    }


    /**
     * Find the next record that needs to be updated.
     *
     * @param BatchUpdateBasePlugin $plugin
     * @param string                $xref
     * @param array                 $all_data
     * @param Tree                  $tree
     *
     * @return string
     * @throws \Exception
     */
    private function findNextXref(BatchUpdateBasePlugin $plugin, string $xref, array $all_data, Tree $tree): string
    {
        foreach (array_keys($all_data) as $key) {
            if ($key > $xref) {
                $record = $this->getRecord($all_data[$key], $tree);
                if ($plugin->doesRecordNeedUpdate($record)) {
                    return $key;
                }
            }
        }

        return '';
    }

    /**
     * Find the previous record that needs to be updated.
     *
     * @param BatchUpdateBasePlugin $plugin
     * @param string                $xref
     * @param array                 $all_data
     * @param Tree                  $tree
     *
     * @return string
     * @throws \Exception
     */
    private function findPrevXref(BatchUpdateBasePlugin $plugin, string $xref, array $all_data, Tree $tree): string
    {
        foreach (array_reverse($all_data) as $key => $value) {
            if ($key > $xref) {
                $record = $this->getRecord($all_data[$key], $tree);
                if ($plugin->doesRecordNeedUpdate($record)) {
                    return $key;
                }
            }
        }

        return '';
    }

    /**
     * Fetch all records that might need updating.
     *
     * @param BatchUpdateBasePlugin $plugin
     * @param Tree                  $tree
     *
     * @return object[]
     * @throws \Exception
     */
    private function allData(BatchUpdateBasePlugin $plugin, Tree $tree): array
    {
        $tmp = [];

        foreach ($plugin->getRecordTypesToUpdate() as $type) {
            switch ($type) {
                case 'INDI':
                    $tmp += Database::prepare(
                        "SELECT i_id AS xref, 'INDI' AS type, i_gedcom AS gedcom  FROM `##individuals` WHERE i_file = :tree_id"
                    )->execute([
                        'tree_id' => $tree->getTreeId(),
                    ])->fetchAll();
                    break;

                case 'FAM':
                    $tmp += Database::prepare(
                        "SELECT f_id AS xref, 'FAM' AS type, f_gedcom AS gedcom  FROM `##families` WHERE f_file = :tree_id"
                    )->execute([
                        'tree_id' => $tree->getTreeId(),
                    ])->fetchAll();
                    break;

                case 'SOUR':
                    $tmp += Database::prepare(
                        "SELECT s_id AS xref, 'SOUR' AS type, s_gedcom AS gedcom  FROM `##sources` WHERE s_file = :tree_id"
                    )->execute([
                        'tree_id' => $tree->getTreeId(),
                    ])->fetchAll();
                    break;

                case 'OBJE':
                    $tmp += Database::prepare(
                        "SELECT m_id AS xref, 'OBJE' AS type, m_gedcom AS gedcom  FROM `##media` WHERE m_file = :tree_id"
                    )->execute([
                        'tree_id' => $tree->getTreeId(),
                    ])->fetchAll();
                    break;

                default:
                    $tmp += Database::prepare(
                        "SELECT o_id AS xref, 'OBJE' AS type, o_gedcom AS gedcom  FROM `##other` WHERE o_file = :tree_id AND o_type = :type"
                    )->execute([
                        'tree_id' => $tree->getTreeId(),
                        'type'    => $type,
                    ])->fetchAll();
                    break;
            }
        }

        $data = [];

        foreach ($tmp as $value) {
            $data[$value->xref] = $value;
        }

        ksort($tmp);

        return $data;
    }

    /**
     * Scan the plugin folder for a list of plugins
     *
     * @return BatchUpdateBasePlugin[]
     */
    private function getPluginList(): array
    {
        $plugins = [];

        $dir_handle = opendir(__DIR__ . '/BatchUpdate');
        while (($file = readdir($dir_handle)) !== false) {
            if (substr($file, -10) == 'Plugin.php' && $file !== 'BatchUpdateBasePlugin.php') {
                $class           = '\Fisharebest\Webtrees\Module\BatchUpdate\\' . basename($file, '.php');
                $plugins[$class] = new $class();
            }
        }
        closedir($dir_handle);

        return $plugins;
    }

    /**
     * @param stdClass $record
     * @param Tree     $tree
     *
     * @return GedcomRecord
     * @throws \Exception
     */
    public function getRecord(stdClass $record, Tree $tree): GedcomRecord
    {
        switch ($record->type) {
            case 'INDI':
                return Individual::getInstance($record->xref, $tree, $record->gedcom);

            case 'FAM':
                return Family::getInstance($record->xref, $tree, $record->gedcom);

            case 'SOUR':
                return Source::getInstance($record->xref, $tree, $record->gedcom);

            case 'REPO':
                return Repository::getInstance($record->xref, $tree, $record->gedcom);

            case 'OBJE':
                return Media::getInstance($record->xref, $tree, $record->gedcom);

            case 'NOTE':
                return Note::getInstance($record->xref, $tree, $record->gedcom);

            default:
                return GedcomRecord::getInstance($record->xref, $tree, $record->gedcom);
        }
    }

    /**
     * The URL to a page where the user can modify the configuration of this module.
     * These links are displayed in the admin page menu.
     *
     * @return string
     */
    public function getConfigLink(): string
    {
        return route('module', [
            'module' => $this->getName(),
            'action' => 'Admin',
        ]);
    }
}
