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

namespace Fisharebest\Webtrees\Http\Controllers;

use Carbon\Carbon;
use FilesystemIterator;
use Fisharebest\Algorithm\MyersDiff;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Functions\FunctionsImport;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Module\ModuleAnalyticsInterface;
use Fisharebest\Webtrees\Module\ModuleBlockInterface;
use Fisharebest\Webtrees\Module\ModuleChartInterface;
use Fisharebest\Webtrees\Module\ModuleConfigInterface;
use Fisharebest\Webtrees\Module\ModuleFooterInterface;
use Fisharebest\Webtrees\Module\ModuleHistoricEventsInterface;
use Fisharebest\Webtrees\Module\ModuleLanguageInterface;
use Fisharebest\Webtrees\Module\ModuleMenuInterface;
use Fisharebest\Webtrees\Module\ModuleReportInterface;
use Fisharebest\Webtrees\Module\ModuleSidebarInterface;
use Fisharebest\Webtrees\Module\ModuleTabInterface;
use Fisharebest\Webtrees\Module\ModuleThemeInterface;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Services\DatatablesService;
use Fisharebest\Webtrees\Services\HousekeepingService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\UpgradeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Intervention\Image\ImageManager;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use stdClass;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Throwable;
use const WT_DATA_DIR;

/**
 * Controller for the administration pages
 */
class AdminController extends AbstractBaseController
{
    /** @var string */
    protected $layout = 'layouts/administration';

    /**
     * The control panel shows a summary of the site and links to admin functions.
     *
     * @param HousekeepingService    $housekeeping_service
     * @param UpgradeService         $upgrade_service
     * @param Admin\ModuleController $module_controller
     * @param ModuleService          $module_service
     * @param UserService            $user_service
     *
     * @return Response
     */
    public function controlPanel(
        HousekeepingService $housekeeping_service,
        UpgradeService $upgrade_service,
        Admin\ModuleController $module_controller,
        ModuleService $module_service,
        UserService $user_service
    ): Response
    {
        $filesystem      = new Filesystem(new Local(WT_ROOT));
        $files_to_delete = $housekeeping_service->deleteOldWebtreesFiles($filesystem);
        $deleted_modules = $module_controller->deletedModuleNames();

        // Analytics modules have their own configl so don't show them twice.
        $config_modules = $module_service->findByInterface(ModuleConfigInterface::class, true)
            ->filter(function (ModuleConfigInterface $module): bool {
                return !$module instanceof ModuleAnalyticsInterface;
            });

        return $this->viewResponse('admin/control-panel', [
            'title'             => I18N::translate('Control panel'),
            'server_warnings'   => $this->serverWarnings(),
            'latest_version'    => $upgrade_service->latestVersion(),
            'all_users'         => $user_service->all(),
            'administrators'    => $user_service->administrators(),
            'managers'          => $user_service->managers(),
            'moderators'        => $user_service->moderators(),
            'unapproved'        => $user_service->unapproved(),
            'unverified'        => $user_service->unverified(),
            'all_trees'         => Tree::getAll(),
            'changes'           => $this->totalChanges(),
            'individuals'       => $this->totalIndividuals(),
            'families'          => $this->totalFamilies(),
            'sources'           => $this->totalSources(),
            'media'             => $this->totalMediaObjects(),
            'repositories'      => $this->totalRepositories(),
            'notes'             => $this->totalNotes(),
            'files_to_delete'   => $files_to_delete,
            'all_modules'       => $module_service->all(),
            'deleted_modules'   => $deleted_modules,
            'analytics_modules' => $module_service->findByInterface(ModuleAnalyticsInterface::class, true),
            'block_modules'     => $module_service->findByInterface(ModuleBlockInterface::class, true),
            'chart_modules'     => $module_service->findByInterface(ModuleChartInterface::class, true),
            'config_modules'    => $config_modules,
            'footer_modules'    => $module_service->findByInterface(ModuleFooterInterface::class, true),
            'history_modules'   => $module_service->findByInterface(ModuleHistoricEventsInterface::class, true),
            'language_modules'  => $module_service->findByInterface(ModuleLanguageInterface::class, true),
            'menu_modules'      => $module_service->findByInterface(ModuleMenuInterface::class, true),
            'report_modules'    => $module_service->findByInterface(ModuleReportInterface::class, true),
            'sidebar_modules'   => $module_service->findByInterface(ModuleSidebarInterface::class, true),
            'tab_modules'       => $module_service->findByInterface(ModuleTabInterface::class, true),
            'theme_modules'     => $module_service->findByInterface(ModuleThemeInterface::class, true),
        ]);
    }

    /**
     * Managers see a restricted version of the contol panel.
     *
     * @return Response
     */
    public function controlPanelManager(): Response
    {
        $all_trees = array_filter(Tree::getAll(), function (Tree $tree): bool {
            return Auth::isManager($tree);
        });

        return $this->viewResponse('admin/control-panel-manager', [
            'title'        => I18N::translate('Control panel'),
            'all_trees'    => $all_trees,
            'changes'      => $this->totalChanges(),
            'individuals'  => $this->totalIndividuals(),
            'families'     => $this->totalFamilies(),
            'sources'      => $this->totalSources(),
            'media'        => $this->totalMediaObjects(),
            'repositories' => $this->totalRepositories(),
            'notes'        => $this->totalNotes(),
        ]);
    }

