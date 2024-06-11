<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Encodings\ANSEL;
use Fisharebest\Webtrees\Encodings\ASCII;
use Fisharebest\Webtrees\Encodings\UTF16BE;
use Fisharebest\Webtrees\Encodings\UTF8;
use Fisharebest\Webtrees\Encodings\Windows1252;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Http\RequestHandlers\FamilyPage;
use Fisharebest\Webtrees\Http\RequestHandlers\IndividualPage;
use Fisharebest\Webtrees\Http\RequestHandlers\LocationPage;
use Fisharebest\Webtrees\Http\RequestHandlers\MediaPage;
use Fisharebest\Webtrees\Http\RequestHandlers\NotePage;
use Fisharebest\Webtrees\Http\RequestHandlers\RepositoryPage;
use Fisharebest\Webtrees\Http\RequestHandlers\SourcePage;
use Fisharebest\Webtrees\Http\RequestHandlers\SubmitterPage;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Location;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Services\GedcomExportService;
use Fisharebest\Webtrees\Services\LinkedRecordService;
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Submitter;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function array_filter;
use function array_keys;
use function array_map;
use function array_search;
use function assert;
use function count;
use function date;
use function extension_loaded;
use function in_array;
use function is_array;
use function is_string;
use function preg_match_all;
use function redirect;
use function route;
use function str_replace;
use function uasort;
use function view;

use const PREG_SET_ORDER;

/**
 * Class ClippingsCartModule
 */
class ClippingsCartModule extends AbstractModule implements ModuleMenuInterface
{
    use ModuleMenuTrait;

    // What to add to the cart?
    private const ADD_RECORD_ONLY        = 'record';
    private const ADD_CHILDREN           = 'children';
    private const ADD_DESCENDANTS        = 'descendants';
    private const ADD_PARENT_FAMILIES    = 'parents';
    private const ADD_SPOUSE_FAMILIES    = 'spouses';
    private const ADD_ANCESTORS          = 'ancestors';
    private const ADD_ANCESTOR_FAMILIES  = 'families';
    private const ADD_LINKED_INDIVIDUALS = 'linked';

    // Routes that have a record which can be added to the clipboard
    private const ROUTES_WITH_RECORDS = [
        'Family'     => FamilyPage::class,
        'Individual' => IndividualPage::class,
        'Media'      => MediaPage::class,
        'Location'   => LocationPage::class,
        'Note'       => NotePage::class,
        'Repository' => RepositoryPage::class,
        'Source'     => SourcePage::class,
        'Submitter'  => SubmitterPage::class,
    ];

    /** @var int The default access level for this module.  It can be changed in the control panel. */
    protected int $access_level = Auth::PRIV_USER;

    private GedcomExportService $gedcom_export_service;

    private LinkedRecordService $linked_record_service;

    /**
     * @param GedcomExportService $gedcom_export_service
     * @param LinkedRecordService $linked_record_service
     */
    public function __construct(
        GedcomExportService $gedcom_export_service,
        LinkedRecordService $linked_record_service
    ) {
        $this->gedcom_export_service = $gedcom_export_service;
        $this->linked_record_service = $linked_record_service;
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “Clippings cart” module */
        return I18N::translate('Select records from your family tree and save them as a GEDCOM file.');
    }

    /**
     * The default position for this menu.  It can be changed in the control panel.
     *
     * @return int
     */
    public function defaultMenuOrder(): int
    {
        return 6;
    }

