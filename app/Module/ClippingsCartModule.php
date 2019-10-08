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
use Fisharebest\Webtrees\Http\RequestHandlers\ModuleAction;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Tree;
use League\Flysystem\Filesystem;
use League\Flysystem\MountManager;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;

use function app;
use function array_filter;
use function array_keys;
use function array_map;
use function in_array;
use function key;
use function preg_match_all;
use function redirect;
use function route;
use function str_replace;
use function strip_tags;
use function sys_get_temp_dir;
use function tempnam;
use function ucfirst;
use function utf8_decode;

/**
 * Class ClippingsCartModule
 */
class ClippingsCartModule extends AbstractModule implements ModuleMenuInterface
{
    use ModuleMenuTrait;

    // Routes that have a record which can be added to the clipboard
    private const ROUTES_WITH_RECORDS = [
        'family',
        'individual',
        'media',
        'note',
        'repository',
        'source',
    ];

    /** @var int The default access level for this module.  It can be changed in the control panel. */
    protected $access_level = Auth::PRIV_USER;

    /**
     * @var UserService
     */
    private $user_service;

    /**
     * ClippingsCartModule constructor.
     *
     * @param UserService $user_service
     */
    public function __construct(UserService $user_service)
    {
        $this->user_service = $user_service;
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
     *
     * @param Tree $tree
     *
     * @return Menu|null
     */
    public function getMenu(Tree $tree): ?Menu
    {
        /** @var ServerRequestInterface $request */
        $request = app(ServerRequestInterface::class);

        $route = $request->getQueryParams()['route'] ?? '';

        $submenus = [
            new Menu($this->title(), route('module', [
                'module' => $this->name(),
                'action' => 'Show',
                'ged'    => $tree->name(),
            ]), 'menu-clippings-cart', ['rel' => 'nofollow']),
        ];

        if (in_array($route, self::ROUTES_WITH_RECORDS, true)) {
            $xref      = $request->getQueryParams()['xref'] ?? '';
            $action    = 'Add' . ucfirst($route);
            $add_route = route('module', [
                'module' => $this->name(),
                'action' => $action,
                'xref'   => $xref,
                'ged'    => $tree->name(),
            ]);

            $submenus[] = new Menu(I18N::translate('Add to the clippings cart'), $add_route, 'menu-clippings-add', ['rel' => 'nofollow']);
        }

        if (!$this->isCartEmpty($tree)) {
            $submenus[] = new Menu(I18N::translate('Empty the clippings cart'), route('module', [
                'module' => $this->name(),
                'action' => 'Empty',
                'ged'    => $tree->name(),
            ]), 'menu-clippings-empty', ['rel' => 'nofollow']);
            $submenus[] = new Menu(I18N::translate('Download'), route('module', [
                'module' => $this->name(),
                'action' => 'DownloadForm',
                'ged'    => $tree->name(),
            ]), 'menu-clippings-download', ['rel' => 'nofollow']);
        }

        return new Menu($this->title(), '#', 'menu-clippings', ['rel' => 'nofollow'], $submenus);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getDownloadAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree   = $request->getAttribute('tree');
        $params = $request->getQueryParams();

        $privatize_export = $params['privatize_export'];
        $convert          = (bool) ($params['convert'] ?? false);

        $cart = Session::get('cart', []);

        $xrefs = array_keys($cart[$tree->name()] ?? []);

        // Create a new/empty .ZIP file
        $temp_zip_file  = tempnam(sys_get_temp_dir(), 'webtrees-zip-');
        $zip_filesystem = new Filesystem(new ZipArchiveAdapter($temp_zip_file));

        $manager = new MountManager([
            'media' => $tree->mediaFilesystem(),
            'zip'   => $zip_filesystem,
        ]);

        // Media file prefix
        $path = $tree->getPreference('MEDIA_DIRECTORY');

        // GEDCOM file header
        $filetext = FunctionsExport::gedcomHeader($tree, $convert ? 'ANSI' : 'UTF-8');

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
            if ($object instanceof  GedcomRecord) {
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

                if ($object instanceof Individual || $object instanceof Family) {
                    $filetext .= $record . "\n";
                    $filetext .= "1 SOUR @WEBTREES@\n";
                    $filetext .= '2 PAGE ' . $object->url() . "\n";
                } elseif ($object instanceof Source) {
                    $filetext .= $record . "\n";
                    $filetext .= '1 NOTE ' . $object->url() . "\n";
                } elseif ($object instanceof Media) {
                    // Add the media files to the archive
                    foreach ($object->mediaFiles() as $media_file) {
                        $from = 'media://' . $media_file->filename();
                        $to   = 'zip://' . $path . $media_file->filename();
                        if (!$media_file->isExternal() && $manager->has($from)) {
                            $manager->copy($from, $to);
                        }
                    }
                    $filetext .= $record . "\n";
                } else {
                    $filetext .= $record . "\n";
                }
            }
        }

        $base_url = $request->getAttribute('base_url');

        // Create a source, to indicate the source of the data.
        $filetext .= "0 @WEBTREES@ SOUR\n1 TITL " . $base_url . "\n";
        $author   = $this->user_service->find((int) $tree->getPreference('CONTACT_USER_ID'));
        if ($author !== null) {
            $filetext .= '1 AUTH ' . $author->realName() . "\n";
        }
        $filetext .= "0 TRLR\n";

        // Make sure the preferred line endings are used
        $filetext = str_replace('\n', Gedcom::EOL, $filetext);

        if ($convert) {
            $filetext = utf8_decode($filetext);
        }

        // Finally add the GEDCOM file to the .ZIP file.
        $zip_filesystem->write('clippings.ged', $filetext);

        // Need to force-close ZipArchive filesystems.
        $zip_filesystem->getAdapter()->getArchive()->close();

        // Use a stream, so that we do not have to load the entire file into memory.
        $stream = app(StreamFactoryInterface::class)->createStreamFromFile($temp_zip_file);

        /** @var ResponseFactoryInterface $response_factory */
        $response_factory = app(ResponseFactoryInterface::class);

        return $response_factory->createResponse()
            ->withBody($stream)
            ->withHeader('Content-Type', 'application/zip')
            ->withHeader('Content-Disposition', 'attachment; filename="clippings.zip');
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getDownloadFormAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree  = $request->getAttribute('tree');
        $user  = $request->getAttribute('user');
        $title = I18N::translate('Family tree clippings cart') . ' — ' . I18N::translate('Download');

        return $this->viewResponse('modules/clippings/download', [
            'is_manager' => Auth::isManager($tree, $user),
            'is_member'  => Auth::isMember($tree, $user),
            'module'     => $this->name(),
            'title'      => $title,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getEmptyAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree                = $request->getAttribute('tree');
        $cart                = Session::get('cart', []);
        $cart[$tree->name()] = [];
        Session::put('cart', $cart);

        $url = route('module', [
            'module' => $this->name(),
            'action' => 'Show',
            'ged'    => $tree->name(),
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
        $tree = $request->getAttribute('tree');
        $xref = $request->getQueryParams()['xref'];

        $cart = Session::get('cart', []);
        unset($cart[$tree->name()][$xref]);
        Session::put('cart', $cart);

        $url = route('module', [
            'module' => $this->name(),
            'action' => 'Show',
            'ged'    => $tree->name(),
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
        $tree = $request->getAttribute('tree');

        return $this->viewResponse('modules/clippings/show', [
            'records' => $this->allRecordsInCart($tree),
            'title'   => I18N::translate('Family tree clippings cart'),
            'tree'    => $tree,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getAddFamilyAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        $xref = $request->getQueryParams()['xref'];

        $family = Family::getInstance($xref, $tree);

        if ($family === null) {
            throw new FamilyNotFoundException();
        }

        $options = $this->familyOptions($family);

        $title = I18N::translate('Add %s to the clippings cart', $family->fullName());

        return $this->viewResponse('modules/clippings/add-options', [
            'action'  => route('module', ['module' => $this->name(), 'action' => 'AddFamily']),
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
        $name = strip_tags($family->fullName());

        return [
            'parents'     => $name,
            /* I18N: %s is a family (husband + wife) */
            'members'     => I18N::translate('%s and their children', $name),
            /* I18N: %s is a family (husband + wife) */
            'descendants' => I18N::translate('%s and their descendants', $name),
        ];
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function postAddFamilyAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree   = $request->getAttribute('tree');
        $xref   = $request->getQueryParams()['xref'];
        $option = $request->getParsedBody()['option'];

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

        return redirect($family->url());
    }

    /**
     * @param Family $family
     *
     * @return void
     */
    private function addFamilyToCart(Family $family): void
    {
        $this->addRecordToCart($family);

        foreach ($family->spouses() as $spouse) {
            $this->addRecordToCart($spouse);
        }
    }

    /**
     * @param Family $family
     *
     * @return void
     */
    private function addFamilyAndChildrenToCart(Family $family): void
    {
        $this->addRecordToCart($family);

        foreach ($family->spouses() as $spouse) {
            $this->addRecordToCart($spouse);
        }
        foreach ($family->children() as $child) {
            $this->addRecordToCart($child);
        }
    }

    /**
     * @param Family $family
     *
     * @return void
     */
    private function addFamilyAndDescendantsToCart(Family $family): void
    {
        $this->addRecordToCart($family);

        foreach ($family->spouses() as $spouse) {
            $this->addRecordToCart($spouse);
        }
        foreach ($family->children() as $child) {
            $this->addRecordToCart($child);
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
        $tree = $request->getAttribute('tree');
        $xref = $request->getQueryParams()['xref'];

        $individual = Individual::getInstance($xref, $tree);

        if ($individual === null) {
            throw new IndividualNotFoundException();
        }

        $options = $this->individualOptions($individual);

        $title = I18N::translate('Add %s to the clippings cart', $individual->fullName());

        return $this->viewResponse('modules/clippings/add-options', [
            'action'  => route('module', ['module' => $this->name(), 'action' => 'AddIndividual']),
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
        $name = strip_tags($individual->fullName());

        if ($individual->sex() === 'F') {
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
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function postAddIndividualAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree   = $request->getAttribute('tree');
        $xref   = $request->getQueryParams()['xref'];
        $option = $request->getParsedBody()['option'];

        $individual = Individual::getInstance($xref, $tree);

        if ($individual === null) {
            throw new IndividualNotFoundException();
        }

        switch ($option) {
            case 'self':
                $this->addRecordToCart($individual);
                break;

            case 'parents':
                foreach ($individual->childFamilies() as $family) {
                    $this->addFamilyAndChildrenToCart($family);
                }
                break;

            case 'spouses':
                foreach ($individual->spouseFamilies() as $family) {
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
    private function addAncestorsToCart(Individual $individual): void
    {
        $this->addRecordToCart($individual);

        foreach ($individual->childFamilies() as $family) {
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
    private function addAncestorFamiliesToCart(Individual $individual): void
    {
        foreach ($individual->childFamilies() as $family) {
            $this->addFamilyAndChildrenToCart($family);
            foreach ($family->spouses() as $parent) {
                $this->addAncestorsToCart($parent);
            }
        }
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getAddMediaAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        $xref = $request->getQueryParams()['xref'];

        $media = Media::getInstance($xref, $tree);

        if ($media === null) {
            throw new MediaNotFoundException();
        }

        $options = $this->mediaOptions($media);

        $title = I18N::translate('Add %s to the clippings cart', $media->fullName());

        return $this->viewResponse('modules/clippings/add-options', [
            'action'  => route('module', ['module' => $this->name(), 'action' => 'AddMedia']),
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
        $name = strip_tags($media->fullName());

        return [
            'self' => $name,
        ];
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function postAddMediaAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        $xref = $request->getQueryParams()['xref'];

        $media = Media::getInstance($xref, $tree);

        if ($media === null) {
            throw new MediaNotFoundException();
        }

        $this->addRecordToCart($media);

        return redirect($media->url());
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getAddNoteAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        $xref = $request->getQueryParams()['xref'];

        $note = Note::getInstance($xref, $tree);

        if ($note === null) {
            throw new NoteNotFoundException();
        }

        $options = $this->noteOptions($note);

        $title = I18N::translate('Add %s to the clippings cart', $note->fullName());

        return $this->viewResponse('modules/clippings/add-options', [
            'action'  => route('module', ['module' => $this->name(), 'action' => 'AddNote']),
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
        $name = strip_tags($note->fullName());

        return [
            'self' => $name,
        ];
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function postAddNoteAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        $xref = $request->getQueryParams()['xref'];

        $note = Note::getInstance($xref, $tree);

        if ($note === null) {
            throw new NoteNotFoundException();
        }

        $this->addRecordToCart($note);

        return redirect($note->url());
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getAddRepositoryAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        $xref = $request->getQueryParams()['xref'];

        $repository = Repository::getInstance($xref, $tree);

        if ($repository === null) {
            throw new RepositoryNotFoundException();
        }

        $options = $this->repositoryOptions($repository);

        $title = I18N::translate('Add %s to the clippings cart', $repository->fullName());

        return $this->viewResponse('modules/clippings/add-options', [
            'action'  => route('module', ['module' => $this->name(), 'action' => 'AddRepository']),
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
        $name = strip_tags($repository->fullName());

        return [
            'self' => $name,
        ];
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function postAddRepositoryAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        $xref = $request->getQueryParams()['xref'];

        $repository = Repository::getInstance($xref, $tree);

        if ($repository === null) {
            throw new RepositoryNotFoundException();
        }

        $this->addRecordToCart($repository);

        return redirect($repository->url());
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getAddSourceAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        $xref = $request->getQueryParams()['xref'];

        $source = Source::getInstance($xref, $tree);

        if ($source === null) {
            throw new SourceNotFoundException();
        }

        $options = $this->sourceOptions($source);

        $title = I18N::translate('Add %s to the clippings cart', $source->fullName());

        return $this->viewResponse('modules/clippings/add-options', [
            'action'  => route('module', ['module' => $this->name(), 'action' => 'AddSource']),
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
        $name = strip_tags($source->fullName());

        return [
            'only'   => strip_tags($source->fullName()),
            'linked' => I18N::translate('%s and the individuals that reference it.', $name),
        ];
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function postAddSourceAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        $xref   = $request->getQueryParams()['xref'];
        $option = $request->getParsedBody()['option'];

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

        return redirect($source->url());
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

        $xrefs = array_keys($cart[$tree->name()] ?? []);

        // Fetch all the records in the cart.
        $records = array_map(static function (string $xref) use ($tree): ?GedcomRecord {
            return GedcomRecord::getInstance($xref, $tree);
        }, $xrefs);

        // Some records may have been deleted after they were added to the cart.
        $records = array_filter($records);

        // Group and sort.
        uasort($records, static function (GedcomRecord $x, GedcomRecord $y): int {
            return $x::RECORD_TYPE <=> $y::RECORD_TYPE ?: GedcomRecord::nameComparator()($x, $y);
        });

        return $records;
    }

    /**
     * Add a record (and direclty linked sources, notes, etc. to the cart.
     *
     * @param GedcomRecord $record
     *
     * @return void
     */
    private function addRecordToCart(GedcomRecord $record): void
    {
        $cart = Session::get('cart', []);

        $tree_name = $record->tree()->name();

        // Add this record
        $cart[$tree_name][$record->xref()] = true;

        // Add directly linked media, notes, repositories and sources.
        preg_match_all('/\n\d (?:OBJE|NOTE|SOUR|REPO) @(' . Gedcom::REGEX_XREF . ')@/', $record->gedcom(), $matches);

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

        return empty($cart[$tree->name()]);
    }
}