    /**
     * Show the edit history for a tree.
     *
     * @param Request     $request
     * @param UserService $user_service
     *
     * @return Response
     */
    public function changesLog(Request $request, UserService $user_service): Response
    {
        $tree_list = [];
        foreach (Tree::getAll() as $tree) {
            if (Auth::isManager($tree)) {
                $tree_list[$tree->name()] = $tree->title();
            }
        }

        $user_list = ['' => ''];
        foreach ($user_service->all() as $tmp_user) {
            $user_list[$tmp_user->userName()] = $tmp_user->userName();
        }

        $action = $request->get('action');

        // @TODO This ought to be a POST action
        if ($action === 'delete') {
            $this->changesQuery($request)->delete();
        }

        // First and last change in the database.
        $earliest = DB::table('change')->min('change_time');
        $latest   = DB::table('change')->max('change_time');

        $earliest = $earliest ? new Carbon($earliest) : Carbon::now();
        $latest   = $latest ? new Carbon($latest) : Carbon::now();

        $earliest = $earliest->toDateString();
        $latest   = $latest->toDateString();

        $ged      = $request->get('ged');
        $from     = $request->get('from', $earliest);
        $to       = $request->get('to', $latest);
        $type     = $request->get('type', '');
        $oldged   = $request->get('oldged', '');
        $newged   = $request->get('newged', '');
        $xref     = $request->get('xref', '');
        $username = $request->get('username', '');
        $search   = $request->get('search', []);
        $search   = $search['value'] ?? null;

        if (!array_key_exists($ged, $tree_list)) {
            $ged = reset($tree_list);
        }

        $statuses = [
            ''         => '',
            /* I18N: the status of an edit accepted/rejected/pending */
            'accepted' => I18N::translate('accepted'),
            /* I18N: the status of an edit accepted/rejected/pending */
            'rejected' => I18N::translate('rejected'),
            /* I18N: the status of an edit accepted/rejected/pending */
            'pending'  => I18N::translate('pending'),
        ];

        return $this->viewResponse('admin/changes-log', [
            'action'    => $action,
            'earliest'  => $earliest,
            'from'      => $from,
            'ged'       => $ged,
            'latest'    => $latest,
            'newged'    => $newged,
            'oldged'    => $oldged,
            'search'    => $search,
            'statuses'  => $statuses,
            'title'     => I18N::translate('Changes log'),
            'to'        => $to,
            'tree_list' => $tree_list,
            'type'      => $type,
            'username'  => $username,
            'user_list' => $user_list,
            'xref'      => $xref,
        ]);
    }

    /**
     * Show the edit history for a tree.
     *
     * @param Request           $request
     * @param DatatablesService $datatables_service
     * @param MyersDiff         $myers_diff
     *
     * @return Response
     */
    public function changesLogData(Request $request, DatatablesService $datatables_service, MyersDiff $myers_diff): Response
    {
        $query = $this->changesQuery($request);

        $callback = function (stdClass $row) use ($myers_diff): array {
            $old_lines = preg_split('/[\n]+/', $row->old_gedcom, -1, PREG_SPLIT_NO_EMPTY);
            $new_lines = preg_split('/[\n]+/', $row->new_gedcom, -1, PREG_SPLIT_NO_EMPTY);

            $differences = $myers_diff->calculate($old_lines, $new_lines);
            $diff_lines  = [];

            foreach ($differences as $difference) {
                switch ($difference[1]) {
                    case MyersDiff::DELETE:
                        $diff_lines[] = '<del>' . $difference[0] . '</del>';
                        break;
                    case MyersDiff::INSERT:
                        $diff_lines[] = '<ins>' . $difference[0] . '</ins>';
                        break;
                    default:
                        $diff_lines[] = $difference[0];
                }
            }

            // Only convert valid xrefs to links
            $tree   = Tree::findByName($row->gedcom_name);
            $record = GedcomRecord::getInstance($row->xref, $tree);

            return [
                $row->change_id,
                $row->change_time,
                I18N::translate($row->status),
                $record ? '<a href="' . e($record->url()) . '">' . $record->xref() . '</a>' : $row->xref,
                '<div class="gedcom-data" dir="ltr">' .
                preg_replace_callback(
                    '/@(' . Gedcom::REGEX_XREF . ')@/',
                    function (array $match) use ($tree) : string {
                        $record = GedcomRecord::getInstance($match[1], $tree);

                        return $record ? '<a href="' . e($record->url()) . '">' . $match[0] . '</a>' : $match[0];
                    },
                    implode("\n", $diff_lines)
                ) .
                '</div>',
                $row->user_name,
                $row->gedcom_name,
            ];
        };

        return $datatables_service->handle($request, $query, [], [], $callback);
    }

    /**
     * Show the edit history for a tree.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function changesLogDownload(Request $request): Response
    {
        $content = $this->changesQuery($request)
            ->get()
            ->map(function (stdClass $row): string {
                // Convert to CSV
                return implode(',', [
                    '"' . $row->change_time . '"',
                    '"' . $row->status . '"',
                    '"' . $row->xref . '"',
                    '"' . strtr($row->old_gedcom, '"', '""') . '"',
                    '"' . strtr($row->new_gedcom, '"', '""') . '"',
                    '"' . strtr($row->user_name, '"', '""') . '"',
                    '"' . strtr($row->gedcom_name, '"', '""') . '"',
                ]);
            })
            ->implode("\n");

        $response    = new Response($content);
        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'changes.csv');
        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');

        return $response;
    }

    /**
     * If media objects are wronly linked to top-level records, reattach them
     * to facts/events.
     *
     * @return Response
     */
    public function fixLevel0Media(): Response
    {
        return $this->viewResponse('admin/fix-level-0-media', [
            'title' => I18N::translate('Link media objects to facts and events'),
        ]);
    }

