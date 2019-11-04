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

namespace Fisharebest\Webtrees\Http\Controllers\Admin;

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Services\PendingChangesService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Webtrees;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Intervention\Image\ImageManager;
use League\Flysystem\Filesystem;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

use function assert;

/**
 * Controller for importing custom thumbnails from webtrees 1.x.
 */
class ImportThumbnailsController extends AbstractAdminController
{
    /** @var TreeService */
    private $tree_service;

    /** @var PendingChangesService */
    private $pending_changes_service;

    /**
     * ImportThumbnailsController constructor.
     *
     * @param PendingChangesService $pending_changes_service
     * @param TreeService           $tree_service
     */
    public function __construct(PendingChangesService $pending_changes_service, TreeService $tree_service)
    {
        $this->pending_changes_service = $pending_changes_service;
        $this->tree_service            = $tree_service;
    }

    /**
     * Import custom thumbnails from webtres 1.x.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function webtrees1Thumbnails(ServerRequestInterface $request): ResponseInterface
    {
        return $this->viewResponse('admin/webtrees1-thumbnails', [
            'title' => I18N::translate('Import custom thumbnails from webtrees version 1'),
        ]);
    }

    /**
     * Import custom thumbnails from webtres 1.x.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function webtrees1ThumbnailsAction(ServerRequestInterface $request): ResponseInterface
    {
        $thumbnail = $request->getParsedBody()['thumbnail'];
        $action    = $request->getParsedBody()['action'];
        $xrefs     = $request->getParsedBody()['xref'];
        $geds      = $request->getParsedBody()['ged'];

        $media_objects = [];

        foreach ($xrefs as $key => $xref) {
            $tree            = $this->tree_service->all()->get($geds[$key]);
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
                    $gedcom = '1 FILE ' . substr($move_to, strlen($prefix)) . "\n2 FORM " . $extension;

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
                    $this->pending_changes_service->acceptRecord($media_object);
                }
                break;
        }

        return response([]);
    }

    /**
     * Import custom thumbnails from webtres 1.x.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function webtrees1ThumbnailsData(ServerRequestInterface $request): ResponseInterface
    {
        $data_filesystem = $request->getAttribute('filesystem.data');
        assert($data_filesystem instanceof Filesystem);

        $start  = (int) $request->getQueryParams()['start'];
        $length = (int) $request->getQueryParams()['length'];
        $search = $request->getQueryParams()['search']['value'];

        // Fetch all thumbnails
        $thumbnails = Collection::make($data_filesystem->listContents('', true))
            ->filter(static function (array $metadata): bool {
                return $metadata['type'] === 'file' && strpos($metadata['path'], '/thumbs/') !== false;
            })
            ->map(static function (array $metadata): string {
                return $metadata['path'];
            });

        $recordsTotal = $thumbnails->count();

        if ($search !== '') {
            $thumbnails = $thumbnails->filter(static function (string $thumbnail) use ($search): bool {
                return stripos($thumbnail, $search) !== false;
            });
        }

        $recordsFiltered = $thumbnails->count();

        $data = $thumbnails
            ->slice($start, $length)
            ->map(function (string $thumbnail): array {
                // Turn each filename into a row for the table
                $original = $this->findOriginalFileFromThumbnail($thumbnail);

                $original_url  = route('unused-media-thumbnail', [
                    'path' => $original,
                    'w'    => 100,
                    'h'    => 100,
                ]);
                $thumbnail_url = route('unused-media-thumbnail', [
                    'path' => $thumbnail,
                    'w'    => 100,
                    'h'    => 100,
                ]);

                $difference = $this->imageDiff($thumbnail, $original);

                $original_path  = substr($original, strlen(WT_DATA_DIR));
                $thumbnail_path = substr($thumbnail, strlen(WT_DATA_DIR));

                $media = $this->findMediaObjectsForMediaFile($original_path);

                $media_links = array_map(static function (Media $media): string {
                    return '<a href="' . e($media->url()) . '">' . $media->fullName() . '</a>';
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
            });

        return response([
            'draw'            => (int) $request->getQueryParams()['draw'],
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data->values()->all(),
        ]);
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
            $matches = glob($pattern, GLOB_NOSORT);
            if ($matches !== [] && is_file($matches[0])) {
                $original = $matches[0];
            }
        }

        return $original;
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
            ->join('media_file', static function (JoinClause $join): void {
                $join
                    ->on('media_file.m_file', '=', 'media.m_file')
                    ->on('media_file.m_id', '=', 'media.m_id');
            })
            ->join('gedcom_setting', 'media.m_file', '=', 'gedcom_setting.gedcom_id')
            ->where(new Expression('setting_value || multimedia_file_refn'), '=', $file)
            ->select(['media.*'])
            ->distinct()
            ->get()
            ->map(Media::rowMapper())
            ->all();
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

        $cache_dir  = Webtrees::DATA_DIR . 'cache/';

        if (!is_dir($cache_dir)) {
            mkdir($cache_dir);
        }

        $cache_file = $cache_dir . $sha1 . '.php';

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
}
