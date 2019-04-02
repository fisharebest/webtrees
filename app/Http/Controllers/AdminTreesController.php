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

use Exception;
use function fclose;
use Fisharebest\Algorithm\ConnectedComponent;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\File;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Functions\Functions;
use Fisharebest\Webtrees\Functions\FunctionsExport;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Module\ModuleThemeInterface;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TimeoutService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\SurnameTradition;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use League\Flysystem\Filesystem;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;
use Nyholm\Psr7\UploadedFile;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use stdClass;
use Throwable;
use function addcslashes;
use function app;
use const UPLOAD_ERR_OK;
use const WT_DATA_DIR;

/**
 * Controller for tree administration.
 */
class AdminTreesController extends AbstractBaseController
{
    // Show a reduced page when there are more than a certain number of trees
    private const MULTIPLE_TREE_THRESHOLD = '500';

    /** @var string */
    protected $layout = 'layouts/administration';

    /**
     * @var ModuleService
     */
    private $module_service;

    /**
     * @var UserService
     */
    private $user_service;

    /**
     * AdminTreesController constructor.
     *
     * @param ModuleService $module_service
     * @param UserService   $user_service
     */
    public function __construct(ModuleService $module_service, UserService $user_service)
    {
        $this->module_service = $module_service;
        $this->user_service   = $user_service;
    }

    /**
     * @param Tree $tree
     *
     * @return ResponseInterface
     */
    public function check(Tree $tree): ResponseInterface
    {
        // We need to work with raw GEDCOM data, as we are looking for errors
        // which may prevent the GedcomRecord objects from working.

        $q1 = DB::table('individuals')
            ->where('i_file', '=', $tree->id())
            ->select(['i_id AS xref', 'i_gedcom AS gedcom', DB::raw("'INDI' AS type")]);
        $q2 = DB::table('families')
            ->where('f_file', '=', $tree->id())
            ->select(['f_id AS xref', 'f_gedcom AS gedcom', DB::raw("'FAM' AS type")]);
        $q3 = DB::table('media')
            ->where('m_file', '=', $tree->id())
            ->select(['m_id AS xref', 'm_gedcom AS gedcom', DB::raw("'OBJE' AS type")]);
        $q4 = DB::table('sources')
            ->where('s_file', '=', $tree->id())
            ->select(['s_id AS xref', 's_gedcom AS gedcom', DB::raw("'SOUR' AS type")]);
        $q5 = DB::table('other')
            ->where('o_file', '=', $tree->id())
            ->whereNotIn('o_type', ['HEAD', 'TRLR'])
            ->select(['o_id AS xref', 'o_gedcom AS gedcom', 'o_type']);
        $q6 = DB::table('change')
            ->where('gedcom_id', '=', $tree->id())
            ->where('status', '=', 'pending')
            ->orderBy('change_id')
            ->select(['xref', 'new_gedcom AS gedcom', DB::raw("'' AS type")]);

        $rows = $q1
            ->unionAll($q2)
            ->unionAll($q3)
            ->unionAll($q4)
            ->unionAll($q5)
            ->unionAll($q6)
            ->get()
            ->map(static function (stdClass $row): stdClass {
                // Extract type for pending record
                if ($row->type === '' && preg_match('/^0 @[^@]*@ ([_A-Z0-9]+)/', $row->gedcom, $match)) {
                    $row->type = $match[1];
                }

                return $row;
            });

        $records = [];

        foreach ($rows as $row) {
            if ($row->gedcom !== '') {
                // existing or updated record
                $records[$row->xref] = $row;
            } else {
                // deleted record
                unset($records[$row->xref]);
            }
        }

        // LOOK FOR BROKEN LINKS
        $XREF_LINKS = [
            'NOTE'          => 'NOTE',
            'SOUR'          => 'SOUR',
            'REPO'          => 'REPO',
            'OBJE'          => 'OBJE',
            'SUBM'          => 'SUBM',
            'FAMC'          => 'FAM',
            'FAMS'          => 'FAM',
            //'ADOP'=>'FAM', // Need to handle this case specially. We may have both ADOP and FAMC links to the same FAM, but only store one.
            'HUSB'          => 'INDI',
            'WIFE'          => 'INDI',
            'CHIL'          => 'INDI',
            'ASSO'          => 'INDI',
            '_ASSO'         => 'INDI',
            // A webtrees extension
            'ALIA'          => 'INDI',
            'AUTH'          => 'INDI',
            // A webtrees extension
            'ANCI'          => 'SUBM',
            'DESI'          => 'SUBM',
            '_WT_OBJE_SORT' => 'OBJE',
            '_LOC'          => '_LOC',
        ];

        $RECORD_LINKS = [
            'INDI' => [
                'NOTE',
                'OBJE',
                'SOUR',
                'SUBM',
                'ASSO',
                '_ASSO',
                'FAMC',
                'FAMS',
                'ALIA',
                '_WT_OBJE_SORT',
                '_LOC',
            ],
            'FAM'  => [
                'NOTE',
                'OBJE',
                'SOUR',
                'SUBM',
                'ASSO',
                '_ASSO',
                'HUSB',
                'WIFE',
                'CHIL',
                '_LOC',
            ],
            'SOUR' => [
                'NOTE',
                'OBJE',
                'REPO',
                'AUTH',
            ],
            'REPO' => ['NOTE'],
            'OBJE' => ['NOTE'],
            // The spec also allows SOUR, but we treat this as a warning
            'NOTE' => [],
            // The spec also allows SOUR, but we treat this as a warning
            'SUBM' => [
                'NOTE',
                'OBJE',
            ],
            'SUBN' => ['SUBM'],
            '_LOC' => [
                'SOUR',
                'OBJE',
                '_LOC',
                'NOTE',
            ],
        ];

        $errors   = [];
        $warnings = [];

        // Generate lists of all links
        $all_links   = [];
        $upper_links = [];
        foreach ($records as $record) {
            $all_links[$record->xref]               = [];
            $upper_links[strtoupper($record->xref)] = $record->xref;
            preg_match_all('/\n\d (' . Gedcom::REGEX_TAG . ') @([^#@\n][^\n@]*)@/', $record->gedcom, $matches, PREG_SET_ORDER);
            foreach ($matches as $match) {
                $all_links[$record->xref][$match[2]] = $match[1];
            }
        }

        foreach ($all_links as $xref1 => $links) {
            $type1 = $records[$xref1]->type;
            foreach ($links as $xref2 => $type2) {
                $type3 = isset($records[$xref2]) ? $records[$xref2]->type : '';
                if (!array_key_exists($xref2, $all_links)) {
                    if (array_key_exists(strtoupper($xref2), $upper_links)) {
                        $warnings[] =
                            $this->checkLinkMessage($tree, $type1, $xref1, $type2, $xref2) . ' ' .
                            /* I18N: placeholders are GEDCOM XREFs, such as R123 */
                            I18N::translate('%1$s does not exist. Did you mean %2$s?', $this->checkLink($tree, $xref2), $this->checkLink($tree, $upper_links[strtoupper($xref2)]));
                    } else {
                        /* I18N: placeholders are GEDCOM XREFs, such as R123 */
                        $errors[] = $this->checkLinkMessage($tree, $type1, $xref1, $type2, $xref2) . ' ' . I18N::translate('%1$s does not exist.', $this->checkLink($tree, $xref2));
                    }
                } elseif ($type2 === 'SOUR' && $type1 === 'NOTE') {
                    // Notes are intended to add explanations and comments to other records. They should not have their own sources.
                } elseif ($type2 === 'SOUR' && $type1 === 'OBJE') {
                    // Media objects are intended to illustrate other records, facts, and source/citations. They should not have their own sources.
                } elseif ($type2 === 'OBJE' && $type1 === 'REPO') {
                    $warnings[] =
                        $this->checkLinkMessage($tree, $type1, $xref1, $type2, $xref2) .
                        ' ' .
                        I18N::translate('This type of link is not allowed here.');
                } elseif (!array_key_exists($type1, $RECORD_LINKS) || !in_array($type2, $RECORD_LINKS[$type1]) || !array_key_exists($type2, $XREF_LINKS)) {
                    $errors[] =
                        $this->checkLinkMessage($tree, $type1, $xref1, $type2, $xref2) .
                        ' ' .
                        I18N::translate('This type of link is not allowed here.');
                } elseif ($XREF_LINKS[$type2] !== $type3) {
                    // Target XREF does exist - but is invalid
                    $errors[] =
                        $this->checkLinkMessage($tree, $type1, $xref1, $type2, $xref2) . ' ' .
                        /* I18N: %1$s is an internal ID number such as R123. %2$s and %3$s are record types, such as INDI or SOUR */
                        I18N::translate('%1$s is a %2$s but a %3$s is expected.', $this->checkLink($tree, $xref2), $this->formatType($type3), $this->formatType($type2));
                } elseif (
                    $type2 === 'FAMC' && (!array_key_exists($xref1, $all_links[$xref2]) || $all_links[$xref2][$xref1] !== 'CHIL') ||
                    $type2 === 'FAMS' && (!array_key_exists($xref1, $all_links[$xref2]) || $all_links[$xref2][$xref1] !== 'HUSB' && $all_links[$xref2][$xref1] !== 'WIFE') ||
                    $type2 === 'CHIL' && (!array_key_exists($xref1, $all_links[$xref2]) || $all_links[$xref2][$xref1] !== 'FAMC') ||
                    $type2 === 'HUSB' && (!array_key_exists($xref1, $all_links[$xref2]) || $all_links[$xref2][$xref1] !== 'FAMS') ||
                    $type2 === 'WIFE' && (!array_key_exists($xref1, $all_links[$xref2]) || $all_links[$xref2][$xref1] !== 'FAMS')
                ) {
                    /* I18N: %1$s and %2$s are internal ID numbers such as R123 */
                    $errors[] = $this->checkLinkMessage($tree, $type1, $xref1, $type2, $xref2) . ' ' . I18N::translate('%1$s does not have a link back to %2$s.', $this->checkLink($tree, $xref2), $this->checkLink($tree, $xref1));
                }
            }
        }

        $title = I18N::translate('Check for errors') . ' — ' . e($tree->title());

        return $this->viewResponse('admin/trees-check', [
            'errors'   => $errors,
            'title'    => $title,
            'tree'     => $tree,
            'warnings' => $warnings,
        ]);
    }

