<?php
namespace Fisharebest\Webtrees;
use PDOException;

/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
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

/**
 * Class blog_WT_Module
 * @package Fisharebest\Webtrees
 */
class BlogModule extends Module implements ModuleBlockInterface {

	CONST LIMIT_NONE = 0;
	CONST LIMIT_BY_AGE = 1;
	CONST LIMIT_BY_NUMBER = 2;
	CONST LIMIT_BY_SCROLLBAR = 3;
	CONST SECS_PER_DAY = 86400;
	CONST DIALOG_WIDTH = 730;

	static private $active_language_tags = array();

	/** {@inheritdoc} */
	public function __construct($directory) {
		parent::__construct($directory);
		foreach (I18N::activeLocales() as $locale) {
			self::$active_language_tags[] = $locale->languageTag();
		}
		// Create tables, if not already present
		try {
			Database::updateSchema(__DIR__ . '/db_schema/', 'NB_SCHEMA_VERSION', 4);
		} catch (PDOException $ex) {
			// The schema update scripts should never fail.  If they do, there is no clean recovery.
			FlashMessages::addMessage($ex->getMessage(), 'danger');
			header('Location: ' . WT_BASE_URL . 'site-unavailable.php');
			throw $ex;
		}
	}

	/**
	 * static Function getActiveLanguageTagsAsString
	 * @return string
	 */
	static public function getActiveLanguageTagsAsString() {
		return implode(',', self::$active_language_tags);
	}

	/** {@inheritdoc} */
	public function getTitle() {
		return I18N::translate('News and Journal');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return I18N::translate('Site and personal journal');
	}

	/** {@inheritdoc} */
	public function modAction($mod_action) {
		global $WT_TREE;

		$news            = new \stdClass;
		$ctype           = Filter::get('ctype', null, 'gedcom');
		$isgedcom        = ($ctype === 'gedcom');
		$news->gedcom_id = $isgedcom ? $WT_TREE->getTreeId() : null;
		$news->user_id   = $isgedcom ? null : Auth::id();
		$news->body      = Filter::post('body', null, '');
		$news->languages = Filter::postArray('languages', null, self::$active_language_tags);
		$news->news_id   = Filter::postInteger('news_id', 1, PHP_INT_MAX, Filter::getInteger('news_id'));

		switch ($mod_action) {
			case 'add':
				//drop through
			case 'edit':
				$this->edit($news, $ctype);
				break;
			case 'save':
				if (Filter::checkCsrf()) {
					$this->save($news);
				}
				break;
			case 'delete':
				if (Filter::checkCsrf()) {
					if (blog_Database::deleteNews($news->news_id)) {
						header('HTTP/1.0 200 Success');
					} else {
						header('HTTP/1.0 500 Internal Server Error');
					}
				}
				break;
			default:
				header('HTTP/1.0 404 Not Found');
		}
	}

