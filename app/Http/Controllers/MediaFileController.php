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

namespace Fisharebest\Webtrees\Http\Controllers;

use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\MediaFile;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tree;
use Intervention\Image\Exception\NotReadableException;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Glide\Filesystem\FileNotFoundException;
use League\Glide\Server;
use League\Glide\ServerFactory;
use League\Glide\Signatures\Signature;
use League\Glide\Signatures\SignatureException;
use League\Glide\Signatures\SignatureFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

/**
 * Controller for the media page and displaying images.
 */
class MediaFileController extends AbstractBaseController {
	/**
	 * Download a non-image media file.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function mediaDownload(Request $request): Response {
		/** @var Tree $tree */
		$tree    = $request->attributes->get('tree');
		$xref    = $request->get('xref');
		$fact_id = $request->get('fact_id');
		$media   = Media::getInstance($xref, $tree);

		if ($media === null) {
			throw new NotFoundHttpException;
		}

		if (!$media->canShow()) {
			throw new AccessDeniedHttpException;
		}

		foreach ($media->mediaFiles() as $media_file) {
			if ($media_file->factId() === $fact_id) {
				if ($media_file->isExternal()) {
					return new RedirectResponse($media_file->filename());
				}

				if (!$media_file->isImage() && $media_file->fileExists()) {
					$data     = file_get_contents($media_file->getServerFilename());
					$response = new Response($data);

					$disposition = $response->headers->makeDisposition(
						ResponseHeaderBag::DISPOSITION_ATTACHMENT,
						basename($media_file->filename())
					);

					$response->headers->set('Content-Disposition', $disposition);
					$response->headers->set('Content-Type', $media_file->mimeType());

					return $response;
				}
			}
		}

