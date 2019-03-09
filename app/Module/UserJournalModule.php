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
namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\Functions\FunctionsDate;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Theme;

/**
 * Class UserJournalModule
 */
class UserJournalModule extends AbstractModule implements ModuleBlockInterface
{
    /**
     * Create a new module.
     *
     * @param string $directory Where is this module installed
     */
    public function __construct($directory)
    {
        parent::__construct($directory);

        // Create/update the database tables.
        Database::updateSchema('\Fisharebest\Webtrees\Module\FamilyTreeNews\Schema', 'NB_SCHEMA_VERSION', 3);
    }

    /**
     * How should this module be labelled on tabs, menus, etc.?
     *
     * @return string
     */
    public function getTitle()
    {
        return /* I18N: Name of a module */ I18N::translate('Journal');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function getDescription()
    {
        return /* I18N: Description of the “Journal” module */ I18N::translate('A private area to record notes or keep a journal.');
    }

    /**
     * Generate the HTML content of this block.
     *
     * @param int      $block_id
     * @param bool     $template
     * @param string[] $cfg
     *
     * @return string
     */
    public function getBlock($block_id, $template = true, $cfg = array())
    {
        global $ctype, $WT_TREE;

        switch (Filter::get('action')) {
            case 'deletenews':
                $news_id = Filter::getInteger('news_id');
                if ($news_id) {
                    Database::prepare("DELETE FROM `##news` WHERE news_id = ?")->execute(array($news_id));
                }
                break;
        }

        $articles = Database::prepare(
            "SELECT news_id, user_id, gedcom_id, UNIX_TIMESTAMP(updated) + :offset AS updated, subject, body FROM `##news` WHERE user_id = :user_id ORDER BY updated DESC"
        )->execute(array(
            'offset'  => WT_TIMESTAMP_OFFSET,
            'user_id' => Auth::id(),
        ))->fetchAll();

        $id      = $this->getName() . $block_id;
        $class   = $this->getName() . '_block';
        $title   = $this->getTitle();
        $content = '';

        if (empty($articles)) {
            $content .= '<p>' . I18N::translate('You have not created any journal items.') . '</p>';
        }

        foreach ($articles as $article) {
            $content .= '<div class="journal_box">';
            $content .= '<div class="news_title">' . Filter::escapeHtml($article->subject) . '</div>';
            $content .= '<div class="news_date">' . FunctionsDate::formatTimestamp($article->updated) . '</div>';
            if ($article->body == strip_tags($article->body)) {
                $article->body = nl2br($article->body, false);
            }
            $content .= $article->body;
            $content .= '<a href="#" onclick="window.open(\'editnews.php?news_id=\'+' . $article->news_id . ', \'_blank\', indx_window_specs); return false;">' . I18N::translate('Edit') . '</a>';
            $content .= ' | ';
            $content .= '<a href="index.php?action=deletenews&amp;news_id=' . $article->news_id . '&amp;ctype=' . $ctype . '&amp;ged=' . $WT_TREE->getNameHtml() . '" onclick="return confirm(\'' . I18N::translate('Are you sure you want to delete “%s”?', Filter::escapeHtml($article->subject)) . "');\">" . I18N::translate('Delete') . '</a><br>';
            $content .= '</div><br>';
        }

        $content .= '<p><a href="#" onclick="window.open(\'editnews.php?user_id=' . Auth::id() . '\', \'_blank\', indx_window_specs); return false;">' . I18N::translate('Add a journal entry') . '</a></p>';

        if ($template) {
            return Theme::theme()->formatBlock($id, $title, $class, $content);
        } else {
            return $content;
        }
    }

    /** {@inheritdoc} */
    public function loadAjax()
    {
        return false;
    }

    /** {@inheritdoc} */
    public function isUserBlock()
    {
        return true;
    }

    /** {@inheritdoc} */
    public function isGedcomBlock()
    {
        return false;
    }

    /**
     * An HTML form to edit block settings
     *
     * @param int $block_id
     */
    public function configureBlock($block_id)
    {
    }
}
