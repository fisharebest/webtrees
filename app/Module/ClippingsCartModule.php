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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Exceptions\FamilyNotFoundException;
use Fisharebest\Webtrees\Exceptions\IndividualNotFoundException;
use Fisharebest\Webtrees\Exceptions\MediaNotFoundException;
use Fisharebest\Webtrees\Exceptions\NoteNotFoundException;
use Fisharebest\Webtrees\Exceptions\RepositoryNotFoundException;
use Fisharebest\Webtrees\Exceptions\SourceNotFoundException;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Functions\FunctionsExport;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Module;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use League\Flysystem\Filesystem;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ClippingsCartModule
 */
class ClippingsCartModule extends AbstractModule implements ModuleMenuInterface
{
    // Routes that have a record which can be added to the clipboard
    const ROUTES_WITH_RECORDS = [
        'family',
        'individual',
        'media',
        'note',
        'repository',
        'source',
    ];

    /** {@inheritdoc} */
    public function getTitle(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Clippings cart');
    }

    /** {@inheritdoc} */
    public function getDescription(): string
    {
        /* I18N: Description of the “Clippings cart” module */
        return I18N::translate('Select records from your family tree and save them as a GEDCOM file.');
    }

    /**
     * What is the default access level for this module?
     *
     * Some modules are aimed at admins or managers, and are not generally shown to users.
     *
     * @return int
     */
    public function defaultAccessLevel(): int
    {
        return Auth::PRIV_USER;
    }

    /**
     * The user can re-order menus. Until they do, they are shown in this order.
     *
     * @return int
     */
    public function defaultMenuOrder(): int
    {
        return 20;
    }

