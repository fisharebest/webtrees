<?php

namespace MyCustomNamespace;

use Fisharebest\Webtrees\Carbon;
use Fisharebest\Webtrees\Module\MinimalTheme;
use Fisharebest\Webtrees\Module\ModuleCustomInterface;
use Fisharebest\Webtrees\Module\ModuleCustomTrait;
use Fisharebest\Webtrees\View;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use function filemtime;
use function pathinfo;
use const PATHINFO_EXTENSION;

/**
 * Example theme.  Here we are extending an existing theme.
 * Instead, you could extend AbstractModule and implement ModuleThemeInterface directly.
 */
return new class extends MinimalTheme implements ModuleCustomInterface
{
    use ModuleCustomTrait;

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Custom theme';
    }

    /**
     * Bootstrap the module
     */
    public function boot(): void
    {
        // Register a namespace for our views.
        View::registerNamespace($this->name(), $this->resourceFolder() . 'views/');

        // Replace an existing view with our own version.
        View::registerCustomView('::chart-box', $this->name() . '::chart-box');
    }

    /**
     * Where does this module store its resources
     *
     * @return string
     */
    public function resourceFolder(): string
    {
        return __DIR__ . '/resources/';
    }

    /**
     * Add our own stylesheet to the existing stylesheets.
     *
     * @return array
     */
    public function stylesheets(): array
    {
        $stylesheets = parent::stylesheets();

        // NOTE - a future version of webtrees will allow the modules to be stored in a private folder.
        // Only files in the /public/ folder will be accessible via the webserver.
        // Since modules cannot copy their files to the /public/ folder, they need to provide them via a callback.
        $stylesheets[] = $this->assetUrl('css/theme.css');

        return $stylesheets;
    }

    /**
     * Create a URL for an asset.
     *
     * @param string $asset e.g. "css/theme.css" or "img/banner.png"
     *
     * @return string
     */
    public function assetUrl(string $asset): string
    {
        $file = $this->resourceFolder() . $asset;

        // Add the file's modification time to the URL, so we can set long expiry cache headers.
        $hash = filemtime($file);

        return route('module', [
            'module' => $this->name(),
            'action' => 'asset',
            'asset'  => $asset,
            'hash'   => $hash,
        ]);
    }

    /**
     * Serve a CSS/JS file.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getAssetAction(Request $request): Response
    {
        // The file being requested.  e.g. "css/theme.css"
        $asset = $request->get('asset');

        // Do not allow requests that try to access parent folders.
        if (Str::contains($asset, '..')) {
            throw new AccessDeniedHttpException($asset);
        }

        // Find the file for this asset.
        // Note that we could also generate CSS files using views/templates.
        // e.g. $file = view(....
        $file = $this->resourceFolder() . $asset;

        if (!file_exists($file)) {
            throw new NotFoundHttpException($file);
        }

        $content     = file_get_contents($file);
        $expiry_date = Carbon::now()->addYears(10);

        $extension = pathinfo($asset, PATHINFO_EXTENSION);

        $mime_types = [
            'css'  => 'text/css',
            'gif'  => 'image/gif',
            'js'   => 'application/javascript',
            'jpg'  => 'image/jpg',
            'jpeg' => 'image/jpg',
            'json' => 'application/json',
            'png'  => 'image/png',
            'txt'  => 'text/plain',
        ];

        $mime_type = $mime_types[$extension] ?? 'application/octet-stream';

        $headers = [
            'Content-Type' => $mime_type,
        ];

        $response = new Response($content, Response::HTTP_OK, $headers);

        return $response
            ->setExpires($expiry_date);
    }
};
