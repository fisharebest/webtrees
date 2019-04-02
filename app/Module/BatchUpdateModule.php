<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
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
use Illuminate\Database\Capsule\Manager as DB;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class BatchUpdateModule
 */
class BatchUpdateModule extends AbstractModule implements ModuleConfigInterface
{
    use ModuleConfigTrait;

    protected $layout = 'layouts/administration';

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Batch update');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “Batch update” module */
        return I18N::translate('Apply automatic corrections to your genealogy data.');
    }

    /**
     * Main entry point
     *
     * @param ServerRequestInterface $request
     * @param UserInterface          $user
     * @param Tree|null              $tree
     *
     * @return ResponseInterface
     */
    public function getAdminAction(ServerRequestInterface $request, UserInterface $user, Tree $tree = null): ResponseInterface
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
     * Scan the plugin folder for a list of plugins
     *
     * @return BatchUpdateBasePlugin[]
     */
    private function getPluginList(): array
    {
        $plugins = [];
        $files   = glob(__DIR__ . '/BatchUpdate/BatchUpdate*Plugin.php', GLOB_NOSORT);

        foreach ($files as $file) {
            $base_class = basename($file, '.php');

            if ($base_class !== 'BatchUpdateBasePlugin') {
                $class           = __NAMESPACE__ . '\\BatchUpdate\\' . basename($file, '.php');
                $plugins[$class] = new $class();
            }
        }

        return $plugins;
    }

    /**
     * Fetch all records that might need updating.
     *
     * @param BatchUpdateBasePlugin $plugin
     * @param Tree                  $tree
     *
     * @return object[]
     */
    private function allData(BatchUpdateBasePlugin $plugin, Tree $tree): array
    {
        $tmp = [];

        foreach ($plugin->getRecordTypesToUpdate() as $type) {
            switch ($type) {
                case 'INDI':
                    $rows = DB::table('individuals')
                        ->where('i_file', '=', $tree->id())
                        ->select(['i_id AS xref', DB::raw("'INDI' AS type"), 'i_gedcom AS gedcom'])
                        ->get();

                    $tmp = array_merge($tmp, $rows->all());
                    break;

                case 'FAM':
                    $rows = DB::table('families')
                        ->where('f_file', '=', $tree->id())
                        ->select(['f_id AS xref', DB::raw("'FAM' AS type"), 'f_gedcom AS gedcom'])
                        ->get();

                    $tmp = array_merge($tmp, $rows->all());
                    break;

                case 'SOUR':
                    $rows = DB::table('sources')
                        ->where('s_file', '=', $tree->id())
                        ->select(['s_id AS xref', DB::raw("'SOUR' AS type"), 's_gedcom AS gedcom'])
                        ->get();

                    $tmp = array_merge($tmp, $rows->all());
                    break;

                case 'OBJE':
                    $rows = DB::table('media')
                        ->where('m_file', '=', $tree->id())
                        ->select(['m_id AS xref', DB::raw("'OBJE' AS type"), 'm_gedcom AS gedcom'])
                        ->get();

                    $tmp = array_merge($tmp, $rows->all());
                    break;

                default:
                    $rows = DB::table('other')
                        ->where('o_file', '=', $tree->id())
                        ->where('o_type', '=', $type)
                        ->select(['o_id AS xref', 'o_type AS type', 'o_gedcom AS gedcom'])
                        ->get();

                    $tmp = array_merge($tmp, $rows->all());
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
     * @param stdClass $record
     * @param Tree     $tree
     *
     * @return GedcomRecord
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
     * Find the next record that needs to be updated.
     *
     * @param BatchUpdateBasePlugin $plugin
     * @param string                $xref
     * @param array                 $all_data
     * @param Tree                  $tree
     *
     * @return string
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
     * Perform an update
     *
     * @param ServerRequestInterface $request
     * @param UserInterface          $user
     * @param Tree|null              $tree
     *
     * @return ResponseInterface
     */
    public function postAdminAction(ServerRequestInterface $request, UserInterface $user, Tree $tree = null): ResponseInterface
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
        $xref   = $request->getParsedBody()['xref'] ?? '';

        $plugins = $this->getPluginList();
        $plugin  = $plugins[$plugin] ?? null;

        if ($plugin === null) {
            throw new NotFoundHttpException();
        }
        $plugin->getOptions($request);

        $all_data = $this->allData($plugin, $tree);

        $parameters = $request->getParsedBody();
        unset($parameters['csrf']);
        unset($parameters['update']);

        switch ($update) {
            case 'one':
                $record = $this->getRecord($all_data[$xref], $tree);
                if ($plugin->doesRecordNeedUpdate($record)) {
                    $new_gedcom = $plugin->updateRecord($record);
                    $record->updateRecord($new_gedcom, false);
                }

                $parameters['xref'] = $this->findNextXref($plugin, $xref, $all_data, $tree);
                break;

            case 'all':
                foreach ($all_data as $xref => $value) {
                    $record = $this->getRecord($value, $tree);
                    if ($plugin->doesRecordNeedUpdate($record)) {
                        $new_gedcom = $plugin->updateRecord($record);
                        $record->updateRecord($new_gedcom, false);
                    }
                }
                $parameters['xref'] = '';
                break;
        }

        $url = route('module', $parameters);

        return redirect($url);
    }
}