    /**
     * Move a link to a media object from a level 0 record to a level 1 record.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function fixLevel0MediaAction(Request $request): Response
    {
        $fact_id   = $request->get('fact_id');
        $indi_xref = $request->get('indi_xref');
        $obje_xref = $request->get('obje_xref');
        $tree_id   = (int) $request->get('tree_id');

        $tree       = Tree::findById($tree_id);
        $individual = Individual::getInstance($indi_xref, $tree);
        $media      = Media::getInstance($obje_xref, $tree);

        if ($individual !== null && $media !== null) {
            foreach ($individual->facts() as $fact1) {
                if ($fact1->id() === $fact_id) {
                    $individual->updateFact($fact_id, $fact1->gedcom() . "\n2 OBJE @" . $obje_xref . '@', false);
                    foreach ($individual->facts(['OBJE']) as $fact2) {
                        if ($fact2->target() === $media) {
                            $individual->deleteFact($fact2->id(), false);
                        }
                    }
                    break;
                }
            }
        }

        return new Response();
    }

    /**
     * If media objects are wronly linked to top-level records, reattach them
     * to facts/events.
     *
     * @param Request           $request
     * @param DatatablesService $datatables_service
     *
     * @return JsonResponse
     */
    public function fixLevel0MediaData(Request $request, DatatablesService $datatables_service): JsonResponse
    {
        $ignore_facts = [
            'FAMC',
            'FAMS',
            'NAME',
            'SEX',
            'CHAN',
            'NOTE',
            'OBJE',
            'SOUR',
            'RESN',
        ];

        $prefix = DB::connection()->getTablePrefix();

        $query = DB::table('media')
            ->join('media_file', function (JoinClause $join): void {
                $join
                    ->on('media_file.m_file', '=', 'media.m_file')
                    ->on('media_file.m_id', '=', 'media.m_id');
            })
            ->join('link', function (JoinClause $join): void {
                $join
                    ->on('link.l_file', '=', 'media.m_file')
                    ->on('link.l_to', '=', 'media.m_id');
            })
            ->join('individuals', function (JoinClause $join): void {
                $join
                    ->on('individuals.i_file', '=', 'link.l_file')
                    ->on('individuals.i_id', '=', 'link.l_from');
            })
            ->where('i_gedcom', 'LIKE', DB::raw("CONCAT('%\n1 OBJE @', " . $prefix . "media.m_id, '@%')"))
            ->orderby('individuals.i_file')
            ->orderBy('individuals.i_id')
            ->orderBy('media.m_id')
            ->select(['media.m_file', 'media.m_id', 'media.m_gedcom', 'individuals.i_id', 'individuals.i_gedcom']);

        return $datatables_service->handle($request, $query, [], [], function (stdClass $datum) use ($ignore_facts): array {
            $tree       = Tree::findById((int) $datum->m_file);
            $media      = Media::getInstance($datum->m_id, $tree, $datum->m_gedcom);
            $individual = Individual::getInstance($datum->i_id, $tree, $datum->i_gedcom);

            $facts = $individual->facts([], true);
            $facts = array_filter($facts, function (Fact $fact) use ($ignore_facts): bool {
                return !$fact->isPendingDeletion() && !in_array($fact->getTag(), $ignore_facts);
            });

            // The link to the media object may have been deleted in a pending change.
            $deleted = true;
            foreach ($individual->facts(['OBJE']) as $fact) {
                if ($fact->target() === $media && !$fact->isPendingDeletion()) {
                    $deleted = false;
                }
            }
            if ($deleted) {
                $facts = [];
            }

            $facts = array_map(function (Fact $fact) use ($individual, $media): string {
                return view('admin/fix-level-0-media-action', [
                    'fact'       => $fact,
                    'individual' => $individual,
                    'media'      => $media,
                ]);
            }, $facts);

            return [
                $tree->name(),
                $media->displayImage(100, 100, 'fit', ['class' => 'img-thumbnail']),
                '<a href="' . e($media->url()) . '">' . $media->getFullName() . '</a>',
                '<a href="' . e($individual->url()) . '">' . $individual->getFullName() . '</a>',
                implode(' ', $facts),
            ];
        });
    }

    /**
     * Import custom thumbnails from webtres 1.x.
     *
     * @return Response
     */
    public function webtrees1Thumbnails(): Response
    {
        return $this->viewResponse('admin/webtrees1-thumbnails', [
            'title' => I18N::translate('Import custom thumbnails from webtrees version 1'),
        ]);
    }

