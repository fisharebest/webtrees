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
namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Controller\PageController;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Tree;

/**
 * Class RelationshipsChartModule
 */
class RelationshipsChartModule extends AbstractModule implements ModuleConfigInterface, ModuleChartInterface
{
    /** It would be more correct to use PHP_INT_MAX, but this isn't friendly in URLs */
    const UNLIMITED_RECURSION = 99;

    /** By default new trees allow unlimited recursion */
    const DEFAULT_RECURSION = self::UNLIMITED_RECURSION;

    /** By default new trees search for all relationships (not via ancestors) */
    const DEFAULT_ANCESTORS = 0;

    /**
     * How should this module be labelled on tabs, menus, etc.?
     *
     * @return string
     */
    public function getTitle()
    {
        return /* I18N: Name of a module/chart */ I18N::translate('Relationships');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function getDescription()
    {
        return /* I18N: Description of the “RelationshipsChart” module */ I18N::translate('A chart displaying relationships between two individuals.');
    }

    /**
     * What is the default access level for this module?
     *
     * Some modules are aimed at admins or managers, and are not generally shown to users.
     *
     * @return int
     */
    public function defaultAccessLevel()
    {
        return Auth::PRIV_PRIVATE;
    }

    /**
     * Return a menu item for this chart.
     *
     * @param Individual $individual
     *
     * @return Menu|null
     */
    public function getChartMenu(Individual $individual)
    {
        $tree     = $individual->getTree();
        $gedcomid = $tree->getUserPreference(Auth::user(), 'gedcomid');

        if ($gedcomid) {
            return new Menu(
                I18N::translate('Relationship to me'),
                'relationship.php?pid1=' . $gedcomid . '&amp;pid2=' . $individual->getXref() . '&amp;ged=' . $tree->getNameUrl(),
                'menu-chart-relationship',
                array('rel' => 'nofollow')
            );
        } else {
            return new Menu(
                I18N::translate('Relationships'),
                'relationship.php?pid1=' . $individual->getXref() . '&amp;ged=' . $tree->getNameUrl(),
                'menu-chart-relationship',
                array('rel' => 'nofollow')
            );
        }
    }

    /**
     * Return a menu item for this chart - for use in individual boxes.
     *
     * @param Individual $individual
     *
     * @return Menu|null
     */
    public function getBoxChartMenu(Individual $individual)
    {
        return $this->getChartMenu($individual);
    }

    /**
     * This is a general purpose hook, allowing modules to respond to routes
     * of the form module.php?mod=FOO&mod_action=BAR
     *
     * @param string $mod_action
     */
    public function modAction($mod_action)
    {
        switch ($mod_action) {
            case 'admin':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $this->saveConfig();
                } else {
                    $this->editConfig();
                }
                break;
            default:
                http_response_code(404);
        }
    }

    /**
     * Possible options for the ancestors option
     */
    private function ancestorsOptions()
    {
        return array(
            0 => I18N::translate('Find any relationship'),
            1 => I18N::translate('Find relationships via ancestors'),
        );
    }

    /**
     * Possible options for the recursion option
     */
    private function recursionOptions()
    {
        return array(
            0                         => I18N::translate('none'),
            1                         => I18N::number(1),
            2                         => I18N::number(2),
            3                         => I18N::number(3),
            self::UNLIMITED_RECURSION => I18N::translate('unlimited'),
        );
    }

    /**
     * Display a form to edit configuration settings.
     */
    private function editConfig()
    {
        $controller = new PageController;
        $controller
            ->restrictAccess(Auth::isAdmin())
            ->setPageTitle(I18N::translate('Chart preferences') . ' — ' . $this->getTitle())
            ->pageHeader();

        ?>
        <ol class="breadcrumb small">
            <li><a href="admin.php"><?php echo I18N::translate('Control panel'); ?></a></li>
            <li><a href="admin_modules.php"><?php echo I18N::translate('Module administration'); ?></a></li>
            <li class="active"><?php echo $controller->getPageTitle(); ?></li>
        </ol>
        <h1><?php echo $controller->getPageTitle(); ?></h1>

        <p>
            <?php echo I18N::translate('Searching for all possible relationships can take a lot of time in complex trees.') ?>
        </p>

        <form method="post">
            <?php foreach (Tree::getAll() as $tree): ?>
                <h2><?php echo $tree->getTitleHtml() ?></h2>
                <div class="form-group">
                    <label class="control-label col-sm-3" for="relationship-ancestors-<?php echo $tree->getTreeId() ?>">
                        <?php echo /* I18N: Configuration option */I18N::translate('Relationships'); ?>
                    </label>
                    <div class="col-sm-9">
                        <?php echo FunctionsEdit::selectEditControl('relationship-ancestors-' . $tree->getTreeId(), $this->ancestorsOptions(), null, $tree->getPreference('RELATIONSHIP_ANCESTORS', self::DEFAULT_ANCESTORS), 'class="form-control"'); ?>
                    </div>
                </div>

                <fieldset class="form-group">
                    <legend class="control-label col-sm-3">
                        <?php echo /* I18N: Configuration option */I18N::translate('How much recursion to use when searching for relationships'); ?>
                    </legend>
                    <div class="col-sm-9">
                        <?php echo FunctionsEdit::radioButtons('relationship-recursion-' . $tree->getTreeId(), $this->recursionOptions(), $tree->getPreference('RELATIONSHIP_RECURSION', self::DEFAULT_RECURSION), 'class="radio-inline"'); ?>
                    </div>
                </fieldset>
            <?php endforeach; ?>

            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-9">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-check"></i>
                        <?php echo I18N::translate('save'); ?>
                    </button>
                </div>
            </div>
        </form>
        <?php
    }

    /**
     * Save updated configuration settings.
     */
    private function saveConfig()
    {
        if (Auth::isAdmin()) {
            foreach (Tree::getAll() as $tree) {
                $tree->setPreference('RELATIONSHIP_RECURSION', Filter::post('relationship-recursion-' . $tree->getTreeId()));
                $tree->setPreference('RELATIONSHIP_ANCESTORS', Filter::post('relationship-ancestors-' . $tree->getTreeId()));
            }

            FlashMessages::addMessage(I18N::translate('The preferences for the chart “%s” have been updated.', $this->getTitle()), 'success');
        }

        header('Location: ' . WT_BASE_URL . 'module.php?mod=' . $this->getName() . '&mod_action=admin');
    }

    /**
     * The URL to a page where the user can modify the configuration of this module.
     *
     * @return string
     */
    public function getConfigLink()
    {
        return 'module.php?mod=' . $this->getName() . '&amp;mod_action=admin';
    }
}
