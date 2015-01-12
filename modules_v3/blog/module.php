<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2010 John Finlay
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
// module developed by David Drury
// NOTE: In a change from the gedcom_news and user_blog modules,
// if this module is called from an html block then editing is disabled

// Create tables, if not already present
try {
	WT_DB::updateSchema(__DIR__ . '/db_schema/', 'NB_SCHEMA_VERSION', 4);
} catch (PDOException $ex) {
	// The schema update scripts should never fail. If they do, there is no clean recovery.
	die($ex);
}

use WT\Auth;

require "blog_Core_Override.php";

class blog_WT_Module extends WT_Module implements WT_Module_Block {

	CONST VERSION = '1.6.3';
	CONST JS_FILE = 'blog.min.js';
	CONST LIMIT_NONE = 0;
	CONST LIMIT_BY_AGE = 1;
	CONST LIMIT_BY_NUMBER = 2;
	CONST LIMIT_BY_SCROLLBAR = 3;
	CONST SECS_PER_DAY = 86400;
	CONST DIALOG_WIDTH = 710;

	private static $add;
	private static $edit;
	private static $del;

	/** {@inheritdoc} */
	public function __construct() {
		parent::__construct();
		// Load any local user translations
		$lang_file = __DIR__ . '/language/' . WT_LOCALE;
		foreach (array('.mo', '.php', '.csv') as $extn) {
			$xlation_file = $lang_file . $extn;
			if (file_exists($xlation_file)) {
				WT_I18N::addTranslation(new Zend_Translate('gettext', $xlation_file, WT_LOCALE));
			}
		}
		self::$add  = WT_I18N::translate('Add an article');
		self::$edit = WT_I18N::translate('Edit this article');
		self::$del  = WT_I18N::translate('Delete this article');
	}

	/** {@inheritdoc} */
	public function getTitle() {
		return WT_I18N::translate('News and Journal');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return WT_I18N::translate('Site and personal journal');
	}