    /**
     * Import custom thumbnails from webtres 1.x.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function webtrees1ThumbnailsAction(Request $request): Response
    {
        $thumbnail = $request->get('thumbnail', '');
        $action    = $request->get('action', '');
        $xrefs     = $request->get('xref', []);
        $geds      = $request->get('ged', []);

        $media_objects = [];

        foreach ($xrefs as $key => $xref) {
            $tree            = Tree::findByName($geds[$key]);
            $media_objects[] = Media::getInstance($xref, $tree);
        }

        $thumbnail = WT_DATA_DIR . $thumbnail;

        switch ($action) {
            case 'delete':
                if (file_exists($thumbnail)) {
                    unlink($thumbnail);
                }
                break;

            case 'add':
                $image_size = getimagesize($thumbnail);
                [, $extension] = explode('/', $image_size['mime']);
                $move_to = dirname($thumbnail, 2) . '/' . sha1_file($thumbnail) . '.' . $extension;
                rename($thumbnail, $move_to);

                foreach ($media_objects as $media_object) {
                    $prefix = WT_DATA_DIR . $media_object->tree()->getPreference('MEDIA_DIRECTORY');
                    $gedcom = "1 FILE " . substr($move_to, strlen($prefix)) . "\n2 FORM " . $extension;

                    if ($media_object->firstImageFile() === null) {
                        // The media object doesn't have an image.  Add this as a secondary file.
                        $media_object->createFact($gedcom, true);
                    } else {
                        // The media object already has an image.  Show this custom one in preference.
                        $gedcom = '0 @' . $media_object->xref() . "@ OBJE\n" . $gedcom;
                        foreach ($media_object->facts() as $fact) {
                            $gedcom .= "\n" . $fact->gedcom();
                        }
                        $media_object->updateRecord($gedcom, true);
                    }

                    // Accept the changes, to keep the filesystem in sync with the GEDCOM data.
                    FunctionsImport::acceptAllChanges($media_object->getxref(), $media_object->tree());
                }
                break;
        }

        return new JsonResponse([]);
    }

    /**
     * Import custom thumbnails from webtres 1.x.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function webtrees1ThumbnailsData(Request $request): JsonResponse
    {
        $start  = (int) $request->get('start', 0);
        $length = (int) $request->get('length', 20);
        $search = $request->get('search', []);
        $search = $search['value'] ?? '';

        // Fetch all thumbnails
        $thumbnails = [];

        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(WT_DATA_DIR, FilesystemIterator::FOLLOW_SYMLINKS));

        foreach ($iterator as $iteration) {
            if ($iteration->isFile() && strpos($iteration->getPathname(), '/thumbs/') !== false) {
                $thumbnails[] = $iteration->getPathname();
            }
        }

        $recordsTotal = count($thumbnails);

        if ($search !== '') {
            $thumbnails = array_filter($thumbnails, function (string $thumbnail) use ($search): bool {
                return stripos($thumbnail, $search) !== false;
            });
        }

        $recordsFiltered = count($thumbnails);

        $thumbnails = array_slice($thumbnails, $start, $length);

        // Turn each filename into a row for the table
        $data = array_map(function (string $thumbnail): array {
            $original = $this->findOriginalFileFromThumbnail($thumbnail);

            $original_url  = route('unused-media-thumbnail', [
                'folder' => dirname($original),
                'file'   => basename($original),
                'w'      => 100,
                'h'      => 100,
            ]);
            $thumbnail_url = route('unused-media-thumbnail', [
                'folder' => dirname($thumbnail),
                'file'   => basename($thumbnail),
                'w'      => 100,
                'h'      => 100,
            ]);

            $difference = $this->imageDiff($thumbnail, $original);

            $original_path  = substr($original, strlen(WT_DATA_DIR));
            $thumbnail_path = substr($thumbnail, strlen(WT_DATA_DIR));

            $media = $this->findMediaObjectsForMediaFile($original_path);

            $media_links = array_map(function (Media $media): string {
                return '<a href="' . e($media->url()) . '">' . $media->getFullName() . '</a>';
            }, $media);

            $media_links = implode('<br>', $media_links);

            $action = view('admin/webtrees1-thumbnails-form', [
                'difference' => $difference,
                'media'      => $media,
                'thumbnail'  => $thumbnail_path,
            ]);

            return [
                '<img src="' . e($thumbnail_url) . '" title="' . e($thumbnail_path) . '">',
                '<img src="' . e($original_url) . '" title="' . e($original_path) . '">',
                $media_links,
                I18N::percentage($difference / 100.0, 0),
                $action,
            ];
        }, $thumbnails);

        return new JsonResponse([
            'draw'            => (int) $request->get('draw'),
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }

    /**
     * Merge two genealogy records.
     *
     * @param Request $request
     * @param Tree    $tree
     *
     * @return Response
     */
    public function mergeRecords(Request $request, Tree $tree): Response
    {
        $title = I18N::translate('Merge records') . ' — ' . e($tree->title());

        $xref1 = $request->get('xref1', '');
        $xref2 = $request->get('xref2', '');

        $record1 = GedcomRecord::getInstance($xref1, $tree);
        $record2 = GedcomRecord::getInstance($xref2, $tree);

        if ($xref1 !== '' && $record1 === null) {
            $xref1 = '';
        }

        if ($xref2 !== '' && $record2 === null) {
            $xref2 = '';
        }

        if ($record1 === $record2) {
            $xref2 = '';
        }

        if ($record1 !== null && $record2 && $record1::RECORD_TYPE !== $record2::RECORD_TYPE) {
            $xref2 = '';
        }

        if ($xref1 === '' || $xref2 === '') {
            return $this->viewResponse('admin/merge-records-step-1', [
                'individual1' => $record1 instanceof Individual ? $record1 : null,
                'individual2' => $record2 instanceof Individual ? $record2 : null,
                'family1'     => $record1 instanceof Family ? $record1 : null,
                'family2'     => $record2 instanceof Family ? $record2 : null,
                'source1'     => $record1 instanceof Source ? $record1 : null,
                'source2'     => $record2 instanceof Source ? $record2 : null,
                'repository1' => $record1 instanceof Repository ? $record1 : null,
                'repository2' => $record2 instanceof Repository ? $record2 : null,
                'media1'      => $record1 instanceof Media ? $record1 : null,
                'media2'      => $record2 instanceof Media ? $record2 : null,
                'note1'       => $record1 instanceof Note ? $record1 : null,
                'note2'       => $record2 instanceof Note ? $record2 : null,
                'title'       => $title,
            ]);
        }

        // Facts found both records
        $facts = [];
        // Facts found in only one record
        $facts1 = [];
        $facts2 = [];

        foreach ($record1->facts() as $fact) {
            if (!$fact->isPendingDeletion() && $fact->getTag() !== 'CHAN') {
                $facts1[$fact->id()] = $fact;
            }
        }

        foreach ($record2->facts() as $fact) {
            if (!$fact->isPendingDeletion() && $fact->getTag() !== 'CHAN') {
                $facts2[$fact->id()] = $fact;
            }
        }

        foreach ($facts1 as $id1 => $fact1) {
            foreach ($facts2 as $id2 => $fact2) {
                if ($fact1->id() === $fact2->id()) {
                    $facts[] = $fact1;
                    unset($facts1[$id1]);
                    unset($facts2[$id2]);
                }
            }
        }

        return $this->viewResponse('admin/merge-records-step-2', [
            'facts'   => $facts,
            'facts1'  => $facts1,
            'facts2'  => $facts2,
            'record1' => $record1,
            'record2' => $record2,
            'title'   => $title,
        ]);
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return Response
     */
    public function mergeRecordsAction(Request $request, Tree $tree): Response
    {
        $xref1 = $request->get('xref1', '');
        $xref2 = $request->get('xref2', '');
        $keep1 = $request->get('keep1', []);
        $keep2 = $request->get('keep2', []);

        // Merge record2 into record1
        $record1 = GedcomRecord::getInstance($xref1, $tree);
        $record2 = GedcomRecord::getInstance($xref2, $tree);

        // Facts found both records
        $facts = [];
        // Facts found in only one record
        $facts1 = [];
        $facts2 = [];

        foreach ($record1->facts() as $fact) {
            if (!$fact->isPendingDeletion() && $fact->getTag() !== 'CHAN') {
                $facts1[$fact->id()] = $fact;
            }
        }

        foreach ($record2->facts() as $fact) {
            if (!$fact->isPendingDeletion() && $fact->getTag() !== 'CHAN') {
                $facts2[$fact->id()] = $fact;
            }
        }

        // If we are not auto-accepting, then we can show a link to the pending deletion
        if (Auth::user()->getPreference('auto_accept')) {
            $record2_name = $record2->getFullName();
        } else {
            $record2_name = '<a class="alert-link" href="' . e($record2->url()) . '">' . $record2->getFullName() . '</a>';
        }

        // Update records that link to the one we will be removing.
        $linking_records = $record2->linkingRecords();

        foreach ($linking_records as $record) {
            if (!$record->isPendingDeletion()) {
                /* I18N: The placeholders are the names of individuals, sources, etc. */
                FlashMessages::addMessage(I18N::translate(
                    'The link from “%1$s” to “%2$s” has been updated.',
                    '<a class="alert-link" href="' . e($record->url()) . '">' . $record->getFullName() . '</a>',
                    $record2_name
                ), 'info');
                $gedcom = str_replace('@' . $xref2 . '@', '@' . $xref1 . '@', $record->gedcom());
                $gedcom = preg_replace(
                    '/(\n1.*@.+@.*(?:(?:\n[2-9].*)*))((?:\n1.*(?:\n[2-9].*)*)*\1)/',
                    '$2',
                    $gedcom
                );
                $record->updateRecord($gedcom, true);
            }
        }

        // Update any linked user-accounts
        DB::table('user_gedcom_setting')
            ->where('gedcom_id', '=', $tree->id())
            ->whereIn('setting_name', ['gedcomid', 'rootid'])
            ->where('setting_value', '=', $xref2)
            ->update(['setting_value' => $xref1]);

        // Merge hit counters
        $hits = DB::table('hit_counter')
            ->where('gedcom_id', '=', $tree->id())
            ->whereIn('page_parameter', [$xref1, $xref2])
            ->groupBy('page_name')
            ->pluck(DB::raw('SUM(page_count)'), 'page_name');

        foreach ($hits as $page_name => $page_count) {
            DB::table('hit_counter')
                ->where('gedcom_id', '=', $tree->id())
                ->where('page_name', '=', $page_name)
                ->update(['page_count' => $page_count]);
        }

        DB::table('hit_counter')
            ->where('gedcom_id', '=', $tree->id())
            ->where('page_parameter', '=', $xref2)
            ->delete();

        $gedcom = '0 @' . $record1->xref() . '@ ' . $record1::RECORD_TYPE;
        foreach ($facts as $fact_id => $fact) {
            $gedcom .= "\n" . $fact->gedcom();
        }
        foreach ($facts1 as $fact_id => $fact) {
            if (in_array($fact_id, $keep1)) {
                $gedcom .= "\n" . $fact->gedcom();
            }
        }
        foreach ($facts2 as $fact_id => $fact) {
            if (in_array($fact_id, $keep2)) {
                $gedcom .= "\n" . $fact->gedcom();
            }
        }

        DB::table('favorite')
            ->where('gedcom_id', '=', $tree->id())
            ->where('xref', '=', $xref2)
            ->update(['xref' => $xref1]);

        $record1->updateRecord($gedcom, true);
        $record2->deleteRecord();

        /* I18N: Records are individuals, sources, etc. */
        FlashMessages::addMessage(I18N::translate(
            'The records “%1$s” and “%2$s” have been merged.',
            '<a class="alert-link" href="' . e($record1->url()) . '">' . $record1->getFullName() . '</a>',
            $record2_name
        ), 'success');

        return new RedirectResponse(route('merge-records', ['ged' => $tree->name()]));
    }

    /**
     * @param Tree $tree
     *
     * @return Response
     */
    public function treePrivacyEdit(Tree $tree): Response
    {
        $title                = e($tree->name()) . ' — ' . I18N::translate('Privacy');
        $all_tags             = $this->tagsForPrivacy($tree);
        $privacy_constants    = $this->privacyConstants();
        $privacy_restrictions = $this->privacyRestrictions($tree);

        return $this->viewResponse('admin/trees-privacy', [
            'all_tags'             => $all_tags,
            'count_trees'          => count(Tree::getAll()),
            'privacy_constants'    => $privacy_constants,
            'privacy_restrictions' => $privacy_restrictions,
            'title'                => $title,
        ]);
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return RedirectResponse
     */
    public function treePrivacyUpdate(Request $request, Tree $tree): RedirectResponse
    {
        $delete_default_resn_id = (array) $request->get('delete');

        DB::table('default_resn')
            ->whereIn('default_resn_id', $delete_default_resn_id)
            ->delete();

        $xrefs     = (array) $request->get('xref');
        $tag_types = (array) $request->get('tag_type');
        $resns     = (array) $request->get('resn');

        foreach ($xrefs as $n => $xref) {
            $tag_type = (string) $tag_types[$n];
            $resn     = (string) $resns[$n];

            if ($tag_type !== '' || $xref !== '') {
                // Delete any existing data
                if ($xref === '') {
                    DB::table('default_resn')
                        ->where('gedcom_id', '=', $tree->id())
                        ->where('xref', '=', $xref)
                        ->delete();
                }
                if ($tag_type === '' && $xref !== '') {
                    DB::table('default_resn')
                        ->where('gedcom_id', '=', $tree->id())
                        ->whereNull('tag_type')
                        ->where('xref', '=', $xref)
                        ->delete();
                }

                // Add (or update) the new data
                DB::table('default_resn')->insert([
                    'gedcom_id' => $tree->id(),
                    'xref'      => $xref,
                    'tag_type'  => $tag_type,
                    'resn'      => $resn,
                ]);
            }
        }

        $tree->setPreference('HIDE_LIVE_PEOPLE', $request->get('HIDE_LIVE_PEOPLE'));
        $tree->setPreference('KEEP_ALIVE_YEARS_BIRTH', $request->get('KEEP_ALIVE_YEARS_BIRTH', '0'));
        $tree->setPreference('KEEP_ALIVE_YEARS_DEATH', $request->get('KEEP_ALIVE_YEARS_DEATH', '0'));
        $tree->setPreference('MAX_ALIVE_AGE', $request->get('MAX_ALIVE_AGE', '100'));
        $tree->setPreference('REQUIRE_AUTHENTICATION', $request->get('REQUIRE_AUTHENTICATION'));
        $tree->setPreference('SHOW_DEAD_PEOPLE', $request->get('SHOW_DEAD_PEOPLE'));
        $tree->setPreference('SHOW_LIVING_NAMES', $request->get('SHOW_LIVING_NAMES'));
        $tree->setPreference('SHOW_PRIVATE_RELATIONSHIPS', $request->get('SHOW_PRIVATE_RELATIONSHIPS'));

        FlashMessages::addMessage(I18N::translate('The preferences for the family tree “%s” have been updated.', e($tree->title()), 'success'));

        // Coming soon...
        if ((bool) $request->get('all_trees')) {
            FlashMessages::addMessage(I18N::translate('The preferences for all family trees have been updated.', e($tree->title())), 'success');
        }
        if ((bool) $request->get('new_trees')) {
            FlashMessages::addMessage(I18N::translate('The preferences for new family trees have been updated.', e($tree->title())), 'success');
        }

        return new RedirectResponse(route('admin-trees', ['ged' => $tree->name()]));
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
    protected function viewResponse($name, $data, $status = Response::HTTP_OK): Response
    {
        $html = view($this->layout, [
            'content' => view($name, $data),
            'title'   => strip_tags($data['title']),
        ]);

        return new Response($html, $status);
    }

    /**
     * Generate a query for filtering the changes log.
     *
     * @param Request $request
     *
     * @return Builder
     */
    private function changesQuery(Request $request): Builder
    {
        $from     = $request->get('from', '');
        $to       = $request->get('to', '');
        $type     = $request->get('type', '');
        $oldged   = $request->get('oldged', '');
        $newged   = $request->get('newged', '');
        $xref     = $request->get('xref', '');
        $username = $request->get('username', '');
        $ged      = $request->get('ged', '');
        $search   = $request->get('search', '');
        $search   = $search['value'] ?? '';

        $query = DB::table('change')
            ->leftJoin('user', 'user.user_id', '=', 'change.user_id')
            ->join('gedcom', 'gedcom.gedcom_id', '=', 'change.gedcom_id')
            ->select(['change.*', DB::raw("IFNULL(user_name, '<none>') AS user_name"), 'gedcom_name']);

        if ($search !== '') {
            $query->where(function (Builder $query) use ($search): void {
                $query
                    ->whereContains('old_gedcom', $search)
                    ->whereContains('new_gedcom', $search, 'or');
            });
        }

        if ($from !== '') {
            $query->where('change_time', '>=', $from);
        }

        if ($to !== '') {
            // before end of the day
            $query->where('change_time', '<', (new Carbon($to))->addDay());
        }

        if ($type !== '') {
            $query->where('status', '=', $type);
        }

        if ($oldged !== '') {
            $query->whereContains('old_gedcom', $oldged);
        }
        if ($newged !== '') {
            $query->whereContains('new_gedcom', $oldged);
        }

        if ($xref !== '') {
            $query->where('xref', '=', $xref);
        }

        if ($username !== '') {
            $query->whereContains('user_name', $username);
        }

        if ($ged !== '') {
            $query->whereContains('gedcom_name', $ged);
        }

        return $query;
    }

    /**
     * Find the media object that uses a particular media file.
     *
     * @param string $file
     *
     * @return Media[]
     */
    private function findMediaObjectsForMediaFile(string $file): array
    {
        return DB::table('media')
            ->join('media_file', function (JoinClause $join): void {
                $join
                    ->on('media_file.m_file', '=', 'media.m_file')
                    ->on('media_file.m_id', '=', 'media.m_id');
            })
            ->join('gedcom_setting', 'media.m_file', '=', 'gedcom_setting.gedcom_id')
            ->where(DB::raw('CONCAT(setting_value, multimedia_file_refn)'), '=', $file)
            ->select(['media.*'])
            ->distinct()
            ->get()
            ->map(Media::rowMapper())
            ->all();
    }

    /**
     * Find the original image that corresponds to a (webtrees 1.x) thumbnail file.
     *
     * @param string $thumbnail
     *
     * @return string
     */
    private function findOriginalFileFromThumbnail(string $thumbnail): string
    {
        // First option - a file with the same name
        $original = str_replace('/thumbs/', '/', $thumbnail);

        // Second option - a .PNG thumbnail for some other image type
        if (substr_compare($original, '.png', -4, 4) === 0) {
            $pattern = substr($original, 0, -3) . '*';
            $matches = glob($pattern);
            if (!empty($matches) && is_file($matches[0])) {
                $original = $matches[0];
            }
        }

        return $original;
    }

    /**
     * Compare two images, and return a quantified difference.
     * 0 (different) ... 100 (same)
     *
     * @param string $thumbanil
     * @param string $original
     *
     * @return int
     */
    private function imageDiff($thumbanil, $original): int
    {
        try {
            if (getimagesize($thumbanil) === false) {
                return 100;
            }
        } catch (Throwable $ex) {
            // If the first file is not an image then similarity is unimportant.
            // Response with an exact match, so the GUI will recommend deleting it.
            return 100;
        }

        try {
            if (getimagesize($original) === false) {
                return 0;
            }
        } catch (Throwable $ex) {
            // If the first file is not an image then the thumbnail .
            // Response with an exact mismatch, so the GUI will recommend importing it.
            return 0;
        }

        $pixels1 = $this->scaledImagePixels($thumbanil);
        $pixels2 = $this->scaledImagePixels($original);

        $max_difference = 0;

        foreach ($pixels1 as $x => $row) {
            foreach ($row as $y => $pixel) {
                $max_difference = max($max_difference, abs($pixel - $pixels2[$x][$y]));
            }
        }

        // The maximum difference is 255 (black versus white).
        return 100 - intdiv($max_difference * 100, 255);
    }

    /**
     * Scale an image to 10x10 and read the individual pixels.
     * This is a slow operation, add we will do it many times on
     * the "import wetbrees 1 thumbnails" page so cache the results.
     *
     * @param string $path
     *
     * @return int[][]
     */
    private function scaledImagePixels($path): array
    {
        $size       = 10;
        $sha1       = sha1_file($path);
        $cache_file = WT_DATA_DIR . 'cache/' . $sha1 . '.php';

        if (file_exists($cache_file)) {
            return include $cache_file;
        }

        $manager = new ImageManager();
        $image   = $manager->make($path)->resize($size, $size);

        $pixels = [];
        for ($x = 0; $x < $size; ++$x) {
            $pixels[$x] = [];
            for ($y = 0; $y < $size; ++$y) {
                $pixel          = $image->pickColor($x, $y);
                $pixels[$x][$y] = (int) (($pixel[0] + $pixel[1] + $pixel[2]) / 3);
            }
        }

        file_put_contents($cache_file, '<?php return ' . var_export($pixels, true) . ';');

        return $pixels;
    }

    /**
     * Names of our privacy levels
     *
     * @return array
     */
    private function privacyConstants(): array
    {
        return [
            'none'         => I18N::translate('Show to visitors'),
            'privacy'      => I18N::translate('Show to members'),
            'confidential' => I18N::translate('Show to managers'),
            'hidden'       => I18N::translate('Hide from everyone'),
        ];
    }

    /**
     * The current privacy restrictions for a tree.
     *
     * @param Tree $tree
     *
     * @return array
     */
    private function privacyRestrictions(Tree $tree): array
    {
        return DB::table('default_resn')
            ->where('gedcom_id', '=', $tree->id())
            ->get()
            ->map(function (stdClass $row) use ($tree): stdClass {
                $row->record = null;
                $row->label  = '';

                if ($row->xref !== null) {
                    $row->record = GedcomRecord::getInstance($row->xref, $tree);
                }

                if ($row->tag_type) {
                    $row->tag_label = GedcomTag::getLabel($row->tag_type);
                } else {
                    $row->tag_label = '';
                }

                return $row;
            })
            ->sort(function (stdClass $x, stdClass $y): int {
                return I18N::strcasecmp($x->tag_label, $y->tag_label);
            })
            ->all();
    }

    /**
     * Generate a list of potential problems with the server.
     *
     * @return string[]
     */
    private function serverWarnings(): array
    {
        $php_support_url   = 'https://secure.php.net/supported-versions.php';
        $version_parts     = explode('.', PHP_VERSION);
        $php_minor_version = $version_parts[0] . $version_parts[1];
        $today             = date('Y-m-d');
        $warnings          = [];

        if ($php_minor_version === '70' && $today >= '2018-12-03' || $php_minor_version === '71' && $today >= '2019-12-01' || $php_minor_version === '72' && $today >= '2020-11-30') {
            $warnings[] = I18N::translate('Your web server is using PHP version %s, which is no longer receiving security updates. You should upgrade to a later version as soon as possible.', PHP_VERSION) . ' <a href="' . $php_support_url . '">' . $php_support_url . '</a>';
        }

        return $warnings;
    }

    /**
     * Generate a list of potential problems with the server.
     *
     * @param Tree $tree
     *
     * @return string[]
     */
    private function tagsForPrivacy(Tree $tree): array
    {
        $tags = array_unique(array_merge(
            explode(',', $tree->getPreference('INDI_FACTS_ADD')),
            explode(',', $tree->getPreference('INDI_FACTS_UNIQUE')),
            explode(',', $tree->getPreference('FAM_FACTS_ADD')),
            explode(',', $tree->getPreference('FAM_FACTS_UNIQUE')),
            explode(',', $tree->getPreference('NOTE_FACTS_ADD')),
            explode(',', $tree->getPreference('NOTE_FACTS_UNIQUE')),
            explode(',', $tree->getPreference('SOUR_FACTS_ADD')),
            explode(',', $tree->getPreference('SOUR_FACTS_UNIQUE')),
            explode(',', $tree->getPreference('REPO_FACTS_ADD')),
            explode(',', $tree->getPreference('REPO_FACTS_UNIQUE')),
            [
                'SOUR',
                'REPO',
                'OBJE',
                '_PRIM',
                'NOTE',
                'SUBM',
                'SUBN',
                '_UID',
                'CHAN',
            ]
        ));

        $all_tags = [];

        foreach ($tags as $tag) {
            if ($tag) {
                $all_tags[$tag] = GedcomTag::getLabel($tag);
            }
        }

        uasort($all_tags, '\Fisharebest\Webtrees\I18N::strcasecmp');

        return array_merge(
            ['' => I18N::translate('All facts and events')],
            $all_tags
        );
    }

    /**
     * Count the number of pending changes in each tree.
     *
     * @return string[]
     */
    private function totalChanges(): array
    {
        return DB::table('gedcom')
            ->leftJoin('change', function (JoinClause $join): void {
                $join
                    ->on('change.gedcom_id', '=', 'gedcom.gedcom_id')
                    ->where('change.status', '=', 'pending');
            })
            ->groupBy('gedcom.gedcom_id')
            ->pluck(DB::raw('COUNT(change_id)'), 'gedcom.gedcom_id')
            ->all();
    }

    /**
     * Count the number of families in each tree.
     *
     * @return Collection|int[]
     */
    private function totalFamilies(): Collection
    {
        return DB::table('gedcom')
            ->leftJoin('families', 'f_file', '=', 'gedcom_id')
            ->groupBy('gedcom_id')
            ->pluck(DB::raw('COUNT(f_id)'), 'gedcom_id')
            ->map(function (string $count) {
                return (int) $count;
            });
    }

    /**
     * Count the number of individuals in each tree.
     *
     * @return Collection|int[]
     */
    private function totalIndividuals(): Collection
    {
        return DB::table('gedcom')
            ->leftJoin('individuals', 'i_file', '=', 'gedcom_id')
            ->groupBy('gedcom_id')
            ->pluck(DB::raw('COUNT(i_id)'), 'gedcom_id')
            ->map(function (string $count) {
                return (int) $count;
            });
    }

    /**
     * Count the number of media objects in each tree.
     *
     * @return Collection|int[]
     */
    private function totalMediaObjects(): Collection
    {
        return DB::table('gedcom')
            ->leftJoin('media', 'm_file', '=', 'gedcom_id')
            ->groupBy('gedcom_id')
            ->pluck(DB::raw('COUNT(m_id)'), 'gedcom_id')
            ->map(function (string $count) {
                return (int) $count;
            });
    }

    /**
     * Count the number of notes in each tree.
     *
     * @return Collection|int[]
     */
    private function totalNotes(): Collection
    {
        return DB::table('gedcom')
            ->leftJoin('other', function (JoinClause $join): void {
                $join
                    ->on('o_file', '=', 'gedcom_id')
                    ->where('o_type', '=', 'NOTE');
            })
            ->groupBy('gedcom_id')
            ->pluck(DB::raw('COUNT(o_id)'), 'gedcom_id')
            ->map(function (string $count) {
                return (int) $count;
            });
    }

    /**
     * Count the number of repositorie in each tree.
     *
     * @return Collection|int[]
     */
    private function totalRepositories(): Collection
    {
        return DB::table('gedcom')
            ->leftJoin('other', function (JoinClause $join): void {
                $join
                    ->on('o_file', '=', 'gedcom_id')
                    ->where('o_type', '=', 'REPO');
            })
            ->groupBy('gedcom_id')
            ->pluck(DB::raw('COUNT(o_id)'), 'gedcom_id')
            ->map(function (string $count) {
                return (int) $count;
            });
    }

    /**
     * Count the number of sources in each tree.
     *
     * @return Collection|int[]
     */
    private function totalSources(): Collection
    {
        return DB::table('gedcom')
            ->leftJoin('sources', 's_file', '=', 'gedcom_id')
            ->groupBy('gedcom_id')
            ->pluck(DB::raw('COUNT(s_id)'), 'gedcom_id')
            ->map(function (string $count) {
                return (int) $count;
            });
    }
}