	/** {@inheritdoc} */
	public function getBlock($block_id, $template = true, $cfg = null) {
		global $WT_TREE, $controller, $ctype;

		$useMarkdown = $WT_TREE->getPreference('FORMAT_TEXT') === 'markdown';
		$editable = $cfg === null && (($ctype === 'gedcom' && Auth::isManager($WT_TREE)) || $ctype === 'user' && Auth::check());

		// load the editor if available
		if ($editable && module::getModuleByName('markitup')) {
			markitupModule::loadEditor($controller);
		}

		// sort out the configuration parameters
		if ($cfg) {
			foreach (array('limit', 'flag') as $name) {
				$$name = array_key_exists($name, $cfg) ? (int)$cfg[$name] : null;
			}
		} elseif (Filter::get('blog_archive', null, '') === 'yes') {
			$flag  = 0;
			$limit = self::LIMIT_NONE;
		} else {
			$flag  = (int)get_block_setting($block_id, "flag", 0);
			$limit = (int)get_block_setting($block_id, "limit", self::LIMIT_NONE);
		}

		//  Add some styles
		//	They replace the following classes used by the
		//	gedcom_news & user_blog modules
		//	news_box, news_title, news_date & journal_box

		$styles =
			".blog_article{border:1px solid #81a9cb;padding:0 5px;margin:1px}" .
			".blog_html{white-space: pre-wrap}" .
			".blog_controlbox{margin:5px 0}" .
			".blog_controlbox .label:after{content: ': '}" .
			".blog_controlbox a{margin:0 2px}" .
			".blog_controlbox .ui-icon{display:inline-block;vertical-align:bottom}" .
			".blog_iconbox{float:right}" .
			"[dir=rtl] .blog_iconbox {float:left;}" .
			".blog_iconbox a{display:inline-block}" .
			".blog_archive{display:none}";

		$controller
			->addInlineJavascript("
				jQuery('head').append(\"<style type='text/css'>$styles</style>\");
				String.prototype.truncate =
					function(n) {
						'use strict';
						var p = new RegExp ('^.{0,' + n + '}[\\S]*', 'g'),
						re = this.match (p),
						l = re[0].length;

						re = re[0].replace (/\s$/, '');
						if (l < this.length) {
							return re + ' \u2026';
						}
				};
				jQuery('.blog_block')
					.on('click', '.blog_controlbox a', function(e) {
						e.preventDefault();
						var self     = jQuery(this),
							ctype    = self.parents('.block').attr('id').split('_').shift(),
							article  = self.closest('.blog_article'),
							news_id  = article.length ? article.attr('id').split('_').pop() : null,
							txt;

						switch (jQuery(this).attr('href')) {
							case '#add':
								modalDialog('module.php?mod=" . $this->getName() . "&mod_action=edit&ctype=' + ctype,'" . I18N::translate('Add an article') . "','" . self::DIALOG_WIDTH . "');
								break;
							case '#edit':
								modalDialog('module.php?mod=" . $this->getName() . "&mod_action=edit&ctype=' + ctype + '&news_id=' + news_id,'" . I18N::translate('Edit this article') . "','" . self::DIALOG_WIDTH . "');
								break;
							case '#delete':
								txt = jQuery('#blog_' + news_id).text().truncate (60);
								if (confirm ('" . I18N::translate('Delete this article')  . "?\\n\\n' + txt)) {
									jQuery.post('module.php?mod=" . $this->getName() . "&mod_action=delete', {
										csrf: WT_CSRF_TOKEN,
										news_id: news_id
									})
									.done(function () {
										window.location.reload ();
									})
									.fail(function () {
										window.alert ('Deletion failed');
									});
								}
								break;
							case '#archive':
								jQuery('.blog_archive').slideToggle(function () {
									self.children('.ui-icon').toggleClass('ui-icon-triangle-1-e ui-icon-triangle-1-s');
								});
								break;
							default:
								//do nothing
						}
				});
			");

		// Get content
		$hitlimit = false;
		$news     = blog_Database::getNews($ctype, $editable);

		if (empty($news)) {
			$content = Filter::formatText('**' . I18N::translate('There are no articles available') . '**', $WT_TREE);
		} else {
			$content = '';
			$counter = 0;

			$article_class = 'blog_article';
			if(!$useMarkdown) {
				$article_class .= ' blog_html';
			}

			$stats = new Stats($WT_TREE);

			foreach ($news as $article) {
				if (!$hitlimit && (($limit === self::LIMIT_BY_NUMBER && ++$counter > $flag) ||
						($limit === self::LIMIT_BY_AGE && ceil((WT_TIMESTAMP - $article->updated) / self::SECS_PER_DAY) > $flag))
				) {
					$hitlimit = true;
					$article_class .= ' blog_archive';
				}

				// Can't use Filter::formatText() when then format isn't markdown because
				// the '<' & '>' characters are replaced by their filtered entities
				// thus rendering any embedded html useless

				// First process any embedded stats
				// then process markdown formatting if necessary
				$text = $stats->embedTags($article->body);
				if ($useMarkdown) {
					$text = Filter::formatText($text, $WT_TREE);
				}

				$content .=
					sprintf('<div id="blog_%s" class="%s" dir="auto">%s<div class="blog_controlbox" dir="auto"><span class="label">%s</span>%s',
					 $article->news_id, $article_class, $text, I18N::translate('Article last modified'), format_timestamp($article->updated));

				// Print Admin options for this News item
				if ($editable) {
					$content .=
						"<span class='blog_iconbox'>" .
							sprintf('<a class="editicon"   href="#edit"   title="%1$s"><span class="link_text">%1$s</span></a>', I18N::translate('Edit this article')) .
							sprintf('<a class="deleteicon" href="#delete" title="%1$s"><span class="link_text">%1$s</span></a>', I18N::translate('Delete this article')) .
						"</span>";
				}
				$content .= "</div></div>";
			}
		}

		if ($editable) {
			$title = "<span class='icon-admin' title='" . I18N::translate('Configure') . "' onclick=\"modalDialog('block_edit.php?block_id={$block_id}', '{$this->getTitle()}');\"></span>";
			$controls = sprintf('<a href="#add" title="%1$s"><span dir="auto" class="ui-icon ui-icon-document"></span>%1$s</a>', I18N::translate('Add an article'));
		} else {
			$title = '';
			$controls = '';
		}
		if ($hitlimit) {
			$controls .= sprintf('<a href="#archive" title="%1$s"><span dir="auto" class="ui-icon ui-icon-triangle-1-e"></span>%1$s</a>', I18N::translate('Archive'));
		}

		if ($controls) {
			$content .= "<div class='blog_controlbox'>" . $controls . "</div>";
		}
		$title .= $this->getTitle();
		$id    = $ctype . '_' . $this->getName() . '_' . $block_id;
		$class = $this->getName() . '_block';

		if ($template) {
			if ($limit === self::LIMIT_BY_SCROLLBAR) {
				$class .= ' small_inner_block';
			}
			return Theme::theme()->formatBlock($id, $title, $class, $content);
		} else {
			return $content;
		}
	}

	/** {@inheritdoc} */
	public function loadAjax() {
		return false;
	}

	/** {@inheritdoc} */
	public function isUserBlock() {
		return true;
	}

	/** {@inheritdoc} */
	public function isGedcomBlock() {
		return true;
	}

	/** {@inheritdoc} */
	public function configureBlock($block_id) {
		if (Filter::postBool('save')) {
			set_block_setting($block_id, 'limit', Filter::postInteger('limit'));
			set_block_setting($block_id, 'flag', Filter::postInteger('flag'));
			exit;
		}

		$limit = (int)get_block_setting($block_id, 'limit', self::LIMIT_NONE);
		$flag  = (int)get_block_setting($block_id, 'flag', 0);
		?>
		<tr>
			<td class="descriptionbox wrap width33">
				<label for="limit"><?php echo I18N::translate('Limit display by'); ?></label>
			</td>
			<td class="optionbox">
				<select id = "limit" name="limit">
					<option	value='<?php echo self::LIMIT_NONE ?>' <?php echo $limit == self::LIMIT_NONE ? 'selected' : ''; ?>><?php echo I18N::translate('No limit'); ?></option>
					<option	value='<?php echo self::LIMIT_BY_AGE ?>' <?php echo $limit == self::LIMIT_BY_AGE ? 'selected' : ''; ?>><?php echo I18N::translate('Age of articles'); ?></option>
					<option	value='<?php echo self::LIMIT_BY_NUMBER ?>' <?php echo $limit == self::LIMIT_BY_NUMBER ? 'selected' : ''; ?>><?php echo I18N::translate('Number of articles'); ?></option>
					<option	value='<?php echo self::LIMIT_BY_SCROLLBAR ?>' <?php echo $limit == self::LIMIT_BY_SCROLLBAR ? 'selected' : ''; ?>><?php echo I18N::translate('Add a scrollbar when block contents grow'); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="descriptionbox wrap width33">
				<label for="flag"><?php echo I18N::translate('Limit'); ?></label>
			</td>
			<td class="optionbox">
				<input id="flag" type="text" name="flag" size="4" maxlength="4" value="<?php echo $flag; ?>">
			</td>
		</tr>
		<?php
	}

	/**
	 * Function edit
	 * @param \stdClass $news
	 * @param string $ctype
	 */
	private function edit($news, $ctype) {

		$controller = new AjaxController();
		$controller->pageHeader();

		if (module::getModuleByName('markitup')) {
			markitupModule::enableEditor($controller, '.blog_edit');
		}
		if ($news->news_id) {
			$news_item = blog_Database::getNewsItem($news->news_id);
			$body      = $news_item->body;
			$languages = explode(',', $news_item->languages);
		} else {
			$body      = '';
			$languages = self::$active_language_tags;
		}
		?>
		<form name="block" method="post"
		      action="module.php?mod=blog&mod_action=save&ctype=<?php echo $ctype; ?>"
		      onsubmit="return modalDialogSubmitAjax(this);">
			<input type="hidden" name="news_id" value="<?php echo $news->news_id; ?>">
			<?php echo Filter::getCsrf(); ?>
			<?php printf("<textarea id='news_id%s' class='blog_edit' name='body' rows='10' cols='80'>%s</textarea>", $news->news_id, $body);
			if ($ctype === 'gedcom') { ?>
				<div class="blog_iconbox">
					<?php echo edit_language_checkboxes('languages', $languages); ?>
				</div>
			<?php }	?>
			<div>
				<input type="submit" value="<?php echo I18N::translate('save'); ?>">
			</div>
		</form>
		<?php
	}

	/**
	 * Function save
	 * @param \stdClass $news
	 */
	private function save($news) {
		$news->languages = implode(',', $news->languages);
		blog_Database::addNews($news);
	}

}
/**
 * Class blog_Database
 * @package Fisharebest\Webtrees
 */
class blog_Database {

	/**
	 * Adds a news item to the database
	 *
	 * @param \stdClass $news a news item
	 *
	 * @return void
	 */
	public static function addNews($news) {
		if ($news->news_id) {
			Database::prepare(
				"UPDATE `##news`" .
				" SET body=:body, languages=:languages" .
				" WHERE news_id=:news_id")
			        ->execute(array('body' => $news->body, 'languages' => $news->languages, 'news_id' => $news->news_id));
		} else {
			Database::prepare(
				"INSERT INTO `##news` (user_id, gedcom_id, body, languages)" .
				" VALUES (NULLIF(:user_id, ''), NULLIF(:gedcom_id, ''), :body, :languages)")
			        ->execute(array('user_id' => $news->user_id, 'gedcom_id' => $news->gedcom_id, 'body' => $news->body, 'languages' => $news->languages));
		}
	}

	/**
	 * Deletes a news item from the database
	 *
	 * @author John Finlay
	 *
	 * @param int $news_id
	 *
	 * @return bool
	 */
	public static function deleteNews($news_id) {
		return (bool)Database::prepare(
			"DELETE FROM `##news` WHERE news_id=:news_id"
		)
		                     ->execute(array('news_id' => $news_id));
	}

	/**
	 * Gets the news items for the given user or gedcom
	 *
	 * @param string $ctype
	 *
	 * @param bool $showAll
	 *
	 * @return array
	 */
	public static function getNews($ctype, $showAll = false) {
		global $WT_TREE;
		$id = ($ctype === 'gedcom') ? $WT_TREE->getTreeId() : Auth::id();

		/* Note FIND_IN_SET is not particularly efficient but it is unlikely
		   that there will ever be more than a few records in the news table */
		$filter = $showAll ? '' : " AND FIND_IN_SET('" . WT_LOCALE . "', languages)";

		return Database::prepare(
			"SELECT SQL_CACHE news_id, user_id, gedcom_id, languages, UNIX_TIMESTAMP(updated) AS updated, body" .
			" FROM `##news`" .
			" WHERE {$ctype}_id=:id{$filter}" .
			" ORDER BY updated DESC")
		               ->execute(array('id' => $id))
		               ->fetchAll();
	}

	/**
	 * Gets the news item for the given news id
	 *
	 * @param int $news_id
	 *
	 * @return object
	 */
	public static function getNewsItem($news_id) {
		return Database::prepare(
			"SELECT SQL_CACHE news_id, user_id, gedcom_id, languages, body" .
			" FROM `##news`" .
			" WHERE news_id=:news_id")
		               ->execute(array('news_id' => $news_id))
		               ->fetchOneRow();
	}

	/**
	 * static Function getNewsCount
	 *
	 * Gets the count of news items for the relevant type
	 * not currently used but could be used by library/WT/stats.php
	 *
	 * @param string $ctype
	 *
	 * @return int
	 */
	public static function getNewsCount($ctype) {

		$qry = Database::prepare(
			"SELECT COUNT(*) AS count" .
			" FROM `##news`" .
			" WHERE {$ctype}_id IS NOT NULL")
		               ->execute()
		               ->fetchOneRow();
		return (int)$qry->count;
	}

}

return new BlogModule(__DIR__);