	/** {@inheritdoc} */
	public function modAction($mod_action) {
		$news            = new stdClass;
		$ctype           = WT_Filter::post('ctype', null, WT_Filter::get('ctype'));
		$isgedcom        = ($ctype === 'gedcom');
		$news->gedcom_id = $isgedcom ? WT_GED_ID : null;
		$news->user_id   = $isgedcom ? null : Auth::id();
		$news->body      = WT_Filter::post('body', null, '');
		$news->languages = WT_Filter::post('languages');
		$news->news_id   = WT_Filter::post('news_id', null, WT_Filter::get('news_id'));

		switch ($mod_action) {
			case 'add':
			case 'edit':
				$this->edit($news, $ctype);
				break;
			case 'save':
				if (WT_Filter::checkCsrf()) {
					$this->save($news);
				}
				break;
			case 'delete':
				if (WT_Filter::checkCsrf()) {
					if (blog_DB::deleteNews($news->news_id)) {
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

		// load the editor if available
		if (array_key_exists('markitup', WT_Module::getActiveModules())) {
			markitup_WT_Module::loadEditor($controller);
			$editor = WT_I18N::translate('%%s using %s', $WT_TREE->getPreference('FORMAT_TEXT') ? 'markdown' : 'HTML');
		} else {
			$editor = '%s';
		}

		$editable = $cfg === null && (($ctype === 'gedcom' && WT_USER_GEDCOM_ADMIN) || $ctype === 'user');
		if ($editable) {
			$title = "<span class='icon-admin' title='" . WT_I18N::translate('Configure') . "' onclick=\"modalDialog('block_edit.php?block_id={$block_id}', '{$this->getTitle()}');\"></span>";
		} else {
			$title = '';
		}
		$title .= $this->getTitle();

		// sort out the configuration parameters
		if ($cfg) {
			foreach (array('limit', 'flag') as $name) {
				$$name = array_key_exists($name, $cfg) ? (int)$cfg[$name] : null;
			}
		} elseif (WT_Filter::get('blog_archive', null, '') === 'yes') {
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
			".blog_text{width:100%;min-height:20em;margin:1em 0}" .
			".blog_article{padding:5px}" .
			".blog_article:not(:first-child){border-top:1px dotted #000}" .
			".blog_databox .label:after{content: ': '}" .
			".blog_iconbox{float:right}" .
			"[dir=rtl] .blog_iconbox {float:left;}" .
			".blog_iconbox a{display:inline-block;margin:0 2px}" .
			".blog_archive{display:none}";

		$controller
			->addExternalJavascript(WT_MODULES_DIR . $this->getname() . '/js-' . self::VERSION . '/' . self::JS_FILE)
			->addInlineJavascript("
				jQuery('head').append(\"<style type='text/css'>$styles</style>\");
				blog
					.getInstance()
					.init(" .
			            json_encode(
				            array(
					            'width'   => self::DIALOG_WIDTH,
						        'cmd'     => 'module.php?mod=' . $this->getName() . '&mod_action=',
						        'add'     => self::$add,
						        'edit'    => self::$edit,
						        'del'     => self::$del,
						        'title'   => $editor
				            )
				        ) .
				    ");"
			);

		// Get content
		$hitlimit = false;
		$news     = blog_DB::getNews($ctype, $editable);

		if (empty($news)) {
			$content = WT_Filter::formatText('**' . WT_I18N::translate('There are no articles available') . '**', $WT_TREE);
		} else {
			$content = '';
			$counter = 0;
			$archive = '';
			$stats = new WT_Stats(WT_GEDCOM);
			foreach ($news as $article) {
				if (!$hitlimit && (($limit === self::LIMIT_BY_NUMBER && ++$counter > $flag) ||
						($limit === self::LIMIT_BY_AGE && ceil((WT_TIMESTAMP - $article->updated) / self::SECS_PER_DAY) > $flag))
				) {
					$hitlimit = true;
					$view =  WT_I18N::translate('View archive');
					$hide =  WT_I18N::translate('Hide archive');
					$archive  = 'blog_archive';
				}

				$content .=
					sprintf('<div id="blog_%s" class="blog_article %s">%s<div class="blog_databox"><span class="label">%s</span>%s',
					 $article->news_id, $archive, $stats->embedTags(WT_Filter::formatText($article->body, $WT_TREE)),
					 WT_I18N::translate('Article last modified'), format_timestamp($article->updated));

				// Print Admin options for this News item
				if ($editable) {
					$content .=
						"<div class='blog_iconbox'>" .
							sprintf('<a class="editicon"   href="#edit"   title="%1$s"><span class="link_text">%1$s</span></a>', self::$edit) .
							sprintf('<a class="deleteicon" href="#delete" title="%1$s"><span class="link_text">%1$s</span></a>', self::$del) .
						"</div>";
				}
				$content .= "</div></div>";
			}
		}
		if ($editable) {
			$content .= sprintf('<a href="#add" title="%1$s">%1$s</a>', self::$add);
		}
		if ($hitlimit) {
			if ($editable) {
				$content .= " | ";
			}
			$content .= sprintf('<a href="#archive" title="%1$s" data-view="%1$s" data-hide="%2$s">%1$s</a>', $view, $hide);
			$content .= help_link('archive', $this->getName());
		}


		if ($template) {
			// $id, $class, $title & $content are needed by the template
			$id    = $ctype . '_' . $this->getName() . $block_id;
			$class = $this->getName() . '_block';
			if ($limit === self::LIMIT_BY_SCROLLBAR) {
				require WT_THEME_DIR . 'templates/block_small_temp.php';
			} else {
				require WT_THEME_DIR . 'templates/block_main_temp.php';
			}
		} else {
			return $content;
		}
		return true;
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
		if (WT_Filter::postBool('save')) {
			set_block_setting($block_id, 'limit', WT_Filter::postInteger('limit'));
			set_block_setting($block_id, 'flag', WT_Filter::postInteger('flag'));
			exit;
		}

		$limit = (int)get_block_setting($block_id, 'limit', self::LIMIT_NONE);
		$flag  = (int)get_block_setting($block_id, 'flag', 0);

		?>
		<tr>
			<td class="descriptionbox wrap width33">
				<label for="limit"><?php echo WT_I18N::translate('Limit display by') . ':</label>' . help_link('limit', $this->getName()); ?>
			</td>
			<td class="optionbox">
				<select id = "limit" name="limit">
					<option
						value='<?php echo self::LIMIT_NONE ?>' <?php echo $limit == self::LIMIT_NONE ? 'selected="selected"' : ''; ?>><?php echo WT_I18N::translate('No limit'); ?></option>
					<option
						value='<?php echo self::LIMIT_BY_AGE ?>' <?php echo $limit == self::LIMIT_BY_AGE ? 'selected="selected"' : ''; ?>><?php echo WT_I18N::translate('Age of item'); ?></option>
					<option
						value='<?php echo self::LIMIT_BY_NUMBER ?>' <?php echo $limit == self::LIMIT_BY_NUMBER ? 'selected="selected"' : ''; ?>><?php echo WT_I18N::translate('Number of items'); ?></option>
					<option
						value='<?php echo self::LIMIT_BY_SCROLLBAR ?>' <?php echo $limit == self::LIMIT_BY_SCROLLBAR ? 'selected="selected"' : ''; ?>><?php echo WT_I18N::translate('Add a scrollbar when block contents grow'); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="descriptionbox wrap width33">
				<label for="flag"><?php echo WT_I18N::translate('Limit') . ':</label>' . help_link('flag', $this->getName()); ?>
			</td>
			<td class="optionbox">
				<input id="flag" type="text" name="flag" size="4" maxlength="4" value="<?php echo $flag; ?>">
			</td>
		</tr>
		<?php
	}

	/**
	 * Function edit
	 * @param stdClass $news
	 *
	 * @param string $ctype
	 *
	 * @return void
	 */
	private function edit($news, $ctype) {

		$controller = new WT_Controller_Ajax();
		$controller->pageHeader();

		if (array_key_exists('markitup', WT_Module::getActiveModules())) {
			markitup_WT_Module::enableEditor($controller, '.blog_text');
		}
		if ($news->news_id) {
			$news_item = blog_DB::getNewsItem($news->news_id);
			$body      = $news_item->body;
			$languages = $news_item->languages;
		} else {
			$body      = '';
			$languages = implode(',', array_keys(WT_I18N::installed_languages()));
		}
		?>
		<form name="block" method="post"
		      action="module.php?mod=blog&mod_action=save&ctype=<?php echo $ctype; ?>"
		      onsubmit="return modalDialogSubmitAjax(this);">
			<input type="hidden" name="news_id" value="<?php echo $news->news_id; ?>">
			<?php echo WT_Filter::getCsrf(); ?>
			<?php printf("<textarea id='news_id%s' class='blog_text' name='body' rows='10'>%s</textarea>", $news->news_id, $body); ?>
			<div class="blog_iconbox">
				<?php
				// For $ctype=user assume items written in native language so no need for language checkboxes
				if ($ctype === 'gedcom') {
					echo blog_DB::edit_language_checkboxes('languages', $languages);
				}
				?>
			</div>
			<input type="submit" value="<?php echo WT_I18N::translate('save'); ?>">
		</form>
		<?php
	}

	/**
	 * Function save
	 * 
	 * @param stdClass $news
	 *
	 * @return void
	 */
	private function save($news) {
		$news->languages = implode(',', $news->languages);
		blog_DB::addNews($news);
	}

}