    /**
     * A menu, to be added to the main application menu.
     *
     * @param Tree $tree
     *
     * @return Menu|null
     */
    public function getMenu(Tree $tree)
    {
        $request = Request::createFromGlobals();

        $route = $request->get('route');

        $submenus = [
            new Menu($this->getTitle(), route('module', [
                'module' => 'clippings',
                'action' => 'Show',
                'ged'    => $tree->getName(),
            ]), 'menu-clippings-cart', ['rel' => 'nofollow']),
        ];

        if (in_array($route, self::ROUTES_WITH_RECORDS)) {
            $xref      = $request->get('xref');
            $action    = 'Add' . ucfirst($route);
            $add_route = route('module', [
                'module' => 'clippings',
                'action' => $action,
                'xref'   => $xref,
                'ged'    => $tree->getName(),
            ]);

            $submenus[] = new Menu(I18N::translate('Add to the clippings cart'), $add_route, 'menu-clippings-add', ['rel' => 'nofollow']);
        }

        if (!$this->isCartEmpty($tree)) {
            $submenus[] = new Menu(I18N::translate('Empty the clippings cart'), route('module', [
                'module' => 'clippings',
                'action' => 'Empty',
                'ged'    => $tree->getName(),
            ]), 'menu-clippings-empty', ['rel' => 'nofollow']);
            $submenus[] = new Menu(I18N::translate('Download'), route('module', [
                'module' => 'clippings',
                'action' => 'DownloadForm',
                'ged'    => $tree->getName(),
            ]), 'menu-clippings-download', ['rel' => 'nofollow']);
        }

        return new Menu($this->getTitle(), '#', 'menu-clippings', ['rel' => 'nofollow'], $submenus);
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return BinaryFileResponse
     */
    public function getDownloadAction(Request $request, Tree $tree): BinaryFileResponse
    {
        $this->checkModuleAccess($tree);

        $privatize_export = $request->get('privatize_export');
        $convert          = (bool) $request->get('convert');

        $cart = Session::get('cart', []);

        $xrefs = array_keys($cart[$tree->getName()] ?? []);

        // Create a new/empty .ZIP file
        $temp_zip_file  = tempnam(sys_get_temp_dir(), 'webtrees-zip-');
        $zip_filesystem = new Filesystem(new ZipArchiveAdapter($temp_zip_file));

        // Media file prefix
        $path = $tree->getPreference('MEDIA_DIRECTORY');

        // GEDCOM file header
        $filetext = FunctionsExport::gedcomHeader($tree);

        // Include SUBM/SUBN records, if they exist
        $subn =
            Database::prepare("SELECT o_gedcom FROM `##other` WHERE o_type=? AND o_file=?")
                ->execute([
                    'SUBN',
                    $tree->getName(),
                ])
                ->fetchOne();
        if ($subn) {
            $filetext .= $subn . "\n";
        }
        $subm =
            Database::prepare("SELECT o_gedcom FROM `##other` WHERE o_type=? AND o_file=?")
                ->execute([
                    'SUBM',
                    $tree->getName(),
                ])
                ->fetchOne();
        if ($subm) {
            $filetext .= $subm . "\n";
        }

        switch ($privatize_export) {
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
            $object = GedcomRecord::getInstance($xref, $tree);
            // The object may have been deleted since we added it to the cart....
            if ($object) {
                $record = $object->privatizeGedcom($access_level);
                // Remove links to objects that aren't in the cart
                preg_match_all('/\n1 ' . WT_REGEX_TAG . ' @(' . WT_REGEX_XREF . ')@(\n[2-9].*)*/', $record, $matches, PREG_SET_ORDER);
                foreach ($matches as $match) {
                    if (!array_key_exists($match[1], $xrefs)) {
                        $record = str_replace($match[0], '', $record);
                    }
                }
                preg_match_all('/\n2 ' . WT_REGEX_TAG . ' @(' . WT_REGEX_XREF . ')@(\n[3-9].*)*/', $record, $matches, PREG_SET_ORDER);
                foreach ($matches as $match) {
                    if (!array_key_exists($match[1], $xrefs)) {
                        $record = str_replace($match[0], '', $record);
                    }
                }
                preg_match_all('/\n3 ' . WT_REGEX_TAG . ' @(' . WT_REGEX_XREF . ')@(\n[4-9].*)*/', $record, $matches, PREG_SET_ORDER);
                foreach ($matches as $match) {
                    if (!array_key_exists($match[1], $xrefs)) {
                        $record = str_replace($match[0], '', $record);
                    }
                }

                if ($convert) {
                    $record = utf8_decode($record);
                }
                switch ($object::RECORD_TYPE) {
                    case 'INDI':
                    case 'FAM':
                        $filetext .= $record . "\n";
                        $filetext .= "1 SOUR @WEBTREES@\n";
                        $filetext .= '2 PAGE ' . WT_BASE_URL . $object->url() . "\n";
                        break;
                    case 'SOUR':
                        $filetext .= $record . "\n";
                        $filetext .= '1 NOTE ' . WT_BASE_URL . $object->url() . "\n";
                        break;
                    case 'OBJE':
                        // Add the file to the archive
                        foreach ($object->mediaFiles() as $media_file) {
                            if (file_exists($media_file->getServerFilename())) {
                                $fp = fopen($media_file->getServerFilename(), 'r');
                                $zip_filesystem->writeStream($path . $media_file->filename(), $fp);
                                fclose($fp);
                            }
                        }
                        $filetext .= $record . "\n";
                        break;
                    default:
                        $filetext .= $record . "\n";
                        break;
                }
            }
        }

        // Create a source, to indicate the source of the data.
        $filetext .= "0 @WEBTREES@ SOUR\n1 TITL " . WT_BASE_URL . "\n";
        $author = User::find($tree->getPreference('CONTACT_EMAIL'));
        if ($author !== null) {
            $filetext .= '1 AUTH ' . $author->getRealName() . "\n";
        }
        $filetext .= "0 TRLR\n";

        // Make sure the preferred line endings are used
        $filetext = preg_replace("/[\r\n]+/", Gedcom::EOL, $filetext);

        if ($convert === 'yes') {
            $filetext = str_replace('UTF-8', 'ANSI', $filetext);
            $filetext = utf8_decode($filetext);
        }

        // Finally add the GEDCOM file to the .ZIP file.
        $zip_filesystem->write('clippings.ged', $filetext);

        // Need to force-close the filesystem
        $zip_filesystem = null;

        $response = new BinaryFileResponse($temp_zip_file);
        $response->deleteFileAfterSend(true);

        $response->headers->set('Content-Type', 'application/zip');
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'clippings.zip'
        );

        return $response;
    }

