<?php

namespace MyCustomNamespace;

use Fisharebest\Webtrees\Module\AbstractModule;
use Fisharebest\Webtrees\Module\ModuleCustomInterface;
use Fisharebest\Webtrees\Module\ModuleCustomTrait;
use Fisharebest\Webtrees\Module\ModuleFooterInterface;
use Fisharebest\Webtrees\Module\ModuleFooterTrait;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\View;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Example footer with a link to a page of information.
 */
return new class extends AbstractModule implements ModuleCustomInterface, ModuleFooterInterface {
    use ModuleCustomTrait;
    use ModuleFooterTrait;

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Custom footer';
    }

    /**
     * Bootstrap the module
     */
    public function boot(): void
    {
        // Register a namespace for our views.
        View::registerNamespace($this->name(), $this->resourcesFolder() . 'views/');
    }

    /**
     * Where does this module store its resources
     *
     * @return string
     */
    public function resourcesFolder(): string
    {
        return __DIR__ . '/resources/';
    }

    /**
     * A footer, to be added at the bottom of every page.
     *
     * @param Tree|null $tree
     *
     * @return string
     */
    public function getFooter(?Tree $tree): string
    {
        $url = route('module', ['module' => $this->name(), 'action' => 'Page']);

        return view($this->name() . '::footer', ['url' => $url]);
    }

    /**
     * Generate the page that will be shown when we click the link in the footer.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getPageAction(ServerRequestInterface $request): ResponseInterface
    {
        return $this->viewResponse($this->name() . '::page', [
            'title' => $this->title(),
        ]);
    }
};