    /**
     * A menu, to be added to the main application menu.
     */
    public function getMenu(Tree $tree): Menu|null
    {
        $request = Registry::container()->get(ServerRequestInterface::class);
        $route   = Validator::attributes($request)->route();
        $cart    = Session::get('cart');
        $cart    = is_array($cart) ? $cart : [];
        $count   = count($cart[$tree->name()] ?? []);
        $badge   = view('components/badge', ['count' => $count]);

        $submenus = [
            new Menu($this->title() . ' ' . $badge, route('module', [
                'module' => $this->name(),
                'action' => 'Show',
                'tree'   => $tree->name(),
            ]), 'menu-clippings-cart', ['rel' => 'nofollow']),
        ];

        $action = array_search($route->name, self::ROUTES_WITH_RECORDS, true);
        if ($action !== false) {
            $xref = $route->attributes['xref'];
            assert(is_string($xref));

            $add_route = route('module', [
                'module' => $this->name(),
                'action' => 'Add' . $action,
                'xref'   => $xref,
                'tree'   => $tree->name(),
            ]);

            $submenus[] = new Menu(I18N::translate('Add to the clippings cart'), $add_route, 'menu-clippings-add', ['rel' => 'nofollow']);
        }

        if (!$this->isCartEmpty($tree)) {
            $submenus[] = new Menu(I18N::translate('Empty the clippings cart'), route('module', [
                'module' => $this->name(),
                'action' => 'Empty',
                'tree'   => $tree->name(),
            ]), 'menu-clippings-empty', ['rel' => 'nofollow']);

            $submenus[] = new Menu(I18N::translate('Download'), route('module', [
                'module' => $this->name(),
                'action' => 'DownloadForm',
                'tree'   => $tree->name(),
            ]), 'menu-clippings-download', ['rel' => 'nofollow']);
        }

        return new Menu($this->title(), '#', 'menu-clippings', ['rel' => 'nofollow'], $submenus);
    }

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Clippings cart');
    }

    /**
     * @param Tree $tree
     *
     * @return bool
     */
    private function isCartEmpty(Tree $tree): bool
    {
        $cart     = Session::get('cart');
        $cart     = is_array($cart) ? $cart : [];
        $contents = $cart[$tree->name()] ?? [];

        return $contents === [];
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getDownloadFormAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree = Validator::attributes($request)->tree();

        $title = I18N::translate('Family tree clippings cart') . ' — ' . I18N::translate('Download');

        $download_filenames = [
            'clippings'                  => 'clippings',
            'clippings-' . date('Y-m-d') => 'clippings-' . date('Y-m-d'),
        ];

        return $this->viewResponse('modules/clippings/download', [
            'download_filenames' => $download_filenames,
            'module'             => $this->name(),
            'title'              => $title,
            'tree'               => $tree,
            'zip_available'      => extension_loaded('zip'),
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function postDownloadAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree = Validator::attributes($request)->tree();

        if (Auth::isAdmin()) {
            $privacy_options = ['none', 'gedadmin', 'user', 'visitor'];
        } elseif (Auth::isManager($tree)) {
            $privacy_options = ['gedadmin', 'user', 'visitor'];
        } elseif (Auth::isMember($tree)) {
            $privacy_options = ['user', 'visitor'];
        } else {
            $privacy_options = ['visitor'];
        }

        $filename     = Validator::parsedBody($request)->string('filename');
        $format       = Validator::parsedBody($request)->isInArray(['gedcom', 'zip', 'zipmedia', 'gedzip'])->string('format');
        $privacy      = Validator::parsedBody($request)->isInArray($privacy_options)->string('privacy');
        $encoding     = Validator::parsedBody($request)->isInArray([UTF8::NAME, UTF16BE::NAME, ANSEL::NAME, ASCII::NAME, Windows1252::NAME])->string('encoding');
        $line_endings = Validator::parsedBody($request)->isInArray(['CRLF', 'LF'])->string('line_endings');

        $cart = Session::get('cart');
        $cart = is_array($cart) ? $cart : [];

        $xrefs = array_keys($cart[$tree->name()] ?? []);
        $xrefs = array_map('strval', $xrefs); // PHP converts numeric keys to integers.

        $records = new Collection();

        switch ($privacy) {
            case 'gedadmin':
                $access_level = Auth::PRIV_NONE;
                break;
            case 'user':
                $access_level = Auth::PRIV_USER;
                break;
            case 'visitor':
                $access_level = Auth::PRIV_PRIVATE;
                break;
            case 'none':
            default:
                $access_level = Auth::PRIV_HIDE;
                break;
        }

        foreach ($xrefs as $xref) {
            $object = Registry::gedcomRecordFactory()->make($xref, $tree);
            // The object may have been deleted since we added it to the cart....
            if ($object instanceof GedcomRecord) {
                $record = $object->privatizeGedcom($access_level);
                // Remove links to objects that aren't in the cart
                preg_match_all('/\n1 ' . Gedcom::REGEX_TAG . ' @(' . Gedcom::REGEX_XREF . ')@(\n[2-9].*)*/', $record, $matches, PREG_SET_ORDER);
                foreach ($matches as $match) {
                    if (!in_array($match[1], $xrefs, true)) {
                        $record = str_replace($match[0], '', $record);
                    }
                }
                preg_match_all('/\n2 ' . Gedcom::REGEX_TAG . ' @(' . Gedcom::REGEX_XREF . ')@(\n[3-9].*)*/', $record, $matches, PREG_SET_ORDER);
                foreach ($matches as $match) {
                    if (!in_array($match[1], $xrefs, true)) {
                        $record = str_replace($match[0], '', $record);
                    }
                }
                preg_match_all('/\n3 ' . Gedcom::REGEX_TAG . ' @(' . Gedcom::REGEX_XREF . ')@(\n[4-9].*)*/', $record, $matches, PREG_SET_ORDER);
                foreach ($matches as $match) {
                    if (!in_array($match[1], $xrefs, true)) {
                        $record = str_replace($match[0], '', $record);
                    }
                }

                $records->add($record);
            }
        }

        // We have already applied privacy filtering, so do not do it again.
        return $this->gedcom_export_service->downloadResponse($tree, false, $encoding, 'none', $line_endings, $filename, $format, $records);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getEmptyAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree = Validator::attributes($request)->tree();

        $cart = Session::get('cart');
        $cart = is_array($cart) ? $cart : [];

        $cart[$tree->name()] = [];
        Session::put('cart', $cart);

        $url = route('module', [
            'module' => $this->name(),
            'action' => 'Show',
            'tree'   => $tree->name(),
        ]);

        return redirect($url);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function postRemoveAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree = Validator::attributes($request)->tree();
        $xref = Validator::queryParams($request)->isXref()->string('xref');
        $cart = Session::get('cart');
        $cart = is_array($cart) ? $cart : [];

        unset($cart[$tree->name()][$xref]);
        Session::put('cart', $cart);

        $url = route('module', [
            'module' => $this->name(),
            'action' => 'Show',
            'tree'   => $tree->name(),
        ]);

        return redirect($url);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getShowAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree = Validator::attributes($request)->tree();

        return $this->viewResponse('modules/clippings/show', [
            'module'  => $this->name(),
            'records' => $this->allRecordsInCart($tree),
            'title'   => I18N::translate('Family tree clippings cart'),
            'tree'    => $tree,
        ]);
    }

    /**
     * Get all the records in the cart.
     *
     * @param Tree $tree
     *
     * @return array<GedcomRecord>
     */
    private function allRecordsInCart(Tree $tree): array
    {
        $cart = Session::get('cart');
        $cart = is_array($cart) ? $cart : [];

        $xrefs = array_keys($cart[$tree->name()] ?? []);
        $xrefs = array_map('strval', $xrefs); // PHP converts numeric keys to integers.

        // Fetch all the records in the cart.
        $records = array_map(static fn (string $xref): GedcomRecord|null => Registry::gedcomRecordFactory()->make($xref, $tree), $xrefs);

        // Some records may have been deleted after they were added to the cart.
        $records = array_filter($records);

        // Group and sort.
        uasort($records, static fn (GedcomRecord $x, GedcomRecord $y): int => $x->tag() <=> $y->tag() ?: GedcomRecord::nameComparator()($x, $y));

        return $records;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getAddFamilyAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree   = Validator::attributes($request)->tree();
        $xref   = Validator::queryParams($request)->isXref()->string('xref');
        $family = Registry::familyFactory()->make($xref, $tree);
        $family = Auth::checkFamilyAccess($family);
        $name   = $family->fullName();

        $options = [
            self::ADD_RECORD_ONLY => $name,
            /* I18N: %s is a family (husband + wife) */
            self::ADD_CHILDREN    => I18N::translate('%s and their children', $name),
            /* I18N: %s is a family (husband + wife) */
            self::ADD_DESCENDANTS => I18N::translate('%s and their descendants', $name),
        ];

        $title = I18N::translate('Add %s to the clippings cart', $name);

        return $this->viewResponse('modules/clippings/add-options', [
            'options' => $options,
            'record'  => $family,
            'title'   => $title,
            'tree'    => $tree,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function postAddFamilyAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree   = Validator::attributes($request)->tree();
        $xref   = Validator::parsedBody($request)->isXref()->string('xref');
        $option = Validator::parsedBody($request)->string('option');

        $family = Registry::familyFactory()->make($xref, $tree);
        $family = Auth::checkFamilyAccess($family);

        switch ($option) {
            case self::ADD_RECORD_ONLY:
                $this->addFamilyToCart($family);
                break;

            case self::ADD_CHILDREN:
                $this->addFamilyAndChildrenToCart($family);
                break;

            case self::ADD_DESCENDANTS:
                $this->addFamilyAndDescendantsToCart($family);
                break;
        }

        return redirect($family->url());
    }

    /**
     * @param Family $family
     *
     * @return void
     */
    protected function addFamilyAndChildrenToCart(Family $family): void
    {
        $this->addFamilyToCart($family);

        foreach ($family->children() as $child) {
            $this->addIndividualToCart($child);
        }
    }

    /**
     * @param Family $family
     *
     * @return void
     */
    protected function addFamilyAndDescendantsToCart(Family $family): void
    {
        $this->addFamilyAndChildrenToCart($family);

        foreach ($family->children() as $child) {
            foreach ($child->spouseFamilies() as $child_family) {
                $this->addFamilyAndDescendantsToCart($child_family);
            }
        }
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getAddIndividualAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree       = Validator::attributes($request)->tree();
        $xref       = Validator::queryParams($request)->isXref()->string('xref');
        $individual = Registry::individualFactory()->make($xref, $tree);
        $individual = Auth::checkIndividualAccess($individual);
        $name       = $individual->fullName();

        if ($individual->sex() === 'F') {
            $options = [
                self::ADD_RECORD_ONLY       => $name,
                self::ADD_PARENT_FAMILIES   => I18N::translate('%s, her parents and siblings', $name),
                self::ADD_SPOUSE_FAMILIES   => I18N::translate('%s, her spouses and children', $name),
                self::ADD_ANCESTORS         => I18N::translate('%s and her ancestors', $name),
                self::ADD_ANCESTOR_FAMILIES => I18N::translate('%s, her ancestors and their families', $name),
                self::ADD_DESCENDANTS       => I18N::translate('%s, her spouses and descendants', $name),
            ];
        } else {
            $options = [
                self::ADD_RECORD_ONLY       => $name,
                self::ADD_PARENT_FAMILIES   => I18N::translate('%s, his parents and siblings', $name),
                self::ADD_SPOUSE_FAMILIES   => I18N::translate('%s, his spouses and children', $name),
                self::ADD_ANCESTORS         => I18N::translate('%s and his ancestors', $name),
                self::ADD_ANCESTOR_FAMILIES => I18N::translate('%s, his ancestors and their families', $name),
                self::ADD_DESCENDANTS       => I18N::translate('%s, his spouses and descendants', $name),
            ];
        }

        $title = I18N::translate('Add %s to the clippings cart', $name);

        return $this->viewResponse('modules/clippings/add-options', [
            'options' => $options,
            'record'  => $individual,
            'title'   => $title,
            'tree'    => $tree,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function postAddIndividualAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree   = Validator::attributes($request)->tree();
        $xref   = Validator::parsedBody($request)->isXref()->string('xref');
        $option = Validator::parsedBody($request)->string('option');

        $individual = Registry::individualFactory()->make($xref, $tree);
        $individual = Auth::checkIndividualAccess($individual);

        switch ($option) {
            case self::ADD_RECORD_ONLY:
                $this->addIndividualToCart($individual);
                break;

            case self::ADD_PARENT_FAMILIES:
                foreach ($individual->childFamilies() as $family) {
                    $this->addFamilyAndChildrenToCart($family);
                }
                break;

            case self::ADD_SPOUSE_FAMILIES:
                foreach ($individual->spouseFamilies() as $family) {
                    $this->addFamilyAndChildrenToCart($family);
                }
                break;

            case self::ADD_ANCESTORS:
                $this->addAncestorsToCart($individual);
                break;

            case self::ADD_ANCESTOR_FAMILIES:
                $this->addAncestorFamiliesToCart($individual);
                break;

            case self::ADD_DESCENDANTS:
                foreach ($individual->spouseFamilies() as $family) {
                    $this->addFamilyAndDescendantsToCart($family);
                }
                break;
        }

        return redirect($individual->url());
    }

    /**
     * @param Individual $individual
     *
     * @return void
     */
    protected function addAncestorsToCart(Individual $individual): void
    {
        $this->addIndividualToCart($individual);

        foreach ($individual->childFamilies() as $family) {
            $this->addFamilyToCart($family);

            foreach ($family->spouses() as $parent) {
                $this->addAncestorsToCart($parent);
            }
        }
    }

    /**
     * @param Individual $individual
     *
     * @return void
     */
    protected function addAncestorFamiliesToCart(Individual $individual): void
    {
        foreach ($individual->childFamilies() as $family) {
            $this->addFamilyAndChildrenToCart($family);

            foreach ($family->spouses() as $parent) {
                $this->addAncestorFamiliesToCart($parent);
            }
        }
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getAddLocationAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree     = Validator::attributes($request)->tree();
        $xref     = Validator::queryParams($request)->isXref()->string('xref');
        $location = Registry::locationFactory()->make($xref, $tree);
        $location = Auth::checkLocationAccess($location);
        $name     = $location->fullName();

        $options = [
            self::ADD_RECORD_ONLY => $name,
        ];

        $title = I18N::translate('Add %s to the clippings cart', $name);

        return $this->viewResponse('modules/clippings/add-options', [
            'options' => $options,
            'record'  => $location,
            'title'   => $title,
            'tree'    => $tree,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function postAddLocationAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree     = Validator::attributes($request)->tree();
        $xref     = Validator::queryParams($request)->isXref()->string('xref');
        $location = Registry::locationFactory()->make($xref, $tree);
        $location = Auth::checkLocationAccess($location);

        $this->addLocationToCart($location);

        return redirect($location->url());
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getAddMediaAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree  = Validator::attributes($request)->tree();
        $xref  = Validator::queryParams($request)->isXref()->string('xref');
        $media = Registry::mediaFactory()->make($xref, $tree);
        $media = Auth::checkMediaAccess($media);
        $name  = $media->fullName();

        $options = [
            self::ADD_RECORD_ONLY => $name,
        ];

        $title = I18N::translate('Add %s to the clippings cart', $name);

        return $this->viewResponse('modules/clippings/add-options', [
            'options' => $options,
            'record'  => $media,
            'title'   => $title,
            'tree'    => $tree,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function postAddMediaAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree  = Validator::attributes($request)->tree();
        $xref  = Validator::queryParams($request)->isXref()->string('xref');
        $media = Registry::mediaFactory()->make($xref, $tree);
        $media = Auth::checkMediaAccess($media);

        $this->addMediaToCart($media);

        return redirect($media->url());
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getAddNoteAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree = Validator::attributes($request)->tree();
        $xref = Validator::queryParams($request)->isXref()->string('xref');
        $note = Registry::noteFactory()->make($xref, $tree);
        $note = Auth::checkNoteAccess($note);
        $name = $note->fullName();

        $options = [
            self::ADD_RECORD_ONLY => $name,
        ];

        $title = I18N::translate('Add %s to the clippings cart', $name);

        return $this->viewResponse('modules/clippings/add-options', [
            'options' => $options,
            'record'  => $note,
            'title'   => $title,
            'tree'    => $tree,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function postAddNoteAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree = Validator::attributes($request)->tree();
        $xref = Validator::queryParams($request)->isXref()->string('xref');
        $note = Registry::noteFactory()->make($xref, $tree);
        $note = Auth::checkNoteAccess($note);

        $this->addNoteToCart($note);

        return redirect($note->url());
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getAddRepositoryAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree       = Validator::attributes($request)->tree();
        $xref       = Validator::queryParams($request)->isXref()->string('xref');
        $repository = Registry::repositoryFactory()->make($xref, $tree);
        $repository = Auth::checkRepositoryAccess($repository);
        $name       = $repository->fullName();

        $options = [
            self::ADD_RECORD_ONLY => $name,
        ];

        $title = I18N::translate('Add %s to the clippings cart', $name);

        return $this->viewResponse('modules/clippings/add-options', [
            'options' => $options,
            'record'  => $repository,
            'title'   => $title,
            'tree'    => $tree,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function postAddRepositoryAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree       = Validator::attributes($request)->tree();
        $xref       = Validator::queryParams($request)->isXref()->string('xref');
        $repository = Registry::repositoryFactory()->make($xref, $tree);
        $repository = Auth::checkRepositoryAccess($repository);

        $this->addRepositoryToCart($repository);

        foreach ($this->linked_record_service->linkedSources($repository) as $source) {
            $this->addSourceToCart($source);
        }

        return redirect($repository->url());
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getAddSourceAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree   = Validator::attributes($request)->tree();
        $xref   = Validator::queryParams($request)->isXref()->string('xref');
        $source = Registry::sourceFactory()->make($xref, $tree);
        $source = Auth::checkSourceAccess($source);
        $name   = $source->fullName();

        $options = [
            self::ADD_RECORD_ONLY        => $name,
            self::ADD_LINKED_INDIVIDUALS => I18N::translate('%s and the individuals that reference it.', $name),
        ];

        $title = I18N::translate('Add %s to the clippings cart', $name);

        return $this->viewResponse('modules/clippings/add-options', [
            'options' => $options,
            'record'  => $source,
            'title'   => $title,
            'tree'    => $tree,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function postAddSourceAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree   = Validator::attributes($request)->tree();
        $xref   = Validator::parsedBody($request)->isXref()->string('xref');
        $option = Validator::parsedBody($request)->string('option');

        $source = Registry::sourceFactory()->make($xref, $tree);
        $source = Auth::checkSourceAccess($source);

        $this->addSourceToCart($source);

        if ($option === self::ADD_LINKED_INDIVIDUALS) {
            foreach ($this->linked_record_service->linkedIndividuals($source) as $individual) {
                $this->addIndividualToCart($individual);
            }
            foreach ($this->linked_record_service->linkedFamilies($source) as $family) {
                $this->addFamilyToCart($family);
            }
        }

        return redirect($source->url());
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getAddSubmitterAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree      = Validator::attributes($request)->tree();
        $xref      = Validator::queryParams($request)->isXref()->string('xref');
        $submitter = Registry::submitterFactory()->make($xref, $tree);
        $submitter = Auth::checkSubmitterAccess($submitter);
        $name      = $submitter->fullName();

        $options = [
            self::ADD_RECORD_ONLY => $name,
        ];

        $title = I18N::translate('Add %s to the clippings cart', $name);

        return $this->viewResponse('modules/clippings/add-options', [
            'options' => $options,
            'record'  => $submitter,
            'title'   => $title,
            'tree'    => $tree,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function postAddSubmitterAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree      = Validator::attributes($request)->tree();
        $xref      = Validator::queryParams($request)->isXref()->string('xref');
        $submitter = Registry::submitterFactory()->make($xref, $tree);
        $submitter = Auth::checkSubmitterAccess($submitter);

        $this->addSubmitterToCart($submitter);

        return redirect($submitter->url());
    }

    /**
     * @param Family $family
     */
    protected function addFamilyToCart(Family $family): void
    {
        $cart = Session::get('cart');
        $cart = is_array($cart) ? $cart : [];

        $tree = $family->tree()->name();
        $xref = $family->xref();

        if (($cart[$tree][$xref] ?? false) === false) {
            $cart[$tree][$xref] = true;

            Session::put('cart', $cart);

            foreach ($family->spouses() as $spouse) {
                $this->addIndividualToCart($spouse);
            }

            $this->addLocationLinksToCart($family);
            $this->addMediaLinksToCart($family);
            $this->addNoteLinksToCart($family);
            $this->addSourceLinksToCart($family);
            $this->addSubmitterLinksToCart($family);
        }
    }

    /**
     * @param Individual $individual
     */
    protected function addIndividualToCart(Individual $individual): void
    {
        $cart = Session::get('cart');
        $cart = is_array($cart) ? $cart : [];

        $tree = $individual->tree()->name();
        $xref = $individual->xref();

        if (($cart[$tree][$xref] ?? false) === false) {
            $cart[$tree][$xref] = true;

            Session::put('cart', $cart);

            $this->addLocationLinksToCart($individual);
            $this->addMediaLinksToCart($individual);
            $this->addNoteLinksToCart($individual);
            $this->addSourceLinksToCart($individual);
        }
    }

    /**
     * @param Location $location
     */
    protected function addLocationToCart(Location $location): void
    {
        $cart = Session::get('cart');
        $cart = is_array($cart) ? $cart : [];

        $tree = $location->tree()->name();
        $xref = $location->xref();

        if (($cart[$tree][$xref] ?? false) === false) {
            $cart[$tree][$xref] = true;

            Session::put('cart', $cart);

            $this->addLocationLinksToCart($location);
            $this->addMediaLinksToCart($location);
            $this->addNoteLinksToCart($location);
            $this->addSourceLinksToCart($location);
        }
    }

    /**
     * @param GedcomRecord $record
     */
    protected function addLocationLinksToCart(GedcomRecord $record): void
    {
        preg_match_all('/\n\d _LOC @(' . Gedcom::REGEX_XREF . ')@/', $record->gedcom(), $matches);

        foreach ($matches[1] as $xref) {
            $location = Registry::locationFactory()->make($xref, $record->tree());

            if ($location instanceof Location && $location->canShow()) {
                $this->addLocationToCart($location);
            }
        }
    }

    /**
     * @param Media $media
     */
    protected function addMediaToCart(Media $media): void
    {
        $cart = Session::get('cart');
        $cart = is_array($cart) ? $cart : [];

        $tree = $media->tree()->name();
        $xref = $media->xref();

        if (($cart[$tree][$xref] ?? false) === false) {
            $cart[$tree][$xref] = true;

            Session::put('cart', $cart);

            $this->addNoteLinksToCart($media);
        }
    }

    /**
     * @param GedcomRecord $record
     */
    protected function addMediaLinksToCart(GedcomRecord $record): void
    {
        preg_match_all('/\n\d OBJE @(' . Gedcom::REGEX_XREF . ')@/', $record->gedcom(), $matches);

        foreach ($matches[1] as $xref) {
            $media = Registry::mediaFactory()->make($xref, $record->tree());

            if ($media instanceof Media && $media->canShow()) {
                $this->addMediaToCart($media);
            }
        }
    }

    /**
     * @param Note $note
     */
    protected function addNoteToCart(Note $note): void
    {
        $cart = Session::get('cart');
        $cart = is_array($cart) ? $cart : [];

        $tree = $note->tree()->name();
        $xref = $note->xref();

        if (($cart[$tree][$xref] ?? false) === false) {
            $cart[$tree][$xref] = true;

            Session::put('cart', $cart);
        }
    }

    /**
     * @param GedcomRecord $record
     */
    protected function addNoteLinksToCart(GedcomRecord $record): void
    {
        preg_match_all('/\n\d NOTE @(' . Gedcom::REGEX_XREF . ')@/', $record->gedcom(), $matches);

        foreach ($matches[1] as $xref) {
            $note = Registry::noteFactory()->make($xref, $record->tree());

            if ($note instanceof Note && $note->canShow()) {
                $this->addNoteToCart($note);
            }
        }
    }

    /**
     * @param Source $source
     */
    protected function addSourceToCart(Source $source): void
    {
        $cart = Session::get('cart');
        $cart = is_array($cart) ? $cart : [];

        $tree = $source->tree()->name();
        $xref = $source->xref();

        if (($cart[$tree][$xref] ?? false) === false) {
            $cart[$tree][$xref] = true;

            Session::put('cart', $cart);

            $this->addNoteLinksToCart($source);
            $this->addRepositoryLinksToCart($source);
        }
    }

    /**
     * @param GedcomRecord $record
     */
    protected function addSourceLinksToCart(GedcomRecord $record): void
    {
        preg_match_all('/\n\d SOUR @(' . Gedcom::REGEX_XREF . ')@/', $record->gedcom(), $matches);

        foreach ($matches[1] as $xref) {
            $source = Registry::sourceFactory()->make($xref, $record->tree());

            if ($source instanceof Source && $source->canShow()) {
                $this->addSourceToCart($source);
            }
        }
    }

    /**
     * @param Repository $repository
     */
    protected function addRepositoryToCart(Repository $repository): void
    {
        $cart = Session::get('cart');
        $cart = is_array($cart) ? $cart : [];

        $tree = $repository->tree()->name();
        $xref = $repository->xref();

        if (($cart[$tree][$xref] ?? false) === false) {
            $cart[$tree][$xref] = true;

            Session::put('cart', $cart);

            $this->addNoteLinksToCart($repository);
        }
    }

    /**
     * @param GedcomRecord $record
     */
    protected function addRepositoryLinksToCart(GedcomRecord $record): void
    {
        preg_match_all('/\n\d REPO @(' . Gedcom::REGEX_XREF . ')@/', $record->gedcom(), $matches);

        foreach ($matches[1] as $xref) {
            $repository = Registry::repositoryFactory()->make($xref, $record->tree());

            if ($repository instanceof Repository && $repository->canShow()) {
                $this->addRepositoryToCart($repository);
            }
        }
    }

    /**
     * @param Submitter $submitter
     */
    protected function addSubmitterToCart(Submitter $submitter): void
    {
        $cart = Session::get('cart');
        $cart = is_array($cart) ? $cart : [];
        $tree = $submitter->tree()->name();
        $xref = $submitter->xref();

        if (($cart[$tree][$xref] ?? false) === false) {
            $cart[$tree][$xref] = true;

            Session::put('cart', $cart);

            $this->addNoteLinksToCart($submitter);
        }
    }

    /**
     * @param GedcomRecord $record
     */
    protected function addSubmitterLinksToCart(GedcomRecord $record): void
    {
        preg_match_all('/\n\d SUBM @(' . Gedcom::REGEX_XREF . ')@/', $record->gedcom(), $matches);

        foreach ($matches[1] as $xref) {
            $submitter = Registry::submitterFactory()->make($xref, $record->tree());

            if ($submitter instanceof Submitter && $submitter->canShow()) {
                $this->addSubmitterToCart($submitter);
            }
        }
    }
}