    /**
     * @param Tree $tree
     * @param User $user
     *
     * @return Response
     */
    public function getDownloadFormAction(Tree $tree, User $user): Response
    {
        $title = I18N::translate('Family tree clippings cart') . ' — ' . I18N::translate('Download');

        return $this->viewResponse('modules/clippings/download', [
            'is_manager' => Auth::isManager($tree, $user),
            'is_member'  => Auth::isMember($tree, $user),
            'title'      => $title,
        ]);
    }

    /**
     * @param Tree $tree
     *
     * @return RedirectResponse
     */
    public function getEmptyAction(Tree $tree): RedirectResponse
    {
        $cart                   = Session::get('cart', []);
        $cart[$tree->getName()] = [];
        Session::put('cart', $cart);

        $url = route('module', [
            'module' => 'clippings',
            'action' => 'Show',
            'ged'    => $tree->getName(),
        ]);

        return new RedirectResponse($url);
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return RedirectResponse
     */
    public function postRemoveAction(Request $request, Tree $tree): RedirectResponse
    {
        $xref = $request->get('xref');

        $cart = Session::get('cart', []);
        unset($cart[$tree->getName()][$xref]);
        Session::put('cart', $cart);

        $url = route('module', [
            'module' => 'clippings',
            'action' => 'Show',
            'ged'    => $tree->getName(),
        ]);

        return new RedirectResponse($url);
    }

    /**
     * @param Tree $tree
     *
     * @return Response
     */
    public function getShowAction(Tree $tree): Response
    {
        return $this->viewResponse('modules/clippings/show', [
            'records' => $this->allRecordsInCart($tree),
            'title'   => I18N::translate('Family tree clippings cart'),
            'tree'    => $tree,
        ]);
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return Response
     */
    public function getAddFamilyAction(Request $request, Tree $tree): Response
    {
        $xref = $request->get('xref');

        $family = Family::getInstance($xref, $tree);

        if ($family === null) {
            throw new FamilyNotFoundException();
        }

        $options = $this->familyOptions($family);

        $title = I18N::translate('Add %s to the clippings cart', $family->getFullName());

        return $this->viewResponse('modules/clippings/add-options', [
            'options' => $options,
            'default' => key($options),
            'record'  => $family,
            'title'   => $title,
            'tree'    => $tree,
        ]);
    }

    /**
     * @param Family $family
     *
     * @return string[]
     */
    private function familyOptions(Family $family): array
    {
        $name = strip_tags($family->getFullName());

        return [
            'parents'     => $name,
            /* I18N: %s is a family (husband + wife) */
            'members'     => I18N::translate('%s and their children', $name),
            /* I18N: %s is a family (husband + wife) */
            'descendants' => I18N::translate('%s and their descendants', $name),
        ];
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return RedirectResponse
     */
    public function postAddFamilyAction(Request $request, Tree $tree): RedirectResponse
    {
        $xref   = $request->get('xref');
        $option = $request->get('option');

        $family = Family::getInstance($xref, $tree);

        if ($family === null) {
            throw new FamilyNotFoundException();
        }

        switch ($option) {
            case 'parents':
                $this->addFamilyToCart($family);
                break;

            case 'members':
                $this->addFamilyAndChildrenToCart($family);
                break;

            case 'descendants':
                $this->addFamilyAndDescendantsToCart($family);
                break;
        }

        return new RedirectResponse($family->url());
    }

    /**
     * @param Family $family
     */
    private function addFamilyToCart(Family $family)
    {
        $this->addRecordToCart($family);

        foreach ($family->getSpouses() as $spouse) {
            $this->addRecordToCart($spouse);
        }
    }

    /**
     * @param Family $family
     */
    private function addFamilyAndChildrenToCart(Family $family)
    {
        $this->addRecordToCart($family);

        foreach ($family->getSpouses() as $spouse) {
            $this->addRecordToCart($spouse);
        }
        foreach ($family->getChildren() as $child) {
            $this->addRecordToCart($child);
        }
    }

    /**
     * @param Family $family
     */
    private function addFamilyAndDescendantsToCart(Family $family)
    {
        $this->addRecordToCart($family);

        foreach ($family->getSpouses() as $spouse) {
            $this->addRecordToCart($spouse);
        }
        foreach ($family->getChildren() as $child) {
            $this->addRecordToCart($child);
            foreach ($child->getSpouseFamilies() as $child_family) {
                $this->addFamilyAndDescendantsToCart($child_family);
            }
        }
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return Response
     */
    public function getAddIndividualAction(Request $request, Tree $tree): Response
    {
        $xref = $request->get('xref');

        $individual = Individual::getInstance($xref, $tree);

        if ($individual === null) {
            throw new IndividualNotFoundException();
        }

        $options = $this->individualOptions($individual);

        $title = I18N::translate('Add %s to the clippings cart', $individual->getFullName());

        return $this->viewResponse('modules/clippings/add-options', [
            'options' => $options,
            'default' => key($options),
            'record'  => $individual,
            'title'   => $title,
            'tree'    => $tree,
        ]);
    }

    /**
     * @param Individual $individual
     *
     * @return string[]
     */
    private function individualOptions(Individual $individual): array
    {
        $name = strip_tags($individual->getFullName());

        if ($individual->getSex() === 'F') {
            return [
                'self'              => $name,
                'parents'           => I18N::translate('%s, her parents and siblings', $name),
                'spouses'           => I18N::translate('%s, her spouses and children', $name),
                'ancestors'         => I18N::translate('%s and her ancestors', $name),
                'ancestor_families' => I18N::translate('%s, her ancestors and their families', $name),
                'descendants'       => I18N::translate('%s, her spouses and descendants', $name),
            ];
        }

        return [
            'self'              => $name,
            'parents'           => I18N::translate('%s, his parents and siblings', $name),
            'spouses'           => I18N::translate('%s, his spouses and children', $name),
            'ancestors'         => I18N::translate('%s and his ancestors', $name),
            'ancestor_families' => I18N::translate('%s, his ancestors and their families', $name),
            'descendants'       => I18N::translate('%s, his spouses and descendants', $name),
        ];
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return RedirectResponse
     */
    public function postAddIndividualAction(Request $request, Tree $tree): RedirectResponse
    {
        $xref   = $request->get('xref');
        $option = $request->get('option');

        $individual = Individual::getInstance($xref, $tree);

        if ($individual === null) {
            throw new IndividualNotFoundException();
        }

        switch ($option) {
            case 'self':
                $this->addRecordToCart($individual);
                break;

            case 'parents':
                foreach ($individual->getChildFamilies() as $family) {
                    $this->addFamilyAndChildrenToCart($family);
                }
                break;

            case 'spouses':
                foreach ($individual->getSpouseFamilies() as $family) {
                    $this->addFamilyAndChildrenToCart($family);
                }
                break;

            case 'ancestors':
                $this->addAncestorsToCart($individual);
                break;

            case 'ancestor_families':
                $this->addAncestorFamiliesToCart($individual);
                break;

            case 'descendants':
                foreach ($individual->getSpouseFamilies() as $family) {
                    $this->addFamilyAndDescendantsToCart($family);
                }
                break;
        }

        return new RedirectResponse($individual->url());
    }

    /**
     * @param Individual $individual
     */
    private function addAncestorsToCart(Individual $individual)
    {
        $this->addRecordToCart($individual);

        foreach ($individual->getChildFamilies() as $family) {
            foreach ($family->getSpouses() as $parent) {
                $this->addAncestorsToCart($parent);
            }
        }
    }

    /**
     * @param Individual $individual
     */
    private function addAncestorFamiliesToCart(Individual $individual)
    {
        foreach ($individual->getChildFamilies() as $family) {
            $this->addFamilyAndChildrenToCart($family);
            foreach ($family->getSpouses() as $parent) {
                $this->addAncestorsToCart($parent);
            }
        }
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return Response
     */
    public function getAddMediaAction(Request $request, Tree $tree): Response
    {
        $xref = $request->get('xref');

        $media = Media::getInstance($xref, $tree);

        if ($media === null) {
            throw new MediaNotFoundException();
        }

        $options = $this->mediaOptions($media);

        $title = I18N::translate('Add %s to the clippings cart', $media->getFullName());

        return $this->viewResponse('modules/clippings/add-options', [
            'options' => $options,
            'default' => key($options),
            'record'  => $media,
            'title'   => $title,
            'tree'    => $tree,
        ]);
    }

    /**
     * @param Media $media
     *
     * @return string[]
     */
    private function mediaOptions(Media $media): array
    {
        $name = strip_tags($media->getFullName());

        return [
            'self' => $name,
        ];
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return RedirectResponse
     */
    public function postAddMediaAction(Request $request, Tree $tree): RedirectResponse
    {
        $xref = $request->get('xref');

        $media = Media::getInstance($xref, $tree);

        if ($media === null) {
            throw new MediaNotFoundException();
        }

        $this->addRecordToCart($media);

        return new RedirectResponse($media->url());
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return Response
     */
    public function getAddNoteAction(Request $request, Tree $tree): Response
    {
        $xref = $request->get('xref');

        $note = Note::getInstance($xref, $tree);

        if ($note === null) {
            throw new NoteNotFoundException();
        }

        $options = $this->noteOptions($note);

        $title = I18N::translate('Add %s to the clippings cart', $note->getFullName());

        return $this->viewResponse('modules/clippings/add-options', [
            'options' => $options,
            'default' => key($options),
            'record'  => $note,
            'title'   => $title,
            'tree'    => $tree,
        ]);
    }

    /**
     * @param Note $note
     *
     * @return string[]
     */
    private function noteOptions(Note $note): array
    {
        $name = strip_tags($note->getFullName());

        return [
            'self' => $name,
        ];
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return RedirectResponse
     */
    public function postAddNoteAction(Request $request, Tree $tree): RedirectResponse
    {
        $xref = $request->get('xref');

        $note = Note::getInstance($xref, $tree);

        if ($note === null) {
            throw new NoteNotFoundException();
        }

        $this->addRecordToCart($note);

        return new RedirectResponse($note->url());
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return Response
     */
    public function getAddRepositoryAction(Request $request, Tree $tree): Response
    {
        $xref = $request->get('xref');

        $repository = Repository::getInstance($xref, $tree);

        if ($repository === null) {
            throw new RepositoryNotFoundException();
        }

        $options = $this->repositoryOptions($repository);

        $title = I18N::translate('Add %s to the clippings cart', $repository->getFullName());

        return $this->viewResponse('modules/clippings/add-options', [
            'options' => $options,
            'default' => key($options),
            'record'  => $repository,
            'title'   => $title,
            'tree'    => $tree,
        ]);
    }

    /**
     * @param Repository $repository
     *
     * @return string[]
     */
    private function repositoryOptions(Repository $repository): array
    {
        $name = strip_tags($repository->getFullName());

        return [
            'self' => $name,
        ];
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return RedirectResponse
     */
    public function postAddRepositoryAction(Request $request, Tree $tree): RedirectResponse
    {
        $xref = $request->get('xref');

        $repository = Repository::getInstance($xref, $tree);

        if ($repository === null) {
            throw new RepositoryNotFoundException();
        }

        $this->addRecordToCart($repository);

        return new RedirectResponse($repository->url());
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return Response
     */
    public function getAddSourceAction(Request $request, Tree $tree): Response
    {
        $xref = $request->get('xref');

        $source = Source::getInstance($xref, $tree);

        if ($source === null) {
            throw new SourceNotFoundException();
        }

        $options = $this->sourceOptions($source);

        $title = I18N::translate('Add %s to the clippings cart', $source->getFullName());

        return $this->viewResponse('modules/clippings/add-options', [
            'options' => $options,
            'default' => key($options),
            'record'  => $source,
            'title'   => $title,
            'tree'    => $tree,
        ]);
    }

    /**
     * @param Source $source
     *
     * @return string[]
     */
    private function sourceOptions(Source $source): array
    {
        $name = strip_tags($source->getFullName());

        return [
            'only'   => strip_tags($source->getFullName()),
            'linked' => I18N::translate('%s and the individuals that reference it.', $name),
        ];
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return RedirectResponse
     */
    public function postAddSourceAction(Request $request, Tree $tree): RedirectResponse
    {
        $xref   = $request->get('xref');
        $option = $request->get('option');

        $source = Source::getInstance($xref, $tree);

        if ($source === null) {
            throw new SourceNotFoundException();
        }

        $this->addRecordToCart($source);

        if ($option === 'linked') {
            foreach ($source->linkedIndividuals('SOUR') as $individual) {
                $this->addRecordToCart($individual);
            }
            foreach ($source->linkedFamilies('SOUR') as $family) {
                $this->addRecordToCart($family);
            }
        }

        return new RedirectResponse($source->url());
    }

    /**
     * Get all the records in the cart.
     *
     * @param Tree $tree
     *
     * @return GedcomRecord[]
     */
    private function allRecordsInCart(Tree $tree): array
    {
        $cart = Session::get('cart', []);

        $xrefs = array_keys($cart[$tree->getName()] ?? []);

        // Fetch all the records in the cart.
        $records = array_map(function (string $xref) use ($tree) {
            return GedcomRecord::getInstance($xref, $tree);
        }, $xrefs);

        // Some records may have been deleted after they were added to the cart.
        $records = array_filter($records);

        // Group and sort.
        uasort($records, function (GedcomRecord $x, GedcomRecord $y) {
            return $x::RECORD_TYPE <=> $y::RECORD_TYPE ?: GedcomRecord::compare($x, $y);
        });

        return $records;
    }

    /**
     * Add a record (and direclty linked sources, notes, etc. to the cart.
     *
     * @param GedcomRecord $record
     */
    private function addRecordToCart(GedcomRecord $record)
    {
        $cart = Session::get('cart', []);

        $tree_name = $record->getTree()->getName();

        // Add this record
        $cart[$tree_name][$record->getXref()] = true;

        // Add directly linked media, notes, repositories and sources.
        preg_match_all('/\n\d (?:OBJE|NOTE|SOUR|REPO) @(' . WT_REGEX_XREF . ')@/', $record->getGedcom(), $matches);

        foreach ($matches[1] as $match) {
            $cart[$tree_name][$match] = true;
        }

        Session::put('cart', $cart);
    }

    /**
     * @param Tree $tree
     *
     * @return bool
     */
    private function isCartEmpty(Tree $tree): bool
    {
        $cart = Session::get('cart', []);

        return empty($cart[$tree->getName()]);
    }

    /**
     * Only allow access to the routes/functions if the menu is active
     *
     * @param Tree $tree
     */
    private function checkModuleAccess(Tree $tree)
    {
        if (!array_key_exists($this->getName(), Module::getActiveMenus($tree))) {
            throw new NotFoundHttpException();
        }
    }
}