		throw new NotFoundHttpException;
	}

	/**
	 * Show an image/thumbnail, with/without a watermark.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function mediaThumbnail(Request $request): Response {
		/** @var Tree $tree */
		$tree    = $request->attributes->get('tree');
		$xref    = $request->get('xref');
		$fact_id = $request->get('fact_id');
		$media   = Media::getInstance($xref, $tree);

		if ($media === null) {
			return $this->httpStatusAsImage(Response::HTTP_NOT_FOUND);
		}

		if (!$media->canShow()) {
			return $this->httpStatusAsImage(Response::HTTP_FORBIDDEN);
		}

		// @TODO handle SVG files
		foreach ($media->mediaFiles() as $media_file) {
			if ($media_file->factId() === $fact_id) {
				if ($media_file->isExternal()) {
					return new RedirectResponse($media_file->filename());
				} else if ($media_file->isImage()) {
					return $this->generateImage($media_file, $request->query->all());
				} else {
					return $this->fileExtensionAsImage($media_file->extension());
				}
			}
		}

		return $this->httpStatusAsImage(Response::HTTP_NOT_FOUND);
	}

	/**
	 * Generate a thumbnail for an unsed media file (i.e. not used by any media object).
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function unusedMediaThumbnail(Request $request): Response {
		$folder = $request->get('folder');
		$file   = $request->get('file');

		try {
			$server = $this->glideServer($folder);
			$path   = $server->makeImage($file, $request->query->all());
			$cache  = $server->getCache();

			return new Response($cache->read($path), Response::HTTP_OK, [
				'Content-Type'   => $cache->getMimeType($path),
				'Content-Length' => $cache->getSize($path),
				'Cache-Control'  => 'max-age=31536000, public',
				'Expires'        => date_create('+1 years')->format('D, d M Y H:i:s') . ' GMT',
			]);
		} catch (FileNotFoundException $ex) {
			return $this->httpStatusAsImage(Response::HTTP_NOT_FOUND);
		} catch (NotReadableException $ex) {
			return $this->httpStatusAsImage(Response::HTTP_INTERNAL_SERVER_ERROR);
		} catch (Throwable $ex) {
			return $this->httpStatusAsImage(Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}

	/**
	 * Generate a thumbnail image for a file.
	 *
	 * @param MediaFile $media_file
	 * @param array     $params
	 *
	 * @return Response
	 */
	private function generateImage(MediaFile $media_file, array $params): Response {
		try {
			// Validate HTTP signature
			$signature = $this->glideSignature();

			$signature->validateRequest(parse_url(WT_BASE_URL . 'index.php', PHP_URL_PATH), $params);

			$server = $this->glideServer($media_file->folder());
			$path   = $server->makeImage($media_file->filename(), $params);

			return new Response($server->getCache()->read($path), Response::HTTP_OK, [
				'Content-Type'   => $server->getCache()->getMimeType($path),
				'Content-Length' => $server->getCache()->getSize($path),
				'Cache-Control'  => 'max-age=31536000, public',
				'Expires'        => date_create('+1 years')->format('D, d M Y H:i:s') . ' GMT',
			]);
		} catch (SignatureException $ex) {
			return $this->httpStatusAsImage(Response::HTTP_FORBIDDEN);
		} catch (FileNotFoundException $ex) {
			return $this->httpStatusAsImage(Response::HTTP_NOT_FOUND);
		} catch (Throwable $ex) {
			Log::addErrorLog('Cannot create thumbnail ' . $ex->getMessage());

			return $this->httpStatusAsImage(Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}

	/**
	 * Create a glide server to generate files in the specified folder
	 *
	 * Caution: $media_folder may contain relative paths: ../../
	 *
	 * @param string $media_folder
	 *
	 * @return Server
	 */
	private function glideServer(string $media_folder): Server {
		$cache_folder     = new Filesystem(new Local(WT_DATA_DIR . 'thumbnail-cache/' . md5($media_folder)));
		$driver           = $this->graphicsDriver();
		$source_folder    = new Filesystem(new Local($media_folder));
		$watermark_folder = new Filesystem(new Local('resources/assets/img'));

		return ServerFactory::create([
			'cache'      => $cache_folder,
			'driver'     => $driver,
			'source'     => $source_folder,
			'watermarks' => $watermark_folder,
		]);
	}

	/**
	 * Generate a signature, to verify the request parameters.
	 *
	 * @return Signature
	 */
	private function glideSignature(): Signature {
		$glide_key = Site::getPreference('glide-key');
		$signature = SignatureFactory::create($glide_key);

		return $signature;
	}

	/**
	 * Which graphics driver should we use for glide/intervention?
	 *
	 * Prefer ImageMagick
	 *
	 * @return string
	 */
	private function graphicsDriver(): string {
		if (extension_loaded('imagick')) {
			$driver = 'imagick';
		} else {
			$driver = 'gd';
		}

		return $driver;
	}

	/**
	 * Send a dummy image, to replace one that could not be found or created.
	 *
	 * @param int $status HTTP status code
	 *
	 * @return Response
	 */
	private function httpStatusAsImage(int $status): Response {
		$svg = '<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100"><rect width="100" height="100" fill="#F88" /><text x="5" y="55" font-family="Verdana" font-size="35">' . $status . '</text></svg>';

		// We can't use the actual status code, as browser's won't show images with 4xx/5xx
		return new Response($svg, Response::HTTP_OK, [
			'Content-Type' => 'image/svg+xml'
		]);
	}

	/**
	 * Send a dummy image, to replace a non-image file.
	 *
	 * @param string $extension
	 *
	 * @return Response
	 */
	private function fileExtensionAsImage(string $extension): Response {
		$extension = '.' . strtolower($extension);

		$svg = '<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100"><rect width="100" height="100" fill="#88F" /><text x="5" y="60" font-family="Verdana" font-size="30">' . $extension . '</text></svg>';

		return new Response($svg, Response::HTTP_OK, [
			'Content-Type' => 'image/svg+xml'
		]);
	}
}
