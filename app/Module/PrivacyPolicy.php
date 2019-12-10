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

use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function assert;
use function view;

/**
 * Class PrivacyPolicy - to comply with the GDPR and similar local laws.
 */
class PrivacyPolicy extends AbstractModule implements ModuleFooterInterface
{
    use ModuleFooterTrait;

    /** @var ModuleService */
    private $module_service;

    /** @var UserService */
    private $user_service;

    /**
     * Dependency injection.
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
     * How should this module be labelled on tabs, footers, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Privacy policy');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “Cookie warning” module */
        return I18N::translate('Show a privacy policy.');
    }

    /**
     * The default position for this footer.  It can be changed in the control panel.
     *
     * @return int
     */
    public function defaultFooterOrder(): int
    {
        return 4;
    }

    /**
     * A footer, to be added at the bottom of every page.
     *
     * @param ServerRequestInterface $request
     *
     * @return string
     */
    public function getFooter(ServerRequestInterface $request): string
    {
        $tree = $request->getAttribute('tree');

        if ($tree === null) {
            return '';
        }

        $user = $request->getAttribute('user');
        assert($user instanceof UserInterface);

        return view('modules/privacy-policy/footer', [
            'tree'           => $tree,
            'uses_analytics' => $this->analyticsModules($tree, $user)->isNotEmpty(),
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getPageAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $user = $request->getAttribute('user');
        assert($user instanceof UserInterface);

        $title = I18N::translate('Privacy policy');

        return $this->viewResponse('modules/privacy-policy/page', [
            'administrators' => $this->user_service->administrators(),
            'analytics'      => $this->analyticsModules($tree, $user),
            'title'          => $title,
            'tree'           => $tree,
        ]);
    }

    /**
     * @param Tree          $tree
     * @param UserInterface $user
     *
     * @return Collection<ModuleAnalyticsInterface>
     */
    protected function analyticsModules(Tree $tree, UserInterface $user): Collection
    {
        return $this->module_service
            ->findByComponent(ModuleAnalyticsInterface::class, $tree, $user)
            ->filter(static function (ModuleAnalyticsInterface $module): bool {
                return $module->isTracker();
            });
    }
}
