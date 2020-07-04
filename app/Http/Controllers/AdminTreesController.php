<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
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
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Factory;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Functions\Functions;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\Header;
use Fisharebest\Webtrees\Http\RequestHandlers\GedcomRecordPage;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Module\ModuleThemeInterface;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TimeoutService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\SurnameTradition;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use League\Flysystem\FilesystemInterface;
use Nyholm\Psr7\UploadedFile;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use stdClass;

use function app;
use function array_key_exists;
use function assert;
use function fclose;
use function is_string;
use function preg_match;
use function route;

use const UPLOAD_ERR_OK;

/**
/**
 * Controller for tree administration.
 */
class AdminTreesController extends AbstractBaseController
{
    // Show a reduced page when there are more than a certain number of trees
    private const MULTIPLE_TREE_THRESHOLD = '500';

    /** @var string */
    protected $layout = 'layouts/administration';

    /** @var ModuleService */
    private $module_service;

    /** @var TimeoutService */
    private $timeout_service;

    /** @var TreeService */
    private $tree_service;

    /** @var UserService */
    private $user_service;

    /**
     * AdminTreesController constructor.
     *
     * @param ModuleService       $module_service
     * @param TimeoutService      $timeout_service
     * @param TreeService         $tree_service
     * @param UserService         $user_service
     */
    public function __construct(
        ModuleService $module_service,
        TimeoutService $timeout_service,
        TreeService $tree_service,
        UserService $user_service
    ) {
        $this->module_service  = $module_service;
        $this->timeout_service = $timeout_service;
        $this->tree_service    = $tree_service;
        $this->user_service    = $user_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function check(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        // We need to work with raw GEDCOM data, as we are looking for errors
        // which may prevent the GedcomRecord objects from working.

        $q1 = DB::table('individuals')
            ->where('i_file', '=', $tree->id())
            ->select(['i_id AS xref', 'i_gedcom AS gedcom', new Expression("'INDI' AS type")]);
        $q2 = DB::table('families')
            ->where('f_file', '=', $tree->id())
            ->select(['f_id AS xref', 'f_gedcom AS gedcom', new Expression("'FAM' AS type")]);
        $q3 = DB::table('media')
            ->where('m_file', '=', $tree->id())
            ->select(['m_id AS xref', 'm_gedcom AS gedcom', new Expression("'OBJE' AS type")]);
        $q4 = DB::table('sources')
            ->where('s_file', '=', $tree->id())
            ->select(['s_id AS xref', 's_gedcom AS gedcom', new Expression("'SOUR' AS type")]);
        $q5 = DB::table('other')
            ->where('o_file', '=', $tree->id())
            ->whereNotIn('o_type', [Header::RECORD_TYPE, 'TRLR'])
            ->select(['o_id AS xref', 'o_gedcom AS gedcom', 'o_type']);
        $q6 = DB::table('change')
            ->where('gedcom_id', '=', $tree->id())
            ->where('status', '=', 'pending')
            ->orderBy('change_id')
            ->select(['xref', 'new_gedcom AS gedcom', new Expression("'' AS type")]);

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
            // PHP converts array keys to integers.
            $xref1 = (string) $xref1;

            $type1 = $records[$xref1]->type;
            foreach ($links as $xref2 => $type2) {
                // PHP converts array keys to integers.
                $xref2 = (string) $xref2;

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
                } elseif (!array_key_exists($type1, $RECORD_LINKS) || !in_array($type2, $RECORD_LINKS[$type1], true) || !array_key_exists($type2, $XREF_LINKS)) {
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
                    $this->checkReverseLink($type2, $all_links, $xref1, $xref2, 'FAMC', ['CHIL']) ||
                    $this->checkReverseLink($type2, $all_links, $xref1, $xref2, 'FAMS', ['HUSB', 'WIFE']) ||
                    $this->checkReverseLink($type2, $all_links, $xref1, $xref2, 'CHIL', ['FAMC']) ||
                    $this->checkReverseLink($type2, $all_links, $xref1, $xref2, 'HUSB', ['FAMS']) ||
                    $this->checkReverseLink($type2, $all_links, $xref1, $xref2, 'WIFE', ['FAMS'])
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
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function duplicates(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $duplicates = $this->duplicateRecords($tree);
        $title      = I18N::translate('Find duplicates') . ' — ' . e($tree->title());

        return $this->viewResponse('admin/trees-duplicates', [
            'duplicates' => $duplicates,
            'title'      => $title,
            'tree'       => $tree,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function importAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $data_filesystem = $request->getAttribute('filesystem.data');
        assert($data_filesystem instanceof FilesystemInterface);

        $params             = (array) $request->getParsedBody();
        $source             = $params['source'];
        $keep_media         = (bool) ($params['keep_media'] ?? false);
        $WORD_WRAPPED_NOTES = (bool) ($params['WORD_WRAPPED_NOTES'] ?? false);
        $GEDCOM_MEDIA_PATH  = $params['GEDCOM_MEDIA_PATH'];

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
            $basename = basename($params['tree_name'] ?? '');

            if ($basename) {
                $resource = $data_filesystem->readStream($basename);
                $stream   = app(StreamFactoryInterface::class)->createStreamFromResource($resource);
                $tree->importGedcomFile($stream, $basename);
            } else {
                FlashMessages::addMessage(I18N::translate('No GEDCOM file was received.'), 'danger');
            }
        }

        $url = route('manage-trees', ['tree' => $tree->name()]);

        return redirect($url);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function importForm(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $data_filesystem = $request->getAttribute('filesystem.data');
        assert($data_filesystem instanceof FilesystemInterface);

        $data_folder = $request->getAttribute('filesystem.data.name');
        assert(is_string($data_folder));

        $default_gedcom_file = $tree->getPreference('gedcom_filename');
        $gedcom_media_path   = $tree->getPreference('GEDCOM_MEDIA_PATH');
        $gedcom_files        = $this->gedcomFiles($data_filesystem);

        $title = I18N::translate('Import a GEDCOM file') . ' — ' . e($tree->title());

        return $this->viewResponse('admin/trees-import', [
            'data_folder'         => $data_folder,
            'default_gedcom_file' => $default_gedcom_file,
            'gedcom_files'        => $gedcom_files,
            'gedcom_media_path'   => $gedcom_media_path,
            'title'               => $title,
            'tree'                => $tree,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');

        $data_filesystem = $request->getAttribute('filesystem.data');
        assert($data_filesystem instanceof FilesystemInterface);

        $multiple_tree_threshold = (int) Site::getPreference('MULTIPLE_TREE_THRESHOLD', self::MULTIPLE_TREE_THRESHOLD);
        $gedcom_files            = $this->gedcomFiles($data_filesystem);

        $all_trees = $this->tree_service->all();

        // On sites with hundreds or thousands of trees, this page becomes very large.
        // Just show the current tree, the default tree, and un-imported trees
        if ($all_trees->count() >= $multiple_tree_threshold) {
            $default   = Site::getPreference('DEFAULT_GEDCOM');
            $all_trees = $all_trees->filter(static function (Tree $x) use ($tree, $default): bool {
                if ($x->getPreference('imported') === '0') {
                    return true;
                }
                if ($tree instanceof Tree && $tree->id() === $x->id()) {
                    return true;
                }

                return $x->name() === $default;
            });
        }

        $title = I18N::translate('Manage family trees');

        $base_url = app(ServerRequestInterface::class)->getAttribute('base_url');

        return $this->viewResponse('admin/trees', [
            'all_trees'               => $all_trees,
            'base_url'                => $base_url,
            'gedcom_files'            => $gedcom_files,
            'multiple_tree_threshold' => $multiple_tree_threshold,
            'title'                   => $title,
            'tree'                    => $tree,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function merge(ServerRequestInterface $request): ResponseInterface
    {
        $params     = $request->getQueryParams();
        $tree1_name = $params['tree1_name'] ?? '';
        $tree2_name = $params['tree2_name'] ?? '';

        $tree1 = $this->tree_service->all()->get($tree1_name);
        $tree2 = $this->tree_service->all()->get($tree2_name);

        if ($tree1 !== null && $tree2 !== null && $tree1->id() !== $tree2->id()) {
            $xrefs = $this->countCommonXrefs($tree1, $tree2);
        } else {
            $xrefs = 0;
        }

        $title = I18N::translate(I18N::translate('Merge family trees'));

        return $this->viewResponse('admin/trees-merge', [
            'tree_list' => $this->tree_service->titles(),
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
        $params     = (array) $request->getParsedBody();
        $tree1_name = $params['tree1_name'] ?? '';
        $tree2_name = $params['tree2_name'] ?? '';

        $tree1 = $this->tree_service->all()->get($tree1_name);
        $tree2 = $this->tree_service->all()->get($tree2_name);

        if ($tree1 instanceof Tree && $tree2 instanceof Tree && $tree1 !== $tree2 && $this->countCommonXrefs($tree1, $tree2) === 0) {
            (new Builder(DB::connection()))->from('individuals')->insertUsing([
                'i_file',
                'i_id',
                'i_rin',
                'i_sex',
                'i_gedcom',
            ], static function (Builder $query) use ($tree1, $tree2): void {
                $query->select([
                    new Expression($tree2->id()),
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
                    new Expression($tree2->id()),
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
                    new Expression($tree2->id()),
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
                    new Expression($tree2->id()),
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
                    new Expression($tree2->id()),
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
                    new Expression($tree2->id()),
                    'o_id',
                    'o_type',
                    'o_gedcom',
                ])->from('other')
                    ->whereNotIn('o_type', [Header::RECORD_TYPE, 'TRLR'])
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
                    new Expression($tree2->id()),
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
                    new Expression($tree2->id()),
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
                    new Expression($tree2->id()),
                    'l_from',
                    'l_type',
                    'l_to',
                ])->from('link')
                    ->whereNotIn('l_from', [Header::RECORD_TYPE, 'TRLR'])
                    ->where('l_file', '=', $tree1->id());
            });

            FlashMessages::addMessage(I18N::translate('The family trees have been merged successfully.'), 'success');

            $url = route('manage-trees', ['tree' => $tree2->name()]);
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
     *
     * @return ResponseInterface
     */
    public function preferences(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $data_folder = $request->getAttribute('filesystem.data.name');
        assert(is_string($data_folder));

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

        $pedigree_individual = Factory::individual()->make($tree->getPreference('PEDIGREE_ROOT_ID'), $tree);

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

        $tree_count = $this->tree_service->all()->count();

        $title = I18N::translate('Preferences') . ' — ' . e($tree->title());

        $base_url = app(ServerRequestInterface::class)->getAttribute('base_url');

        return $this->viewResponse('admin/trees-preferences', [
            'all_fam_facts'            => $all_fam_facts,
            'all_indi_facts'           => $all_indi_facts,
            'all_name_facts'           => $all_name_facts,
            'all_plac_facts'           => $all_plac_facts,
            'all_repo_facts'           => $all_repo_facts,
            'all_sour_facts'           => $all_sour_facts,
            'all_surname_traditions'   => $all_surname_traditions,
            'base_url'                 => $base_url,
            'calendar_formats'         => $calendar_formats,
            'data_folder'              => $data_folder,
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
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function renumber(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $xrefs = $this->duplicateXrefs($tree);

        /* I18N: Renumber the records in a family tree */
        $title = I18N::translate('Renumber family tree') . ' — ' . e($tree->title());

        return $this->viewResponse('admin/trees-renumber', [
            'title' => $title,
            'tree'  => $tree,
            'xrefs' => $xrefs,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function preferencesUpdate(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $params = (array) $request->getParsedBody();

        $tree->setPreference('ADVANCED_NAME_FACTS', implode(',', $params['ADVANCED_NAME_FACTS'] ?? []));
        $tree->setPreference('ADVANCED_PLAC_FACTS', implode(',', $params['ADVANCED_PLAC_FACTS'] ?? []));
        // For backwards compatibility with webtrees 1.x we store the two calendar formats in one variable
        // e.g. "gregorian_and_jewish"
        $tree->setPreference('CALENDAR_FORMAT', implode('_and_', array_unique([
            $params['CALENDAR_FORMAT0'] ?? 'none',
            $params['CALENDAR_FORMAT1'] ?? 'none',
        ])));
        $tree->setPreference('CHART_BOX_TAGS', implode(',', $params['CHART_BOX_TAGS'] ?? []));
        $tree->setPreference('CONTACT_USER_ID', $params['CONTACT_USER_ID'] ?? '');
        $tree->setPreference('EXPAND_NOTES', $params['EXPAND_NOTES'] ?? '');
        $tree->setPreference('EXPAND_SOURCES', $params['EXPAND_SOURCES'] ?? '');
        $tree->setPreference('FAM_FACTS_ADD', implode(',', $params['FAM_FACTS_ADD'] ?? []));
        $tree->setPreference('FAM_FACTS_QUICK', implode(',', $params['FAM_FACTS_QUICK'] ?? []));
        $tree->setPreference('FAM_FACTS_UNIQUE', implode(',', $params['FAM_FACTS_UNIQUE'] ?? []));
        $tree->setPreference('FULL_SOURCES', $params['FULL_SOURCES'] ?? '');
        $tree->setPreference('FORMAT_TEXT', $params['FORMAT_TEXT'] ?? '');
        $tree->setPreference('GENERATE_UIDS', $params['GENERATE_UIDS'] ?? '');
        $tree->setPreference('HIDE_GEDCOM_ERRORS', $params['HIDE_GEDCOM_ERRORS'] ?? '');
        $tree->setPreference('INDI_FACTS_ADD', implode(',', $params['INDI_FACTS_ADD'] ?? []));
        $tree->setPreference('INDI_FACTS_QUICK', implode(',', $params['INDI_FACTS_QUICK'] ?? []));
        $tree->setPreference('INDI_FACTS_UNIQUE', implode(',', $params['INDI_FACTS_UNIQUE'] ?? []));
        $tree->setPreference('MEDIA_UPLOAD', $params['MEDIA_UPLOAD'] ?? '');
        $tree->setPreference('META_DESCRIPTION', $params['META_DESCRIPTION'] ?? '');
        $tree->setPreference('META_TITLE', $params['META_TITLE'] ?? '');
        $tree->setPreference('NO_UPDATE_CHAN', $params['NO_UPDATE_CHAN'] ?? '');
        $tree->setPreference('PEDIGREE_ROOT_ID', $params['PEDIGREE_ROOT_ID'] ?? '');
        $tree->setPreference('PREFER_LEVEL2_SOURCES', $params['PREFER_LEVEL2_SOURCES'] ?? '');
        $tree->setPreference('QUICK_REQUIRED_FACTS', implode(',', $params['QUICK_REQUIRED_FACTS'] ?? []));
        $tree->setPreference('QUICK_REQUIRED_FAMFACTS', implode(',', $params['QUICK_REQUIRED_FAMFACTS'] ?? []));
        $tree->setPreference('REPO_FACTS_ADD', implode(',', $params['REPO_FACTS_ADD'] ?? []));
        $tree->setPreference('REPO_FACTS_QUICK', implode(',', $params['REPO_FACTS_QUICK'] ?? []));
        $tree->setPreference('REPO_FACTS_UNIQUE', implode(',', $params['REPO_FACTS_UNIQUE'] ?? []));
        $tree->setPreference('SHOW_COUNTER', $params['SHOW_COUNTER'] ?? '');
        $tree->setPreference('SHOW_EST_LIST_DATES', $params['SHOW_EST_LIST_DATES'] ?? '');
        $tree->setPreference('SHOW_FACT_ICONS', $params['SHOW_FACT_ICONS'] ?? '');
        $tree->setPreference('SHOW_GEDCOM_RECORD', $params['SHOW_GEDCOM_RECORD'] ?? '');
        $tree->setPreference('SHOW_HIGHLIGHT_IMAGES', $params['SHOW_HIGHLIGHT_IMAGES'] ?? '');
        $tree->setPreference('SHOW_LAST_CHANGE', $params['SHOW_LAST_CHANGE'] ?? '');
        $tree->setPreference('SHOW_MEDIA_DOWNLOAD', $params['SHOW_MEDIA_DOWNLOAD'] ?? '');
        $tree->setPreference('SHOW_NO_WATERMARK', $params['SHOW_NO_WATERMARK'] ?? '');
        $tree->setPreference('SHOW_PARENTS_AGE', $params['SHOW_PARENTS_AGE'] ?? '');
        $tree->setPreference('SHOW_PEDIGREE_PLACES', $params['SHOW_PEDIGREE_PLACES'] ?? '');
        $tree->setPreference('SHOW_PEDIGREE_PLACES_SUFFIX', $params['SHOW_PEDIGREE_PLACES_SUFFIX'] ?? '');
        $tree->setPreference('SHOW_RELATIVES_EVENTS', implode(',', $params['SHOW_RELATIVES_EVENTS'] ?? []));
        $tree->setPreference('SOUR_FACTS_ADD', implode(',', $params['SOUR_FACTS_ADD'] ?? []));
        $tree->setPreference('SOUR_FACTS_QUICK', implode(',', $params['SOUR_FACTS_QUICK'] ?? []));
        $tree->setPreference('SOUR_FACTS_UNIQUE', implode(',', $params['SOUR_FACTS_UNIQUE'] ?? []));
        $tree->setPreference('SUBLIST_TRIGGER_I', $params['SUBLIST_TRIGGER_I'] ?? '200');
        $tree->setPreference('SURNAME_LIST_STYLE', $params['SURNAME_LIST_STYLE'] ?? '');
        $tree->setPreference('SURNAME_TRADITION', $params['SURNAME_TRADITION'] ?? '');
        $tree->setPreference('USE_SILHOUETTE', $params['USE_SILHOUETTE'] ?? '');
        $tree->setPreference('WEBMASTER_USER_ID', $params['WEBMASTER_USER_ID'] ?? '');
        $tree->setPreference('title', $params['title'] ?? '');

        // Only accept valid folders for MEDIA_DIRECTORY
        $MEDIA_DIRECTORY = $params['MEDIA_DIRECTORY'] ?? '';
        $MEDIA_DIRECTORY = preg_replace('/[:\/\\\\]+/', '/', $MEDIA_DIRECTORY);
        $MEDIA_DIRECTORY = trim($MEDIA_DIRECTORY, '/') . '/';

        $tree->setPreference('MEDIA_DIRECTORY', $MEDIA_DIRECTORY);

        $gedcom = $params['gedcom'] ?? '';

        if ($gedcom !== '' && $gedcom !== $tree->name()) {
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

        // Coming soon...
        $all_trees = $params['all_trees'] ?? '';
        $new_trees = $params['new_trees'] ?? '';

        if ($all_trees === 'on') {
            FlashMessages::addMessage(I18N::translate('The preferences for all family trees have been updated.'), 'success');
        }

        if ($new_trees === 'on') {
            FlashMessages::addMessage(I18N::translate('The preferences for new family trees have been updated.'), 'success');
        }

        $url = route('manage-trees', ['tree' => $tree->name()]);

        return redirect($url);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function renumberAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $xrefs = $this->duplicateXrefs($tree);

        foreach ($xrefs as $old_xref => $type) {
            $new_xref = Factory::xref()->make($type);
            switch ($type) {
                case Individual::RECORD_TYPE:
                    DB::table('individuals')
                        ->where('i_file', '=', $tree->id())
                        ->where('i_id', '=', $old_xref)
                        ->update([
                            'i_id'     => $new_xref,
                            'i_gedcom' => new Expression("REPLACE(i_gedcom, '0 @$old_xref@ INDI', '0 @$new_xref@ INDI')"),
                        ]);

                    DB::table('families')
                        ->where('f_husb', '=', $old_xref)
                        ->where('f_file', '=', $tree->id())
                        ->update([
                            'f_husb'   => $new_xref,
                            'f_gedcom' => new Expression("REPLACE(f_gedcom, ' HUSB @$old_xref@', ' HUSB @$new_xref@')"),
                        ]);

                    DB::table('families')
                        ->where('f_wife', '=', $old_xref)
                        ->where('f_file', '=', $tree->id())
                        ->update([
                            'f_wife'   => $new_xref,
                            'f_gedcom' => new Expression("REPLACE(f_gedcom, ' WIFE @$old_xref@', ' WIFE @$new_xref@')"),
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
                                'f_gedcom' => new Expression("REPLACE(f_gedcom, ' $tag @$old_xref@', ' $tag @$new_xref@')"),
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
                                'i_gedcom' => new Expression("REPLACE(i_gedcom, ' $tag @$old_xref@', ' $tag @$new_xref@')"),
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
                        ->whereIn('setting_name', [User::PREF_TREE_ACCOUNT_XREF, User::PREF_TREE_DEFAULT_XREF])
                        ->update([
                            'setting_value' => $new_xref,
                        ]);
                    break;

                case Family::RECORD_TYPE:
                    DB::table('families')
                        ->where('f_file', '=', $tree->id())
                        ->where('f_id', '=', $old_xref)
                        ->update([
                            'f_id'     => $new_xref,
                            'f_gedcom' => new Expression("REPLACE(f_gedcom, '0 @$old_xref@ FAM', '0 @$new_xref@ FAM')"),
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
                                'i_gedcom' => new Expression("REPLACE(i_gedcom, ' $tag @$old_xref@', ' $tag @$new_xref@')"),
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

                case Source::RECORD_TYPE:
                    DB::table('sources')
                        ->where('s_file', '=', $tree->id())
                        ->where('s_id', '=', $old_xref)
                        ->update([
                            's_id'     => $new_xref,
                            's_gedcom' => new Expression("REPLACE(s_gedcom, '0 @$old_xref@ SOUR', '0 @$new_xref@ SOUR')"),
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
                            'i_gedcom' => new Expression("REPLACE(i_gedcom, ' SOUR @$old_xref@', ' SOUR @$new_xref@')"),
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
                            'f_gedcom' => new Expression("REPLACE(f_gedcom, ' SOUR @$old_xref@', ' SOUR @$new_xref@')"),
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
                            'm_gedcom' => new Expression("REPLACE(m_gedcom, ' SOUR @$old_xref@', ' SOUR @$new_xref@')"),
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
                            'o_gedcom' => new Expression("REPLACE(o_gedcom, ' SOUR @$old_xref@', ' SOUR @$new_xref@')"),
                        ]);
                    break;

                case Repository::RECORD_TYPE:
                    DB::table('other')
                        ->where('o_file', '=', $tree->id())
                        ->where('o_id', '=', $old_xref)
                        ->where('o_type', '=', 'REPO')
                        ->update([
                            'o_id'     => $new_xref,
                            'o_gedcom' => new Expression("REPLACE(o_gedcom, '0 @$old_xref@ REPO', '0 @$new_xref@ REPO')"),
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
                            's_gedcom' => new Expression("REPLACE(s_gedcom, ' REPO @$old_xref@', ' REPO @$new_xref@')"),
                        ]);
                    break;

                case Note::RECORD_TYPE:
                    DB::table('other')
                        ->where('o_file', '=', $tree->id())
                        ->where('o_id', '=', $old_xref)
                        ->where('o_type', '=', 'NOTE')
                        ->update([
                            'o_id'     => $new_xref,
                            'o_gedcom' => new Expression("REPLACE(o_gedcom, '0 @$old_xref@ NOTE', '0 @$new_xref@ NOTE')"),
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
                            'i_gedcom' => new Expression("REPLACE(i_gedcom, ' NOTE @$old_xref@', ' NOTE @$new_xref@')"),
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
                            'f_gedcom' => new Expression("REPLACE(f_gedcom, ' NOTE @$old_xref@', ' NOTE @$new_xref@')"),
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
                            'm_gedcom' => new Expression("REPLACE(m_gedcom, ' NOTE @$old_xref@', ' NOTE @$new_xref@')"),
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
                            's_gedcom' => new Expression("REPLACE(s_gedcom, ' NOTE @$old_xref@', ' NOTE @$new_xref@')"),
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
                            'o_gedcom' => new Expression("REPLACE(o_gedcom, ' NOTE @$old_xref@', ' NOTE @$new_xref@')"),
                        ]);
                    break;

                case Media::RECORD_TYPE:
                    DB::table('media')
                        ->where('m_file', '=', $tree->id())
                        ->where('m_id', '=', $old_xref)
                        ->update([
                            'm_id'     => $new_xref,
                            'm_gedcom' => new Expression("REPLACE(m_gedcom, '0 @$old_xref@ OBJE', '0 @$new_xref@ OBJE')"),
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
                            'i_gedcom' => new Expression("REPLACE(i_gedcom, ' OBJE @$old_xref@', ' OBJE @$new_xref@')"),
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
                            'f_gedcom' => new Expression("REPLACE(f_gedcom, ' OBJE @$old_xref@', ' OBJE @$new_xref@')"),
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
                            's_gedcom' => new Expression("REPLACE(s_gedcom, ' OBJE @$old_xref@', ' OBJE @$new_xref@')"),
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
                            'o_gedcom' => new Expression("REPLACE(o_gedcom, ' OBJE @$old_xref@', ' OBJE @$new_xref@')"),
                        ]);
                    break;

                default:
                    DB::table('other')
                        ->where('o_file', '=', $tree->id())
                        ->where('o_id', '=', $old_xref)
                        ->where('o_type', '=', $type)
                        ->update([
                            'o_id'     => $new_xref,
                            'o_gedcom' => new Expression("REPLACE(o_gedcom, '0 @$old_xref@ $type', '0 @$new_xref@ $type')"),
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
                            'i_gedcom' => new Expression("REPLACE(i_gedcom, ' $type @$old_xref@', ' $type @$new_xref@')"),
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
                            'f_gedcom' => new Expression("REPLACE(f_gedcom, ' $type @$old_xref@', ' $type @$new_xref@')"),
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
                            'm_gedcom' => new Expression("REPLACE(m_gedcom, ' $type @$old_xref@', ' $type @$new_xref@')"),
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
                            's_gedcom' => new Expression("REPLACE(s_gedcom, ' $type @$old_xref@', ' $type @$new_xref@')"),
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
                            'o_gedcom' => new Expression("REPLACE(o_gedcom, ' $type @$old_xref@', ' $type @$new_xref@')"),
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
            if ($this->timeout_service->isTimeNearlyUp()) {
                FlashMessages::addMessage(I18N::translate('The server’s time limit has been reached.'), 'warning');
                break;
            }
        }

        $url = route('admin-trees-renumber', ['tree' => $tree->name()]);

        return redirect($url);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function synchronize(ServerRequestInterface $request): ResponseInterface
    {
        $data_filesystem = $request->getAttribute('filesystem.data');
        assert($data_filesystem instanceof FilesystemInterface);

        $gedcom_files = $this->gedcomFiles($data_filesystem);

        foreach ($gedcom_files as $gedcom_file) {
            // Only import files that have changed
            $filemtime = (string) $data_filesystem->getTimestamp($gedcom_file);

            $tree = $this->tree_service->all()->get($gedcom_file) ?? $this->tree_service->create($gedcom_file, $gedcom_file);

            if ($tree->getPreference('filemtime') !== $filemtime) {
                $resource = $data_filesystem->readStream($gedcom_file);
                $stream   = app(StreamFactoryInterface::class)->createStreamFromResource($resource);
                $tree->importGedcomFile($stream, $gedcom_file);
                $stream->close();
                $tree->setPreference('filemtime', $filemtime);

                FlashMessages::addMessage(I18N::translate('The GEDCOM file “%s” has been imported.', e($gedcom_file)), 'success');
            }
        }

        foreach ($this->tree_service->all() as $tree) {
            if (!in_array($tree->name(), $gedcom_files, true)) {
                $this->tree_service->delete($tree);
                FlashMessages::addMessage(I18N::translate('The family tree “%s” has been deleted.', e($tree->title())), 'success');
            }
        }

        return redirect(route('manage-trees', ['tree' => $this->tree_service->all()->first()->name()]));
    }

    /**
     * @param string     $type
     * @param string[][] $links
     * @param string     $xref1
     * @param string     $xref2
     * @param string     $link
     * @param string[]   $reciprocal
     *
     * @return bool
     */
    private function checkReverseLink(string $type, array $links, string $xref1, string $xref2, string $link, array $reciprocal): bool
    {
        return $type === $link && (!array_key_exists($xref1, $links[$xref2]) || !in_array($links[$xref2][$xref1], $reciprocal, true));
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
        return '<b><a href="' . e(route(GedcomRecordPage::class, [
                'xref' => $xref,
                'tree' => $tree->name(),
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
                ->whereNotIn('o_type', [Header::RECORD_TYPE, 'TRLR'])
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
                ->whereNotIn('o_type', [Header::RECORD_TYPE, 'TRLR'])
                ->select(['o_id AS xref']));

        return DB::table(new Expression('(' . $subquery1->toSql() . ') AS sub1'))
            ->mergeBindings($subquery1)
            ->joinSub($subquery2, 'sub2', 'other_xref', '=', 'xref')
            ->count();
    }

    /**
     * @param Tree $tree
     *
     * @return array<string,array<GedcomRecord>>
     */
    private function duplicateRecords(Tree $tree): array
    {
        // We can't do any reasonable checks using MySQL.
        // Will need to wait for a "repositories" table.
        $repositories = [];

        $sources = DB::table('sources')
            ->where('s_file', '=', $tree->id())
            ->groupBy(['s_name'])
            ->having(new Expression('COUNT(s_id)'), '>', '1')
            ->select([new Expression('GROUP_CONCAT(s_id) AS xrefs')])
            ->pluck('xrefs')
            ->map(static function (string $xrefs) use ($tree): array {
                return array_map(static function (string $xref) use ($tree): Source {
                    return Factory::source()->make($xref, $tree);
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
            ->groupBy(['d_year', 'd_month', 'd_day', 'd_type', 'd_fact', 'n_type', 'n_full'])
            ->having(new Expression('COUNT(DISTINCT d_gid)'), '>', '1')
            ->select([new Expression('GROUP_CONCAT(DISTINCT d_gid ORDER BY d_gid) AS xrefs')])
            ->distinct()
            ->pluck('xrefs')
            ->map(static function (string $xrefs) use ($tree): array {
                return array_map(static function (string $xref) use ($tree): Individual {
                    return Factory::individual()->make($xref, $tree);
                }, explode(',', $xrefs));
            })
            ->all();

        $families = DB::table('families')
            ->where('f_file', '=', $tree->id())
            ->groupBy([new Expression('LEAST(f_husb, f_wife)')])
            ->groupBy([new Expression('GREATEST(f_husb, f_wife)')])
            ->having(new Expression('COUNT(f_id)'), '>', '1')
            ->select([new Expression('GROUP_CONCAT(f_id) AS xrefs')])
            ->pluck('xrefs')
            ->map(static function (string $xrefs) use ($tree): array {
                return array_map(static function (string $xref) use ($tree): Family {
                    return Factory::family()->make($xref, $tree);
                }, explode(',', $xrefs));
            })
            ->all();

        $media = DB::table('media_file')
            ->where('m_file', '=', $tree->id())
            ->where('descriptive_title', '<>', '')
            ->groupBy(['descriptive_title'])
            ->having(new Expression('COUNT(m_id)'), '>', '1')
            ->select([new Expression('GROUP_CONCAT(m_id) AS xrefs')])
            ->pluck('xrefs')
            ->map(static function (string $xrefs) use ($tree): array {
                return array_map(static function (string $xref) use ($tree): Media {
                    return Factory::media()->make($xref, $tree);
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
            ->select(['i_id AS xref', new Expression("'INDI' AS type")])
            ->union(DB::table('families')
                ->where('f_file', '=', $tree->id())
                ->select(['f_id AS xref', new Expression("'FAM' AS type")]))
            ->union(DB::table('sources')
                ->where('s_file', '=', $tree->id())
                ->select(['s_id AS xref', new Expression("'SOUR' AS type")]))
            ->union(DB::table('media')
                ->where('m_file', '=', $tree->id())
                ->select(['m_id AS xref', new Expression("'OBJE' AS type")]))
            ->union(DB::table('other')
                ->where('o_file', '=', $tree->id())
                ->whereNotIn('o_type', [Header::RECORD_TYPE, 'TRLR'])
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
                ->whereNotIn('o_type', [Header::RECORD_TYPE, 'TRLR'])
                ->select(['o_id AS xref']));

        return DB::table(new Expression('(' . $subquery1->toSql() . ') AS sub1'))
            ->mergeBindings($subquery1)
            ->joinSub($subquery2, 'sub2', 'other_xref', '=', 'xref')
            ->pluck('type', 'xref')
            ->all();
    }

    /**
     * A list of GEDCOM files in the data folder.
     *
     * @param FilesystemInterface $data_filesystem
     *
     * @return array<string>
     */
    private function gedcomFiles(FilesystemInterface $data_filesystem): array
    {
        return Collection::make($data_filesystem->listContents())
            ->filter(static function (array $path) use ($data_filesystem): bool {
                if ($path['type'] !== 'file') {
                    return false;
                }

                $stream = $data_filesystem->readStream($path['path']);
                $header = fread($stream, 64);
                fclose($stream);

                return preg_match('/^(' . Gedcom::UTF8_BOM . ')?0 *HEAD/', $header) > 0;
            })
            ->map(static function (array $path): string {
                return $path['path'];
            })
            ->sort()
            ->all();
    }

    /**
     * @return Collection<string>
     */
    private function themeOptions(): Collection
    {
        return $this->module_service
            ->findByInterface(ModuleThemeInterface::class)
            ->map($this->module_service->titleMapper())
            ->prepend(I18N::translate('<default theme>'), '');
    }
}