    /**
     * Create a message linking one record to another.
     *
     * @param Tree   $tree
     * @param string $type1
     * @param string $xref1
     * @param string $type2
     * @param string $xref2
     *
     * @return string
     */
    private function checkLinkMessage(Tree $tree, $type1, $xref1, $type2, $xref2): string
    {
        /* I18N: The placeholders are GEDCOM XREFs and tags. e.g. “INDI I123 contains a FAMC link to F234.” */
        return I18N::translate(
            '%1$s %2$s has a %3$s link to %4$s.',
            $this->formatType($type1),
            $this->checkLink($tree, $xref1),
            $this->formatType($type2),
            $this->checkLink($tree, $xref2)
        );
    }

    /**
     * Format a link to a record.
     *
     * @param Tree   $tree
     * @param string $xref
     *
     * @return string
     */
    private function checkLink(Tree $tree, string $xref): string
    {
        return '<b><a href="' . e(route('record', [
                'xref' => $xref,
                'ged'  => $tree->name(),
            ])) . '">' . $xref . '</a></b>';
    }

    /**
     * Format a record type.
     *
     * @param string $type
     *
     * @return string
     */
    private function formatType($type): string
    {
        return '<b title="' . GedcomTag::getLabel($type) . '">' . $type . '</b>';
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function create(ServerRequestInterface $request): ResponseInterface
    {
        $tree_name  = $request->get('tree_name', '');
        $tree_title = $request->get('tree_title', '');

        // We use the tree name as a file name, so no directory separators allowed.
        $tree_name = basename($tree_name);

        if (Tree::findByName($tree_name)) {
            FlashMessages::addMessage(I18N::translate('The family tree “%s” already exists.', e($tree_name)), 'danger');

            $url = route('admin-trees');
        } else {
            $tree = Tree::create($tree_name, $tree_title);
            FlashMessages::addMessage(I18N::translate('The family tree “%s” has been created.', e($tree->name())), 'success');

            $url = route('admin-trees', ['ged' => $tree->name()]);
        }

        return redirect($url);
    }

    /**
     * @param Tree $tree
     *
     * @return ResponseInterface
     */
    public function delete(Tree $tree): ResponseInterface
    {
        /* I18N: %s is the name of a family tree */
        FlashMessages::addMessage(I18N::translate('The family tree “%s” has been deleted.', e($tree->title())), 'success');

        $tree->delete();

        $url = route('admin-trees');

        return redirect($url);
    }

    /**
     * @param Tree $tree
     *
     * @return ResponseInterface
     */
    public function duplicates(Tree $tree): ResponseInterface
    {
        $duplicates = $this->duplicateRecords($tree);

        $title = I18N::translate('Find duplicates') . ' — ' . e($tree->title());

        return $this->viewResponse('admin/trees-duplicates', [
            'duplicates' => $duplicates,
            'title'      => $title,
            'tree'       => $tree,
        ]);
    }

    /**
     * @param Tree $tree
     *
     * @return ResponseInterface
     */
    public function export(Tree $tree): ResponseInterface
    {
        $title = I18N::translate('Export a GEDCOM file') . ' — ' . e($tree->title());

        return $this->viewResponse('admin/trees-export', [
            'title' => $title,
            'tree'  => $tree,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     *
     * @return ResponseInterface
     */
    public function exportClient(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        // Validate user parameters
        $convert          = (bool) $request->get('convert');
        $zip              = (bool) $request->get('zip');
        $media            = (bool) $request->get('media');
        $media_path       = $request->get('media-path', '');
        $privatize_export = $request->get('privatize_export', '');

        $access_levels = [
            'gedadmin' => Auth::PRIV_NONE,
            'user'     => Auth::PRIV_USER,
            'visitor'  => Auth::PRIV_PRIVATE,
            'none'     => Auth::PRIV_HIDE,
        ];

        $access_level = $access_levels[$privatize_export];
        $encoding     = $convert ? 'ANSI' : 'UTF-8';

        // What to call the downloaded file
        $download_filename = $tree->name();
        if (strtolower(substr($download_filename, -4, 4)) !== '.ged') {
            $download_filename .= '.ged';
        }

        if ($zip || $media) {
            // Export the GEDCOM to an in-memory stream.
            $tmp_stream = tmpfile();
            FunctionsExport::exportGedcom($tree, $tmp_stream, $access_level, $media_path, $encoding);
            rewind($tmp_stream);

            // Create a new/empty .ZIP file
            $temp_zip_file  = tempnam(sys_get_temp_dir(), 'webtrees-zip-');
            $zip_filesystem = new Filesystem(new ZipArchiveAdapter($temp_zip_file));
            $zip_filesystem->writeStream($download_filename, $tmp_stream);

            if ($media) {
                $rows = DB::table('media')
                    ->where('m_file', '=', $tree->id())
                    ->get();

                $path = $tree->getPreference('MEDIA_DIRECTORY');

                foreach ($rows as $row) {
                    $record = Media::getInstance($row->m_id, $tree, $row->m_gedcom);
                    if ($record->canShow()) {
                        foreach ($record->mediaFiles() as $media_file) {
                            if (file_exists($media_file->getServerFilename())) {
                                $fp = fopen($media_file->getServerFilename(), 'rb');
                                $zip_filesystem->writeStream($path . $media_file->filename(), $fp);
                                fclose($fp);
                            }
                        }
                    }
                }
            }

            // The ZipArchiveAdapter may or may not close the stream.
            if (is_resource($tmp_stream)) {
                fclose($tmp_stream);
            }

            // Need to force-close the filesystem
            unset($zip_filesystem);

            // Use a stream, so that we do not have to load the entire file into memory.
            $stream   = app(StreamFactoryInterface::class)->createStreamFromFile($temp_zip_file);
            $filename = addcslashes($download_filename, '"') . '.zip';

            return response()
                ->withBody($stream)
                ->withHeader('Content-type', 'application/zip')
                ->withHeader('Content-disposition', 'attachment; filename="' . $filename . '"');
        }

        $resource = fopen('php://temp', 'wb+');
        FunctionsExport::exportGedcom($tree, $resource, $access_level, $media_path, $encoding);
        rewind($resource);

        $charset = $convert ? 'ISO-8859-1' : 'UTF-8';

        /** @var StreamFactoryInterface $response_factory */
        $stream_factory = app(StreamFactoryInterface::class);

        $stream = $stream_factory->createStreamFromStream($resource);

        /** @var ResponseFactoryInterface $response_factory */
        $response_factory = app(ResponseFactoryInterface::class);

        return $response_factory->createResponse()
            ->withBody($stream)
            ->withHeader('Content-type', 'text/x-gedcom; charset=' . $charset)
            ->withHeader('Content-disposition', 'attachment; filename="' . addcslashes($download_filename, '"') . '"');
    }

    /**
     * @param Tree $tree
     *
     * @return ResponseInterface
     */
    public function exportServer(Tree $tree): ResponseInterface
    {
        $filename = WT_DATA_DIR . $tree->name();

        // Force a ".ged" suffix
        if (strtolower(substr($filename, -4)) !== '.ged') {
            $filename .= '.ged';
        }

        try {
            // To avoid partial trees on timeout/diskspace/etc, write to a temporary file first
            $stream = fopen($filename . '.tmp', 'wb');
            $tree->exportGedcom($stream);
            fclose($stream);
            rename($filename . '.tmp', $filename);

            /* I18N: %s is a filename */
            FlashMessages::addMessage(I18N::translate('The family tree has been exported to %s.', Html::filename($filename)), 'success');
        } catch (Throwable $ex) {
            FlashMessages::addMessage(
                I18N::translate('The file %s could not be created.', Html::filename($filename)) . '<hr><samp dir="ltr">' . $ex->getMessage() . '</samp>',
                'danger'
            );
        }

        $url = route('admin-trees', [
            'ged' => $tree->name(),
        ]);

        return redirect($url);
    }

    /**
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     *
     * @return ResponseInterface
     */
    public function importAction(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $source             = $request->get('source');
        $keep_media         = (bool) $request->get('keep_media');
        $WORD_WRAPPED_NOTES = (bool) $request->get('WORD_WRAPPED_NOTES');
        $GEDCOM_MEDIA_PATH  = $request->get('GEDCOM_MEDIA_PATH');

        // Save these choices as defaults
        $tree->setPreference('keep_media', $keep_media ? '1' : '0');
        $tree->setPreference('WORD_WRAPPED_NOTES', $WORD_WRAPPED_NOTES ? '1' : '0');
        $tree->setPreference('GEDCOM_MEDIA_PATH', $GEDCOM_MEDIA_PATH);

        if ($source === 'client') {
            $upload = $request->getUploadedFiles()['tree_name'] ?? null;

            if ($upload instanceof UploadedFile) {
                if ($upload->getError() === UPLOAD_ERR_OK) {
                    $tree->importGedcomFile($upload->getStream(), basename($upload->getClientFilename()));
                } else {
                    FlashMessages::addMessage(Functions::fileUploadErrorText($upload->getError()), 'danger');
                }
            } else {
                FlashMessages::addMessage(I18N::translate('No GEDCOM file was received.'), 'danger');
            }
        }

        if ($source === 'server') {
            $basename = basename($request->get('tree_name'));

            if ($basename) {
                $stream = app(StreamFactoryInterface::class)->createStreamFromFile(WT_DATA_DIR . $basename);
                $tree->importGedcomFile($stream, $basename);
            } else {
                FlashMessages::addMessage(I18N::translate('No GEDCOM file was received.'), 'danger');
            }
        }

        $url = route('admin-trees', ['ged' => $tree->name()]);

        return redirect($url);
    }

    /**
     * @param Tree $tree
     *
     * @return ResponseInterface
     */
    public function importForm(Tree $tree): ResponseInterface
    {
        $default_gedcom_file = $tree->getPreference('gedcom_filename');
        $gedcom_media_path   = $tree->getPreference('GEDCOM_MEDIA_PATH');
        $gedcom_files        = $this->gedcomFiles(WT_DATA_DIR);

        $title = I18N::translate('Import a GEDCOM file') . ' — ' . e($tree->title());

        return $this->viewResponse('admin/trees-import', [
            'data_folder'         => WT_DATA_DIR,
            'default_gedcom_file' => $default_gedcom_file,
            'gedcom_files'        => $gedcom_files,
            'gedcom_media_path'   => $gedcom_media_path,
            'title'               => $title,
        ]);
    }

    /**
     * @param Tree|null $tree
     *
     * @return ResponseInterface
     */
    public function index(Tree $tree = null): ResponseInterface
    {
        $multiple_tree_threshold = (int) Site::getPreference('MULTIPLE_TREE_THRESHOLD', self::MULTIPLE_TREE_THRESHOLD);
        $gedcom_files            = $this->gedcomFiles(WT_DATA_DIR);

        $all_trees = Tree::getAll();

        // On sites with hundreds or thousands of trees, this page becomes very large.
        // Just show the current tree, the default tree, and unimported trees
        if (count($all_trees) >= $multiple_tree_threshold) {
            $all_trees = array_filter($all_trees, static function (Tree $x) use ($tree): bool {
                return $x->getPreference('imported') === '0' || $tree->id() === $x->id() || $x->name() === Site::getPreference('DEFAULT_GEDCOM');
            });
        }

        $default_tree_name  = $this->generateNewTreeName();
        $default_tree_title = I18N::translate('My family tree');

        $title = I18N::translate('Manage family trees');

        return $this->viewResponse('admin/trees', [
            'all_trees'               => $all_trees,
            'default_tree_name'       => $default_tree_name,
            'default_tree_title'      => $default_tree_title,
            'gedcom_files'            => $gedcom_files,
            'multiple_tree_threshold' => $multiple_tree_threshold,
            'title'                   => $title,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function merge(ServerRequestInterface $request): ResponseInterface
    {
        $tree1_name = $request->get('tree1_name');
        $tree2_name = $request->get('tree2_name');

        $tree1 = Tree::findByName($tree1_name);
        $tree2 = Tree::findByName($tree2_name);

        if ($tree1 !== null && $tree2 !== null && $tree1->id() !== $tree2->id()) {
            $xrefs = $this->countCommonXrefs($tree1, $tree2);
        } else {
            $xrefs = 0;
        }

        $tree_list = Tree::getNameList();

        $title = I18N::translate(I18N::translate('Merge family trees'));

        return $this->viewResponse('admin/trees-merge', [
            'tree_list' => $tree_list,
            'tree1'     => $tree1,
            'tree2'     => $tree2,
            'title'     => $title,
            'xrefs'     => $xrefs,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function mergeAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree1_name = $request->get('tree1_name');
        $tree2_name = $request->get('tree2_name');

        $tree1 = Tree::findByName($tree1_name);
        $tree2 = Tree::findByName($tree2_name);

        if ($tree1 !== null && $tree2 !== null && $tree1 !== $tree2 && $this->countCommonXrefs($tree1, $tree2) === 0) {
            (new Builder(DB::connection()))->from('individuals')->insertUsing([
                'i_file',
                'i_id',
                'i_rin',
                'i_sex',
                'i_gedcom',
            ], static function (Builder $query) use ($tree1, $tree2): void {
                $query->select([
                    DB::raw($tree2->id()),
                    'i_id',
                    'i_rin',
                    'i_sex',
                    'i_gedcom',
                ])->from('individuals')
                    ->where('i_file', '=', $tree1->id());
            });

            (new Builder(DB::connection()))->from('families')->insertUsing([
                'f_file',
                'f_id',
                'f_husb',
                'f_wife',
                'f_gedcom',
                'f_numchil',
            ], static function (Builder $query) use ($tree1, $tree2): void {
                $query->select([
                    DB::raw($tree2->id()),
                    'f_id',
                    'f_husb',
                    'f_wife',
                    'f_gedcom',
                    'f_numchil',
                ])->from('families')
                    ->where('f_file', '=', $tree1->id());
            });

            (new Builder(DB::connection()))->from('sources')->insertUsing([
                's_file',
                's_id',
                's_name',
                's_gedcom',
            ], static function (Builder $query) use ($tree1, $tree2): void {
                $query->select([
                    DB::raw($tree2->id()),
                    's_id',
                    's_name',
                    's_gedcom',
                ])->from('sources')
                    ->where('s_file', '=', $tree1->id());
            });

            (new Builder(DB::connection()))->from('media')->insertUsing([
                'm_file',
                'm_id',
                'm_gedcom',
            ], static function (Builder $query) use ($tree1, $tree2): void {
                $query->select([
                    DB::raw($tree2->id()),
                    'm_id',
                    'm_gedcom',
                ])->from('media')
                    ->where('m_file', '=', $tree1->id());
            });

            (new Builder(DB::connection()))->from('media_file')->insertUsing([
                'm_file',
                'm_id',
                'multimedia_file_refn',
                'multimedia_format',
                'source_media_type',
                'descriptive_title',
            ], static function (Builder $query) use ($tree1, $tree2): void {
                $query->select([
                    DB::raw($tree2->id()),
                    'm_id',
                    'multimedia_file_refn',
                    'multimedia_format',
                    'source_media_type',
                    'descriptive_title',
                ])->from('media_file')
                    ->where('m_file', '=', $tree1->id());
            });

            (new Builder(DB::connection()))->from('other')->insertUsing([
                'o_file',
                'o_id',
                'o_type',
                'o_gedcom',
            ], static function (Builder $query) use ($tree1, $tree2): void {
                $query->select([
                    DB::raw($tree2->id()),
                    'o_id',
                    'o_type',
                    'o_gedcom',
                ])->from('other')
                    ->whereNotIn('o_type', ['HEAD', 'TRLR'])
                    ->where('o_file', '=', $tree1->id());
            });

            (new Builder(DB::connection()))->from('name')->insertUsing([
                'n_file',
                'n_id',
                'n_num',
                'n_type',
                'n_sort',
                'n_full',
                'n_surname',
                'n_surn',
                'n_givn',
                'n_soundex_givn_std',
                'n_soundex_surn_std',
                'n_soundex_givn_dm',
                'n_soundex_surn_dm',
            ], static function (Builder $query) use ($tree1, $tree2): void {
                $query->select([
                    DB::raw($tree2->id()),
                    'n_id',
                    'n_num',
                    'n_type',
                    'n_sort',
                    'n_full',
                    'n_surname',
                    'n_surn',
                    'n_givn',
                    'n_soundex_givn_std',
                    'n_soundex_surn_std',
                    'n_soundex_givn_dm',
                    'n_soundex_surn_dm',
                ])->from('name')
                    ->where('n_file', '=', $tree1->id());
            });

            // @TODO placelinks is harder than the others...

            (new Builder(DB::connection()))->from('dates')->insertUsing([
                'd_file',
                'd_gid',
                'd_day',
                'd_month',
                'd_mon',
                'd_year',
                'd_julianday1',
                'd_julianday2',
                'd_fact',
                'd_type',
            ], static function (Builder $query) use ($tree1, $tree2): void {
                $query->select([
                    DB::raw($tree2->id()),
                    'd_gid',
                    'd_day',
                    'd_month',
                    'd_mon',
                    'd_year',
                    'd_julianday1',
                    'd_julianday2',
                    'd_fact',
                    'd_type',
                ])->from('dates')
                    ->where('d_file', '=', $tree1->id());
            });

            (new Builder(DB::connection()))->from('link')->insertUsing([
                'l_file',
                'l_from',
                'l_type',
                'l_to',
            ], static function (Builder $query) use ($tree1, $tree2): void {
                $query->select([
                    DB::raw($tree2->id()),
                    'l_from',
                    'l_type',
                    'l_to',
                ])->from('link')
                    ->where('l_file', '=', $tree1->id());
            });

            FlashMessages::addMessage(I18N::translate('The family trees have been merged successfully.'), 'success');

            $url = route('admin-trees', [
                'ged' => $tree2->name(),
            ]);
        } else {
            $url = route('admin-trees-merge', [
                'tree1_name' => $tree1->name(),
                'tree2_name' => $tree2->name(),
            ]);
        }

        return redirect($url);
    }

    /**
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     *
     * @return ResponseInterface
     */
    public function places(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $search  = $request->get('search', '');
        $replace = $request->get('replace', '');

        if ($search !== '' && $replace !== '') {
            $changes = $this->changePlacesPreview($tree, $search, $replace);
        } else {
            $changes = [];
        }

        /* I18N: Renumber the records in a family tree */
        $title = I18N::translate('Update place names') . ' — ' . e($tree->title());

        return $this->viewResponse('admin/trees-places', [
            'changes' => $changes,
            'replace' => $replace,
            'search'  => $search,
            'title'   => $title,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     *
     * @return ResponseInterface
     */
    public function placesAction(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $search  = $request->get('search', '');
        $replace = $request->get('replace', '');

        $changes = $this->changePlacesUpdate($tree, $search, $replace);

        $feedback = I18N::translate('The following places have been changed:') . '<ul>';
        foreach ($changes as $old_place => $new_place) {
            $feedback .= '<li>' . e($old_place) . ' &rarr; ' . e($new_place) . '</li>';
        }
        $feedback .= '</ul>';

        FlashMessages::addMessage($feedback, 'success');

        $url = route('admin-trees-places', [
            'ged'     => $tree->name(),
            'replace' => $replace,
            'search'  => $search,
        ]);

        return redirect($url);
    }

    /**
     * @param Tree $tree
     *
     * @return ResponseInterface
     */
    public function preferences(Tree $tree): ResponseInterface
    {
        $french_calendar_start    = new Date('22 SEP 1792');
        $french_calendar_end      = new Date('31 DEC 1805');
        $gregorian_calendar_start = new Date('15 OCT 1582');

        $surname_list_styles = [
            /* I18N: Layout option for lists of names */
            'style1' => I18N::translate('list'),
            /* I18N: Layout option for lists of names */
            'style2' => I18N::translate('table'),
            /* I18N: Layout option for lists of names */
            'style3' => I18N::translate('tag cloud'),
        ];

        $page_layouts = [
            /* I18N: page orientation */
            0 => I18N::translate('Portrait'),
            /* I18N: page orientation */
            1 => I18N::translate('Landscape'),
        ];

        $formats = [
            /* I18N: None of the other options */
            ''         => I18N::translate('none'),
            /* I18N: https://en.wikipedia.org/wiki/Markdown */
            'markdown' => I18N::translate('markdown'),
        ];

        $source_types = [
            0 => I18N::translate('none'),
            1 => I18N::translate('facts'),
            2 => I18N::translate('records'),
        ];

        $theme_options = $this->themeOptions();

        $privacy_options = [
            Auth::PRIV_USER => I18N::translate('Show to members'),
            Auth::PRIV_NONE => I18N::translate('Show to managers'),
            Auth::PRIV_HIDE => I18N::translate('Hide from everyone'),
        ];

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
            ['SOUR', 'REPO', 'OBJE', '_PRIM', 'NOTE', 'SUBM', 'SUBN', '_UID', 'CHAN']
        ));

        $all_tags = [];
        foreach ($tags as $tag) {
            if ($tag) {
                $all_tags[$tag] = GedcomTag::getLabel($tag);
            }
        }

        uasort($all_tags, '\Fisharebest\Webtrees\I18N::strcasecmp');

        // For historical reasons, we have two fields in one
        $calendar_formats = explode('_and_', $tree->getPreference('CALENDAR_FORMAT') . '_and_');

        // Split into separate fields
        $relatives_events = explode(',', $tree->getPreference('SHOW_RELATIVES_EVENTS'));

        $pedigree_individual = Individual::getInstance($tree->getPreference('PEDIGREE_ROOT_ID'), $tree);

        $members = $this->user_service->all()->filter(static function (UserInterface $user) use ($tree): bool {
            return Auth::isMember($tree, $user);
        });

        $all_fam_facts  = GedcomTag::getPicklistFacts('FAM');
        $all_indi_facts = GedcomTag::getPicklistFacts('INDI');
        $all_name_facts = GedcomTag::getPicklistFacts('NAME');
        $all_plac_facts = GedcomTag::getPicklistFacts('PLAC');
        $all_repo_facts = GedcomTag::getPicklistFacts('REPO');
        $all_sour_facts = GedcomTag::getPicklistFacts('SOUR');

        $all_surname_traditions = SurnameTradition::allDescriptions();

        $tree_count = count(Tree::getAll());

        $title = I18N::translate('Preferences') . ' — ' . e($tree->title());

        return $this->viewResponse('admin/trees-preferences', [
            'all_fam_facts'            => $all_fam_facts,
            'all_indi_facts'           => $all_indi_facts,
            'all_name_facts'           => $all_name_facts,
            'all_plac_facts'           => $all_plac_facts,
            'all_repo_facts'           => $all_repo_facts,
            'all_sour_facts'           => $all_sour_facts,
            'all_surname_traditions'   => $all_surname_traditions,
            'calendar_formats'         => $calendar_formats,
            'data_folder'              => WT_DATA_DIR,
            'formats'                  => $formats,
            'french_calendar_end'      => $french_calendar_end,
            'french_calendar_start'    => $french_calendar_start,
            'gregorian_calendar_start' => $gregorian_calendar_start,
            'members'                  => $members,
            'page_layouts'             => $page_layouts,
            'pedigree_individual'      => $pedigree_individual,
            'privacy_options'          => $privacy_options,
            'relatives_events'         => $relatives_events,
            'source_types'             => $source_types,
            'surname_list_styles'      => $surname_list_styles,
            'theme_options'            => $theme_options,
            'title'                    => $title,
            'tree'                     => $tree,
            'tree_count'               => $tree_count,
        ]);
    }

    /**
     * @param Tree $tree
     *
     * @return ResponseInterface
     */
    public function renumber(Tree $tree): ResponseInterface
    {
        $xrefs = $this->duplicateXrefs($tree);

        /* I18N: Renumber the records in a family tree */
        $title = I18N::translate('Renumber family tree') . ' — ' . e($tree->title());

        return $this->viewResponse('admin/trees-renumber', [
            'title' => $title,
            'xrefs' => $xrefs,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     *
     * @return ResponseInterface
     */
    public function preferencesUpdate(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        // Coming soon
        if ((bool) $request->get('all_trees')) {
            FlashMessages::addMessage(I18N::translate('The preferences for all family trees have been updated.'), 'success');
        }
        if ((bool) $request->get('new_trees')) {
            FlashMessages::addMessage(I18N::translate('The preferences for new family trees have been updated.'), 'success');
        }

        $tree->setPreference('ADVANCED_NAME_FACTS', implode(',', $request->get('ADVANCED_NAME_FACTS', [])));
        $tree->setPreference('ADVANCED_PLAC_FACTS', implode(',', $request->get('ADVANCED_PLAC_FACTS', [])));
        $tree->setPreference('ALLOW_THEME_DROPDOWN', (string) (bool) $request->get('ALLOW_THEME_DROPDOWN'));
        // For backwards compatibility with webtrees 1.x we store the two calendar formats in one variable
        // e.g. "gregorian_and_jewish"
        $tree->setPreference('CALENDAR_FORMAT', implode('_and_', array_unique([
            $request->get('CALENDAR_FORMAT0', 'none'),
            $request->get('CALENDAR_FORMAT1', 'none'),
        ])));
        $tree->setPreference('CHART_BOX_TAGS', implode(',', $request->get('CHART_BOX_TAGS', [])));
        $tree->setPreference('CONTACT_USER_ID', $request->get('CONTACT_USER_ID'));
        $tree->setPreference('EXPAND_NOTES', (string) (bool) $request->get('EXPAND_NOTES'));
        $tree->setPreference('EXPAND_SOURCES', (string) (bool) $request->get('EXPAND_SOURCES'));
        $tree->setPreference('FAM_FACTS_ADD', implode(',', $request->get('FAM_FACTS_ADD', [])));
        $tree->setPreference('FAM_FACTS_QUICK', implode(',', $request->get('FAM_FACTS_QUICK', [])));
        $tree->setPreference('FAM_FACTS_UNIQUE', implode(',', $request->get('FAM_FACTS_UNIQUE', [])));
        $tree->setPreference('FULL_SOURCES', (string) (bool) $request->get('FULL_SOURCES'));
        $tree->setPreference('FORMAT_TEXT', $request->get('FORMAT_TEXT'));
        $tree->setPreference('GENERATE_UIDS', (string) (bool) $request->get('GENERATE_UIDS'));
        $tree->setPreference('GEONAMES_ACCOUNT', $request->get('GEONAMES_ACCOUNT'));
        $tree->setPreference('HIDE_GEDCOM_ERRORS', (string) (bool) $request->get('HIDE_GEDCOM_ERRORS'));
        $tree->setPreference('INDI_FACTS_ADD', implode(',', $request->get('INDI_FACTS_ADD', [])));
        $tree->setPreference('INDI_FACTS_QUICK', implode(',', $request->get('INDI_FACTS_QUICK', [])));
        $tree->setPreference('INDI_FACTS_UNIQUE', implode(',', $request->get('INDI_FACTS_UNIQUE', [])));
        $tree->setPreference('LANGUAGE', $request->get('LANGUAGE'));
        $tree->setPreference('MEDIA_UPLOAD', $request->get('MEDIA_UPLOAD'));
        $tree->setPreference('META_DESCRIPTION', $request->get('META_DESCRIPTION'));
        $tree->setPreference('META_TITLE', $request->get('META_TITLE'));
        $tree->setPreference('NO_UPDATE_CHAN', (string) (bool) $request->get('NO_UPDATE_CHAN'));
        $tree->setPreference('PEDIGREE_ROOT_ID', $request->get('PEDIGREE_ROOT_ID'));
        $tree->setPreference('PREFER_LEVEL2_SOURCES', $request->get('PREFER_LEVEL2_SOURCES'));
        $tree->setPreference('QUICK_REQUIRED_FACTS', implode(',', $request->get('QUICK_REQUIRED_FACTS', [])));
        $tree->setPreference('QUICK_REQUIRED_FAMFACTS', implode(',', $request->get('QUICK_REQUIRED_FAMFACTS', [])));
        $tree->setPreference('REPO_FACTS_ADD', implode(',', $request->get('REPO_FACTS_ADD', [])));
        $tree->setPreference('REPO_FACTS_QUICK', implode(',', $request->get('REPO_FACTS_QUICK', [])));
        $tree->setPreference('REPO_FACTS_UNIQUE', implode(',', $request->get('REPO_FACTS_UNIQUE', [])));
        $tree->setPreference('SHOW_COUNTER', (string) (bool) $request->get('SHOW_COUNTER'));
        $tree->setPreference('SHOW_EST_LIST_DATES', (string) (bool) $request->get('SHOW_EST_LIST_DATES'));
        $tree->setPreference('SHOW_FACT_ICONS', (string) (bool) $request->get('SHOW_FACT_ICONS'));
        $tree->setPreference('SHOW_GEDCOM_RECORD', (string) (bool) $request->get('SHOW_GEDCOM_RECORD'));
        $tree->setPreference('SHOW_HIGHLIGHT_IMAGES', (string) (bool) $request->get('SHOW_HIGHLIGHT_IMAGES'));
        $tree->setPreference('SHOW_LAST_CHANGE', (string) (bool) $request->get('SHOW_LAST_CHANGE'));
        $tree->setPreference('SHOW_MEDIA_DOWNLOAD', $request->get('SHOW_MEDIA_DOWNLOAD'));
        $tree->setPreference('SHOW_NO_WATERMARK', $request->get('SHOW_NO_WATERMARK'));
        $tree->setPreference('SHOW_PARENTS_AGE', (string) (bool) $request->get('SHOW_PARENTS_AGE'));
        $tree->setPreference('SHOW_PEDIGREE_PLACES', $request->get('SHOW_PEDIGREE_PLACES'));
        $tree->setPreference('SHOW_PEDIGREE_PLACES_SUFFIX', (string) (bool) $request->get('SHOW_PEDIGREE_PLACES_SUFFIX'));
        $tree->setPreference('SHOW_RELATIVES_EVENTS', implode(',', $request->get('SHOW_RELATIVES_EVENTS', [])));
        $tree->setPreference('SOUR_FACTS_ADD', implode(',', $request->get('SOUR_FACTS_ADD', [])));
        $tree->setPreference('SOUR_FACTS_QUICK', implode(',', $request->get('SOUR_FACTS_QUICK', [])));
        $tree->setPreference('SOUR_FACTS_UNIQUE', implode(',', $request->get('SOUR_FACTS_UNIQUE', [])));
        $tree->setPreference('SUBLIST_TRIGGER_I', (string) (int) $request->get('SUBLIST_TRIGGER_I', 200));
        $tree->setPreference('SURNAME_LIST_STYLE', $request->get('SURNAME_LIST_STYLE'));
        $tree->setPreference('SURNAME_TRADITION', $request->get('SURNAME_TRADITION'));
        $tree->setPreference('THEME_DIR', $request->get('THEME_DIR'));
        $tree->setPreference('USE_SILHOUETTE', (string) (bool) $request->get('USE_SILHOUETTE'));
        $tree->setPreference('WEBMASTER_USER_ID', $request->get('WEBMASTER_USER_ID'));
        $tree->setPreference('WEBTREES_EMAIL', $request->get('WEBTREES_EMAIL'));
        $tree->setPreference('title', $request->get('title'));

        // Only accept valid folders for MEDIA_DIRECTORY
        $MEDIA_DIRECTORY = preg_replace('/[\/\\\\]+/', '/', $request->get('MEDIA_DIRECTORY') . '/');
        if (substr($MEDIA_DIRECTORY, 0, 1) === '/') {
            $MEDIA_DIRECTORY = substr($MEDIA_DIRECTORY, 1);
        }

        if ($MEDIA_DIRECTORY) {
            if (is_dir(WT_DATA_DIR . $MEDIA_DIRECTORY)) {
                $tree->setPreference('MEDIA_DIRECTORY', $MEDIA_DIRECTORY);
            } elseif (File::mkdir(WT_DATA_DIR . $MEDIA_DIRECTORY)) {
                $tree->setPreference('MEDIA_DIRECTORY', $MEDIA_DIRECTORY);
                FlashMessages::addMessage(I18N::translate('The folder %s has been created.', Html::filename(WT_DATA_DIR . $MEDIA_DIRECTORY)), 'info');
            } else {
                FlashMessages::addMessage(I18N::translate('The folder %s does not exist, and it could not be created.', Html::filename(WT_DATA_DIR . $MEDIA_DIRECTORY)), 'danger');
            }
        }

        $gedcom = $request->get('gedcom');
        if ($gedcom && $gedcom !== $tree->name()) {
            try {
                DB::table('gedcom')
                    ->where('gedcom_id', '=', $tree->id())
                    ->update(['gedcom_name' => $gedcom]);

                DB::table('site_setting')
                    ->where('setting_name', '=', 'DEFAULT_GEDCOM')
                    ->where('setting_value', '=', $tree->name())
                    ->update(['setting_value' => $gedcom]);
            } catch (Exception $ex) {
                // Probably a duplicate name.
            }
        }

        FlashMessages::addMessage(I18N::translate('The preferences for the family tree “%s” have been updated.', e($tree->title())), 'success');

        $url = route('admin-trees', ['ged' => $tree->name()]);

        return redirect($url);
    }

    /**
     * @param Tree           $tree
     * @param TimeoutService $timeout_service
     *
     * @return ResponseInterface
     */
    public function renumberAction(Tree $tree, TimeoutService $timeout_service): ResponseInterface
    {
        $xrefs = $this->duplicateXrefs($tree);

        foreach ($xrefs as $old_xref => $type) {
            $new_xref = $tree->getNewXref();
            switch ($type) {
                case 'INDI':
                    DB::table('individuals')
                        ->where('i_file', '=', $tree->id())
                        ->where('i_id', '=', $old_xref)
                        ->update([
                            'i_id'     => $new_xref,
                            'i_gedcom' => DB::raw("REPLACE(i_gedcom, '0 @$old_xref@ INDI', '0 @$new_xref@ INDI')"),
                        ]);

                    DB::table('families')
                        ->where('f_husb', '=', $old_xref)
                        ->where('f_file', '=', $tree->id())
                        ->update([
                            'f_husb'   => $new_xref,
                            'f_gedcom' => DB::raw("REPLACE(f_gedcom, ' HUSB @$old_xref@', ' HUSB @$new_xref@')"),
                        ]);

                    DB::table('families')
                        ->where('f_wife', '=', $old_xref)
                        ->where('f_file', '=', $tree->id())
                        ->update([
                            'f_wife'   => $new_xref,
                            'f_gedcom' => DB::raw("REPLACE(f_gedcom, ' WIFE @$old_xref@', ' WIFE @$new_xref@')"),
                        ]);

                    // Other links from families to individuals
                    foreach (['CHIL', 'ASSO', '_ASSO'] as $tag) {
                        DB::table('families')
                            ->join('link', static function (JoinClause $join): void {
                                $join
                                    ->on('l_file', '=', 'f_file')
                                    ->on('l_from', '=', 'f_id');
                            })
                            ->where('l_to', '=', $old_xref)
                            ->where('l_type', '=', $tag)
                            ->where('f_file', '=', $tree->id())
                            ->update([
                                'f_gedcom' => DB::raw("REPLACE(f_gedcom, ' $tag @$old_xref@', ' $tag @$new_xref@')"),
                            ]);
                    }

                    // Links from individuals to individuals
                    foreach (['ALIA', 'ASSO', '_ASSO'] as $tag) {
                        DB::table('individuals')
                            ->join('link', static function (JoinClause $join): void {
                                $join
                                    ->on('l_file', '=', 'i_file')
                                    ->on('l_from', '=', 'i_id');
                            })
                            ->where('link.l_to', '=', $old_xref)
                            ->where('link.l_type', '=', $tag)
                            ->where('i_file', '=', $tree->id())
                            ->update([
                                'i_gedcom' => DB::raw("REPLACE(i_gedcom, ' $tag @$old_xref@', ' $tag @$new_xref@')"),
                            ]);
                    }

                    DB::table('placelinks')
                        ->where('pl_file', '=', $tree->id())
                        ->where('pl_gid', '=', $old_xref)
                        ->update([
                            'pl_gid' => $new_xref,
                        ]);

                    DB::table('dates')
                        ->where('d_file', '=', $tree->id())
                        ->where('d_gid', '=', $old_xref)
                        ->update([
                            'd_gid' => $new_xref,
                        ]);

                    DB::table('user_gedcom_setting')
                        ->where('gedcom_id', '=', $tree->id())
                        ->where('setting_value', '=', $old_xref)
                        ->whereIn('setting_name', ['gedcomid', 'rootid'])
                        ->update([
                            'setting_value' => $new_xref,
                        ]);
                    break;

                case 'FAM':
                    DB::table('families')
                        ->where('f_file', '=', $tree->id())
                        ->where('f_id', '=', $old_xref)
                        ->update([
                            'f_id'     => $new_xref,
                            'f_gedcom' => DB::raw("REPLACE(f_gedcom, '0 @$old_xref@ FAM', '0 @$new_xref@ FAM')"),
                        ]);

                    // Links from individuals to families
                    foreach (['FAMC', 'FAMS'] as $tag) {
                        DB::table('individuals')
                            ->join('link', static function (JoinClause $join): void {
                                $join
                                    ->on('l_file', '=', 'i_file')
                                    ->on('l_from', '=', 'i_id');
                            })
                            ->where('l_to', '=', $old_xref)
                            ->where('l_type', '=', $tag)
                            ->where('i_file', '=', $tree->id())
                            ->update([
                                'i_gedcom' => DB::raw("REPLACE(i_gedcom, ' $tag @$old_xref@', ' $tag @$new_xref@')"),
                            ]);
                    }

                    DB::table('placelinks')
                        ->where('pl_file', '=', $tree->id())
                        ->where('pl_gid', '=', $old_xref)
                        ->update([
                            'pl_gid' => $new_xref,
                        ]);

                    DB::table('dates')
                        ->where('d_file', '=', $tree->id())
                        ->where('d_gid', '=', $old_xref)
                        ->update([
                            'd_gid' => $new_xref,
                        ]);
                    break;

                case 'SOUR':
                    DB::table('sources')
                        ->where('s_file', '=', $tree->id())
                        ->where('s_id', '=', $old_xref)
                        ->update([
                            's_id'     => $new_xref,
                            's_gedcom' => DB::raw("REPLACE(s_gedcom, '0 @$old_xref@ SOUR', '0 @$new_xref@ SOUR')"),
                        ]);

                    DB::table('individuals')
                        ->join('link', static function (JoinClause $join): void {
                            $join
                                ->on('l_file', '=', 'i_file')
                                ->on('l_from', '=', 'i_id');
                        })
                        ->where('l_to', '=', $old_xref)
                        ->where('l_type', '=', 'SOUR')
                        ->where('i_file', '=', $tree->id())
                        ->update([
                            'i_gedcom' => DB::raw("REPLACE(i_gedcom, ' SOUR @$old_xref@', ' SOUR @$new_xref@')"),
                        ]);

                    DB::table('families')
                        ->join('link', static function (JoinClause $join): void {
                            $join
                                ->on('l_file', '=', 'f_file')
                                ->on('l_from', '=', 'f_id');
                        })
                        ->where('l_to', '=', $old_xref)
                        ->where('l_type', '=', 'SOUR')
                        ->where('f_file', '=', $tree->id())
                        ->update([
                            'f_gedcom' => DB::raw("REPLACE(f_gedcom, ' SOUR @$old_xref@', ' SOUR @$new_xref@')"),
                        ]);

                    DB::table('media')
                        ->join('link', static function (JoinClause $join): void {
                            $join
                                ->on('l_file', '=', 'm_file')
                                ->on('l_from', '=', 'm_id');
                        })
                        ->where('l_to', '=', $old_xref)
                        ->where('l_type', '=', 'SOUR')
                        ->where('m_file', '=', $tree->id())
                        ->update([
                            'm_gedcom' => DB::raw("REPLACE(m_gedcom, ' SOUR @$old_xref@', ' SOUR @$new_xref@')"),
                        ]);

                    DB::table('other')
                        ->join('link', static function (JoinClause $join): void {
                            $join
                                ->on('l_file', '=', 'o_file')
                                ->on('l_from', '=', 'o_id');
                        })
                        ->where('l_to', '=', $old_xref)
                        ->where('l_type', '=', 'SOUR')
                        ->where('o_file', '=', $tree->id())
                        ->update([
                            'o_gedcom' => DB::raw("REPLACE(o_gedcom, ' SOUR @$old_xref@', ' SOUR @$new_xref@')"),
                        ]);
                    break;
                case 'REPO':
                    DB::table('other')
                        ->where('o_file', '=', $tree->id())
                        ->where('o_id', '=', $old_xref)
                        ->where('o_type', '=', 'REPO')
                        ->update([
                            'o_id'     => $new_xref,
                            'o_gedcom' => DB::raw("REPLACE(o_gedcom, '0 @$old_xref@ REPO', '0 @$new_xref@ REPO')"),
                        ]);

                    DB::table('sources')
                        ->join('link', static function (JoinClause $join): void {
                            $join
                                ->on('l_file', '=', 's_file')
                                ->on('l_from', '=', 's_id');
                        })
                        ->where('l_to', '=', $old_xref)
                        ->where('l_type', '=', 'REPO')
                        ->where('s_file', '=', $tree->id())
                        ->update([
                            's_gedcom' => DB::raw("REPLACE(s_gedcom, ' REPO @$old_xref@', ' REPO @$new_xref@')"),
                        ]);
                    break;

                case 'NOTE':
                    DB::table('other')
                        ->where('o_file', '=', $tree->id())
                        ->where('o_id', '=', $old_xref)
                        ->where('o_type', '=', 'NOTE')
                        ->update([
                            'o_id'     => $new_xref,
                            'o_gedcom' => DB::raw("REPLACE(o_gedcom, '0 @$old_xref@ NOTE', '0 @$new_xref@ NOTE')"),
                        ]);

                    DB::table('individuals')
                        ->join('link', static function (JoinClause $join): void {
                            $join
                                ->on('l_file', '=', 'i_file')
                                ->on('l_from', '=', 'i_id');
                        })
                        ->where('l_to', '=', $old_xref)
                        ->where('l_type', '=', 'NOTE')
                        ->where('i_file', '=', $tree->id())
                        ->update([
                            'i_gedcom' => DB::raw("REPLACE(i_gedcom, ' NOTE @$old_xref@', ' NOTE @$new_xref@')"),
                        ]);

                    DB::table('families')
                        ->join('link', static function (JoinClause $join): void {
                            $join
                                ->on('l_file', '=', 'f_file')
                                ->on('l_from', '=', 'f_id');
                        })
                        ->where('l_to', '=', $old_xref)
                        ->where('l_type', '=', 'NOTE')
                        ->where('f_file', '=', $tree->id())
                        ->update([
                            'f_gedcom' => DB::raw("REPLACE(f_gedcom, ' NOTE @$old_xref@', ' NOTE @$new_xref@')"),
                        ]);

                    DB::table('media')
                        ->join('link', static function (JoinClause $join): void {
                            $join
                                ->on('l_file', '=', 'm_file')
                                ->on('l_from', '=', 'm_id');
                        })
                        ->where('l_to', '=', $old_xref)
                        ->where('l_type', '=', 'NOTE')
                        ->where('m_file', '=', $tree->id())
                        ->update([
                            'm_gedcom' => DB::raw("REPLACE(m_gedcom, ' NOTE @$old_xref@', ' NOTE @$new_xref@')"),
                        ]);

                    DB::table('sources')
                        ->join('link', static function (JoinClause $join): void {
                            $join
                                ->on('l_file', '=', 's_file')
                                ->on('l_from', '=', 's_id');
                        })
                        ->where('l_to', '=', $old_xref)
                        ->where('l_type', '=', 'NOTE')
                        ->where('s_file', '=', $tree->id())
                        ->update([
                            's_gedcom' => DB::raw("REPLACE(s_gedcom, ' NOTE @$old_xref@', ' NOTE @$new_xref@')"),
                        ]);

                    DB::table('other')
                        ->join('link', static function (JoinClause $join): void {
                            $join
                                ->on('l_file', '=', 'o_file')
                                ->on('l_from', '=', 'o_id');
                        })
                        ->where('l_to', '=', $old_xref)
                        ->where('l_type', '=', 'NOTE')
                        ->where('o_file', '=', $tree->id())
                        ->update([
                            'o_gedcom' => DB::raw("REPLACE(o_gedcom, ' NOTE @$old_xref@', ' NOTE @$new_xref@')"),
                        ]);
                    break;

                case 'OBJE':
                    DB::table('media')
                        ->where('m_file', '=', $tree->id())
                        ->where('m_id', '=', $old_xref)
                        ->update([
                            'm_id'     => $new_xref,
                            'm_gedcom' => DB::raw("REPLACE(m_gedcom, '0 @$old_xref@ OBJE', '0 @$new_xref@ OBJE')"),
                        ]);

                    DB::table('media_file')
                        ->where('m_file', '=', $tree->id())
                        ->where('m_id', '=', $old_xref)
                        ->update([
                            'm_id' => $new_xref,
                        ]);

                    DB::table('individuals')
                        ->join('link', static function (JoinClause $join): void {
                            $join
                                ->on('l_file', '=', 'i_file')
                                ->on('l_from', '=', 'i_id');
                        })
                        ->where('l_to', '=', $old_xref)
                        ->where('l_type', '=', 'OBJE')
                        ->where('i_file', '=', $tree->id())
                        ->update([
                            'i_gedcom' => DB::raw("REPLACE(i_gedcom, ' OBJE @$old_xref@', ' OBJE @$new_xref@')"),
                        ]);

                    DB::table('families')
                        ->join('link', static function (JoinClause $join): void {
                            $join
                                ->on('l_file', '=', 'f_file')
                                ->on('l_from', '=', 'f_id');
                        })
                        ->where('l_to', '=', $old_xref)
                        ->where('l_type', '=', 'OBJE')
                        ->where('f_file', '=', $tree->id())
                        ->update([
                            'f_gedcom' => DB::raw("REPLACE(f_gedcom, ' OBJE @$old_xref@', ' OBJE @$new_xref@')"),
                        ]);

                    DB::table('sources')
                        ->join('link', static function (JoinClause $join): void {
                            $join
                                ->on('l_file', '=', 's_file')
                                ->on('l_from', '=', 's_id');
                        })
                        ->where('l_to', '=', $old_xref)
                        ->where('l_type', '=', 'OBJE')
                        ->where('s_file', '=', $tree->id())
                        ->update([
                            's_gedcom' => DB::raw("REPLACE(s_gedcom, ' OBJE @$old_xref@', ' OBJE @$new_xref@')"),
                        ]);

                    DB::table('other')
                        ->join('link', static function (JoinClause $join): void {
                            $join
                                ->on('l_file', '=', 'o_file')
                                ->on('l_from', '=', 'o_id');
                        })
                        ->where('l_to', '=', $old_xref)
                        ->where('l_type', '=', 'OBJE')
                        ->where('o_file', '=', $tree->id())
                        ->update([
                            'o_gedcom' => DB::raw("REPLACE(o_gedcom, ' OBJE @$old_xref@', ' OBJE @$new_xref@')"),
                        ]);
                    break;

                default:
                    DB::table('other')
                        ->where('o_file', '=', $tree->id())
                        ->where('o_id', '=', $old_xref)
                        ->where('o_type', '=', $type)
                        ->update([
                            'o_id'     => $new_xref,
                            'o_gedcom' => DB::raw("REPLACE(o_gedcom, '0 @$old_xref@ $type', '0 @$new_xref@ $type')"),
                        ]);

                    DB::table('individuals')
                        ->join('link', static function (JoinClause $join): void {
                            $join
                                ->on('l_file', '=', 'i_file')
                                ->on('l_from', '=', 'i_id');
                        })
                        ->where('l_to', '=', $old_xref)
                        ->where('l_type', '=', $type)
                        ->where('i_file', '=', $tree->id())
                        ->update([
                            'i_gedcom' => DB::raw("REPLACE(i_gedcom, ' $type @$old_xref@', ' $type @$new_xref@')"),
                        ]);

                    DB::table('families')
                        ->join('link', static function (JoinClause $join): void {
                            $join
                                ->on('l_file', '=', 'f_file')
                                ->on('l_from', '=', 'f_id');
                        })
                        ->where('l_to', '=', $old_xref)
                        ->where('l_type', '=', $type)
                        ->where('f_file', '=', $tree->id())
                        ->update([
                            'f_gedcom' => DB::raw("REPLACE(f_gedcom, ' $type @$old_xref@', ' $type @$new_xref@')"),
                        ]);

                    DB::table('media')
                        ->join('link', static function (JoinClause $join): void {
                            $join
                                ->on('l_file', '=', 'm_file')
                                ->on('l_from', '=', 'm_id');
                        })
                        ->where('l_to', '=', $old_xref)
                        ->where('l_type', '=', $type)
                        ->where('m_file', '=', $tree->id())
                        ->update([
                            'm_gedcom' => DB::raw("REPLACE(m_gedcom, ' $type @$old_xref@', ' $type @$new_xref@')"),
                        ]);

                    DB::table('sources')
                        ->join('link', static function (JoinClause $join): void {
                            $join
                                ->on('l_file', '=', 's_file')
                                ->on('l_from', '=', 's_id');
                        })
                        ->where('l_to', '=', $old_xref)
                        ->where('l_type', '=', $type)
                        ->where('s_file', '=', $tree->id())
                        ->update([
                            's_gedcom' => DB::raw("REPLACE(s_gedcom, ' $type @$old_xref@', ' $type @$new_xref@')"),
                        ]);

                    DB::table('other')
                        ->join('link', static function (JoinClause $join): void {
                            $join
                                ->on('l_file', '=', 'o_file')
                                ->on('l_from', '=', 'o_id');
                        })
                        ->where('l_to', '=', $old_xref)
                        ->where('l_type', '=', $type)
                        ->where('o_file', '=', $tree->id())
                        ->update([
                            'o_gedcom' => DB::raw("REPLACE(o_gedcom, ' $type @$old_xref@', ' $type @$new_xref@')"),
                        ]);
                    break;
            }

            DB::table('name')
                ->where('n_file', '=', $tree->id())
                ->where('n_id', '=', $old_xref)
                ->update([
                    'n_id' => $new_xref,
                ]);

            DB::table('default_resn')
                ->where('gedcom_id', '=', $tree->id())
                ->where('xref', '=', $old_xref)
                ->update([
                    'xref' => $new_xref,
                ]);

            DB::table('hit_counter')
                ->where('gedcom_id', '=', $tree->id())
                ->where('page_parameter', '=', $old_xref)
                ->update([
                    'page_parameter' => $new_xref,
                ]);

            DB::table('link')
                ->where('l_file', '=', $tree->id())
                ->where('l_from', '=', $old_xref)
                ->update([
                    'l_from' => $new_xref,
                ]);

            DB::table('link')
                ->where('l_file', '=', $tree->id())
                ->where('l_to', '=', $old_xref)
                ->update([
                    'l_to' => $new_xref,
                ]);

            DB::table('favorite')
                ->where('gedcom_id', '=', $tree->id())
                ->where('xref', '=', $old_xref)
                ->update([
                    'xref' => $new_xref,
                ]);

            unset($xrefs[$old_xref]);

            // How much time do we have left?
            if ($timeout_service->isTimeNearlyUp()) {
                FlashMessages::addMessage(I18N::translate('The server’s time limit has been reached.'), 'warning');
                break;
            }
        }

        $url = route('admin-trees-renumber', ['ged' => $tree->name()]);

        return redirect($url);
    }

    /**
     * @param Tree $tree
     *
     * @return ResponseInterface
     */
    public function setDefault(Tree $tree): ResponseInterface
    {
        Site::setPreference('DEFAULT_GEDCOM', $tree->name());

        /* I18N: %s is the name of a family tree */
        FlashMessages::addMessage(I18N::translate('The family tree “%s” will be shown to visitors when they first arrive at this website.', e($tree->title())), 'success');

        $url = route('admin-trees');

        return redirect($url);
    }

    /**
     * @param Tree $tree
     *
     * @return ResponseInterface
     */
    public function synchronize(Tree $tree): ResponseInterface
    {
        $url = route('admin-trees', ['ged' => $tree->name()]);

        $gedcom_files = $this->gedcomFiles(WT_DATA_DIR);

        foreach ($gedcom_files as $gedcom_file) {
            // Only import files that have changed
            $filemtime = (string) filemtime(WT_DATA_DIR . $gedcom_file);

            $tree = Tree::findByName($gedcom_file) ?? Tree::create($gedcom_file, $gedcom_file);

            if ($tree->getPreference('filemtime') !== $filemtime) {
                $stream = app(StreamFactoryInterface::class)->createStreamFromFile(WT_DATA_DIR . $gedcom_file);
                $tree->importGedcomFile($stream, $gedcom_file);
                $tree->setPreference('filemtime', $filemtime);

                FlashMessages::addMessage(I18N::translate('The GEDCOM file “%s” has been imported.', e($gedcom_file)), 'success');
            }
        }

        foreach (Tree::getAll() as $tree) {
            if (!in_array($tree->name(), $gedcom_files)) {
                FlashMessages::addMessage(I18N::translate('The family tree “%s” has been deleted.', e($tree->title())), 'success');
                $tree->delete();
            }
        }

        return redirect($url);
    }

    /**
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     * @param UserInterface          $user
     *
     * @return ResponseInterface
     */
    public function unconnected(ServerRequestInterface $request, Tree $tree, UserInterface $user): ResponseInterface
    {
        $associates = (bool) $request->get('associates');

        if ($associates) {
            $links = ['FAMS', 'FAMC', 'ASSO', '_ASSO'];
        } else {
            $links = ['FAMS', 'FAMC'];
        }

        $rows = DB::table('link')
            ->where('l_file', '=', $tree->id())
            ->whereIn('l_type', $links)
            ->select(['l_from', 'l_to'])
            ->get();

        $graph = [];

        foreach ($rows as $row) {
            $graph[$row->l_from][$row->l_to] = 1;
            $graph[$row->l_to][$row->l_from] = 1;
        }

        $algorithm  = new ConnectedComponent($graph);
        $components = $algorithm->findConnectedComponents();
        $root       = $tree->significantIndividual($user);
        $xref       = $root->xref();

        /** @var Individual[][] */
        $individual_groups = [];

        foreach ($components as $component) {
            if (!in_array($xref, $component)) {
                $individuals = [];
                foreach ($component as $xref) {
                    $individuals[] = Individual::getInstance($xref, $tree);
                }
                // The database query may return pending additions/deletions, which may not exist.
                $individual_groups[] = array_filter($individuals);
            }
        }

        $title = I18N::translate('Find unrelated individuals') . ' — ' . e($tree->title());

        return $this->viewResponse('admin/trees-unconnected', [
            'associates'        => $associates,
            'root'              => $root,
            'individual_groups' => $individual_groups,
            'title'             => $title,
        ]);
    }

    /**
     * Find a list of place names that would be updated.
     *
     * @param Tree   $tree
     * @param string $search
     * @param string $replace
     *
     * @return string[]
     */
    private function changePlacesPreview(Tree $tree, string $search, string $replace): array
    {
        // Fetch the latest GEDCOM for each individual and family
        $union = DB::table('families')
            ->where('f_file', '=', $tree->id())
            ->whereContains('f_gedcom', $search)
            ->select(['f_gedcom AS gedcom']);

        return DB::table('individuals')
            ->where('i_file', '=', $tree->id())
            ->whereContains('i_gedcom', $search)
            ->select(['i_gedcom AS gedcom'])
            ->unionAll($union)
            ->pluck('gedcom')
            ->mapWithKeys(static function (string $gedcom) use ($search, $replace): array {
                preg_match_all('/\n2 PLAC ((?:.*, )*)' . preg_quote($search, '/') . '(\n|$)/i', $gedcom, $matches);

                $changes = [];
                foreach ($matches[1] as $prefix) {
                    $changes[$prefix . $search] = $prefix . $replace;
                }

                return $changes;
            })
            ->sort()
            ->all();
    }

    /**
     * Find a list of place names that would be updated.
     *
     * @param Tree   $tree
     * @param string $search
     * @param string $replace
     *
     * @return string[]
     */
    private function changePlacesUpdate(Tree $tree, string $search, string $replace): array
    {
        $individual_changes = DB::table('individuals')
            ->where('i_file', '=', $tree->id())
            ->whereContains('i_gedcom', $search)
            ->select(['individuals.*'])
            ->get()
            ->map(Individual::rowMapper());

        $family_changes = DB::table('families')
            ->where('f_file', '=', $tree->id())
            ->whereContains('f_gedcom', $search)
            ->select(['families.*'])
            ->get()
            ->map(Family::rowMapper());

        return $individual_changes
            ->merge($family_changes)
            ->mapWithKeys(static function (GedcomRecord $record) use ($search, $replace): array {
                $changes = [];

                foreach ($record->facts() as $fact) {
                    $old_place = $fact->attribute('PLAC');
                    if (preg_match('/(^|, )' . preg_quote($search, '/') . '$/i', $old_place)) {
                        $new_place           = preg_replace('/(^|, )' . preg_quote($search, '/') . '$/i', '$1' . $replace, $old_place);
                        $changes[$old_place] = $new_place;
                        $gedcom              = preg_replace('/(\n2 PLAC (?:.*, )*)' . preg_quote($search, '/') . '(\n|$)/i', '$1' . $replace . '$2', $fact->gedcom());
                        $record->updateFact($fact->id(), $gedcom, false);
                    }
                }

                return $changes;
            })
            ->sort()
            ->all();
    }

    /**
     * Count of XREFs used by two trees at the same time.
     *
     * @param Tree $tree1
     * @param Tree $tree2
     *
     * @return int
     */
    private function countCommonXrefs(Tree $tree1, Tree $tree2): int
    {
        $subquery1 = DB::table('individuals')
            ->where('i_file', '=', $tree1->id())
            ->select(['i_id AS xref'])
            ->union(DB::table('families')
                ->where('f_file', '=', $tree1->id())
                ->select(['f_id AS xref']))
            ->union(DB::table('sources')
                ->where('s_file', '=', $tree1->id())
                ->select(['s_id AS xref']))
            ->union(DB::table('media')
                ->where('m_file', '=', $tree1->id())
                ->select(['m_id AS xref']))
            ->union(DB::table('other')
                ->where('o_file', '=', $tree1->id())
                ->whereNotIn('o_type', ['HEAD', 'TRLR'])
                ->select(['o_id AS xref']));

        $subquery2 = DB::table('change')
            ->where('gedcom_id', '=', $tree2->id())
            ->select(['xref AS other_xref'])
            ->union(DB::table('individuals')
                ->where('i_file', '=', $tree2->id())
                ->select(['i_id AS xref']))
            ->union(DB::table('families')
                ->where('f_file', '=', $tree2->id())
                ->select(['f_id AS xref']))
            ->union(DB::table('sources')
                ->where('s_file', '=', $tree2->id())
                ->select(['s_id AS xref']))
            ->union(DB::table('media')
                ->where('m_file', '=', $tree2->id())
                ->select(['m_id AS xref']))
            ->union(DB::table('other')
                ->where('o_file', '=', $tree2->id())
                ->whereNotIn('o_type', ['HEAD', 'TRLR'])
                ->select(['o_id AS xref']));

        return DB::table(DB::raw('(' . $subquery1->toSql() . ') AS sub1'))
            ->mergeBindings($subquery1)
            ->joinSub($subquery2, 'sub2', 'other_xref', '=', 'xref')
            ->count();
    }

    /**
     * @param Tree $tree
     *
     * @return array
     */
    private function duplicateRecords(Tree $tree): array
    {
        // We can't do any reasonable checks using MySQL.
        // Will need to wait for a "repositories" table.
        $repositories = [];

        $sources = DB::table('sources')
            ->where('s_file', '=', $tree->id())
            ->groupBy('s_name')
            ->having(DB::raw('COUNT(s_id)'), '>', 1)
            ->select([DB::raw('GROUP_CONCAT(s_id) AS xrefs')])
            ->pluck('xrefs')
            ->map(static function (string $xrefs) use ($tree): array {
                return array_map(static function (string $xref) use ($tree): Source {
                    return Source::getInstance($xref, $tree);
                }, explode(',', $xrefs));
            })
            ->all();

        $individuals = DB::table('dates')
            ->join('name', static function (JoinClause $join): void {
                $join
                    ->on('d_file', '=', 'n_file')
                    ->on('d_gid', '=', 'n_id');
            })
            ->where('d_file', '=', $tree->id())
            ->whereIn('d_fact', ['BIRT', 'CHR', 'BAPM', 'DEAT', 'BURI'])
            ->groupBy('d_year')
            ->groupBy('d_month')
            ->groupBy('d_day')
            ->groupBy('d_type')
            ->groupBy('d_fact')
            ->groupBy('n_type')
            ->groupBy('n_full')
            ->having(DB::raw('COUNT(DISTINCT d_gid)'), '>', 1)
            ->select([DB::raw('GROUP_CONCAT(d_gid) AS xrefs')])
            ->pluck('xrefs')
            ->map(static function (string $xrefs) use ($tree): array {
                return array_map(static function (string $xref) use ($tree): Individual {
                    return Individual::getInstance($xref, $tree);
                }, explode(',', $xrefs));
            })
            ->all();

        $families = DB::table('families')
            ->where('f_file', '=', $tree->id())
            ->groupBy(DB::raw('LEAST(f_husb, f_wife)'))
            ->groupBy(DB::raw('GREATEST(f_husb, f_wife)'))
            ->having(DB::raw('COUNT(f_id)'), '>', 1)
            ->select([DB::raw('GROUP_CONCAT(f_id) AS xrefs')])
            ->pluck('xrefs')
            ->map(static function (string $xrefs) use ($tree): array {
                return array_map(static function (string $xref) use ($tree): Family {
                    return Family::getInstance($xref, $tree);
                }, explode(',', $xrefs));
            })
            ->all();

        $media = DB::table('media_file')
            ->where('m_file', '=', $tree->id())
            ->where('descriptive_title', '<>', '')
            ->groupBy('descriptive_title')
            ->having(DB::raw('COUNT(m_id)'), '>', 1)
            ->select([DB::raw('GROUP_CONCAT(m_id) AS xrefs')])
            ->pluck('xrefs')
            ->map(static function (string $xrefs) use ($tree): array {
                return array_map(static function (string $xref) use ($tree): Media {
                    return Media::getInstance($xref, $tree);
                }, explode(',', $xrefs));
            })
            ->all();

        return [
            I18N::translate('Repositories')  => $repositories,
            I18N::translate('Sources')       => $sources,
            I18N::translate('Individuals')   => $individuals,
            I18N::translate('Families')      => $families,
            I18N::translate('Media objects') => $media,
        ];
    }

    /**
     * Every XREF used by this tree and also used by some other tree
     *
     * @param Tree $tree
     *
     * @return string[]
     */
    private function duplicateXrefs(Tree $tree): array
    {
        $subquery1 = DB::table('individuals')
            ->where('i_file', '=', $tree->id())
            ->select(['i_id AS xref', DB::raw("'INDI' AS type")])
            ->union(DB::table('families')
                ->where('f_file', '=', $tree->id())
                ->select(['f_id AS xref', DB::raw("'FAM' AS type")]))
            ->union(DB::table('sources')
                ->where('s_file', '=', $tree->id())
                ->select(['s_id AS xref', DB::raw("'SOUR' AS type")]))
            ->union(DB::table('media')
                ->where('m_file', '=', $tree->id())
                ->select(['m_id AS xref', DB::raw("'OBJE' AS type")]))
            ->union(DB::table('other')
                ->where('o_file', '=', $tree->id())
                ->whereNotIn('o_type', ['HEAD', 'TRLR'])
                ->select(['o_id AS xref', 'o_type AS type']));

        $subquery2 = DB::table('change')
            ->where('gedcom_id', '<>', $tree->id())
            ->select(['xref AS other_xref'])
            ->union(DB::table('individuals')
                ->where('i_file', '<>', $tree->id())
                ->select(['i_id AS xref']))
            ->union(DB::table('families')
                ->where('f_file', '<>', $tree->id())
                ->select(['f_id AS xref']))
            ->union(DB::table('sources')
                ->where('s_file', '<>', $tree->id())
                ->select(['s_id AS xref']))
            ->union(DB::table('media')
                ->where('m_file', '<>', $tree->id())
                ->select(['m_id AS xref']))
            ->union(DB::table('other')
                ->where('o_file', '<>', $tree->id())
                ->whereNotIn('o_type', ['HEAD', 'TRLR'])
                ->select(['o_id AS xref']));

        return DB::table(DB::raw('(' . $subquery1->toSql() . ') AS sub1'))
            ->mergeBindings($subquery1)
            ->joinSub($subquery2, 'sub2', 'other_xref', '=', 'xref')
            ->pluck('type', 'xref')
            ->all();
    }

    /**
     * Find a list of GEDCOM files in a folder
     *
     * @param string $folder
     *
     * @return array
     */
    private function gedcomFiles(string $folder): array
    {
        $d     = opendir($folder);
        $files = [];
        while (($f = readdir($d)) !== false) {
            if (!is_dir(WT_DATA_DIR . $f) && is_readable(WT_DATA_DIR . $f)) {
                $fp     = fopen(WT_DATA_DIR . $f, 'rb');
                $header = fread($fp, 64);
                fclose($fp);
                if (preg_match('/^(' . Gedcom::UTF8_BOM . ')?0 *HEAD/', $header)) {
                    $files[] = $f;
                }
            }
        }
        sort($files);

        return $files;
    }

    /**
     * Generate a unqiue name for new trees
     *
     * @return string
     */
    private function generateNewTreeName(): string
    {
        $tree_name      = 'tree';
        $tree_number    = 1;
        $existing_trees = Tree::getNameList();

        while (array_key_exists($tree_name . $tree_number, $existing_trees)) {
            $tree_number++;
        }

        return $tree_name . $tree_number;
    }

    /**
     * @return Collection
     * @return string[]
     */
    private function themeOptions(): Collection
    {
        return $this->module_service
            ->findByInterface(ModuleThemeInterface::class)
            ->map($this->module_service->titleMapper())
            ->prepend(I18N::translate('<default theme>'), '');
    }
}
