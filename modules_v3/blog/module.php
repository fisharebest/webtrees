<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2013 webtrees development team.
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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

// Create tables, if not already present
try {
	WT_DB::updateSchema(WT_ROOT . WT_MODULES_DIR . 'gedcom_news/db_schema/', 'NB_SCHEMA_VERSION', 3);
} catch (PDOException $ex) {
	// The schema update scripts should never fail.  If they do, there is no clean recovery.
	die($ex);
}

class blog_WT_Module extends WT_Module implements WT_Module_Block {

	CONST LIMIT_NONE = 0;
	CONST LIMIT_BY_AGE = 1;
	CONST LIMIT_BY_NUMBER = 2;
	CONST LIMIT_BY_SCROLLBAR = 3;

	// Extend class WT_Module
	public function getTitle() {
		return WT_I18N::translate('News and Journal');
	}

	// Extend class WT_Module
	public function getDescription() {
		return WT_I18N::translate('Site or personal journal');
	}

	public function modAction($mod_action) {
		$ctype				= WT_Filter::get('ctype');
		$isgedcom			= ($ctype === 'gedcom');
		$news['gedcom_id']	= $isgedcom ? WT_GED_ID : null;
		$news['user_id']	= $isgedcom ? null : WT_USER_ID;
		$news['id']			= WT_Filter::getInteger('news_id');
		$news['title']		= WT_Filter::post('title', null, '');
		$news['date']		= WT_Filter::postInteger('date', 0, PHP_INT_MAX, WT_TIMESTAMP);
		$news['text']		= WT_Filter::post('text', null, '');

		switch ($mod_action) {
			case 'add':
				$this->editForm($news);
				break;
			case 'edit':
				$this->editForm(getNewsItem($news['id']));
				break;
			case 'save':
				if (WT_Filter::checkCsrf()) {
					if (!$news['id']) {
						unset($news['id']);
					}
					addNews($news);
				}
				break;
// Because of csrf implementation, delete is a two stage function:
// firstly the user clicks the delete link, this pops up the confirmation form
// then on clicking the delete button on the form the deleteitem function is run
			case 'deleteform':
				$this->deleteForm(getNewsItem($news['id']));
				break;
			case 'deleteitem':
				if (WT_Filter::checkCsrf()) {
					deleteNews($news['id']);
				}
				header('Location: index.php?ctype=' . $ctype);
				break;
		}
	}

	// Implement class WT_Module_Block
	public function getBlock($block_id, $template = true, $cfg = null) {
		global $ctype;

		define('SECS_PER_DAY', 60 * 60 * 24);
		define('ISGEDCOM', $ctype === 'gedcom');
		define('SHOWCONTROLS', (ISGEDCOM && WT_USER_GEDCOM_ADMIN) || (!ISGEDCOM && WT_USER_ID));

		// sort out the configuration parameters
		if ($cfg) {
			foreach (array('limit', 'flag') as $name) {
				$$name = array_key_exists($name, $cfg) ? $cfg[$name] : null;
			}
		} elseif (WT_Filter::getBool('archive')) {
			$flag = 0;
			$limit = self::LIMIT_NONE;
		} else {
			$flag  = (int) get_block_setting($block_id, "flag", 0);
			$limit = (int) get_block_setting($block_id, "limit", self::LIMIT_NONE);
		}

		if (SHOWCONTROLS) {
			$title = '<span class="icon-admin" title="' . WT_I18N::translate('Configure') . '" onclick="modalDialog(\'block_edit.php?block_id=' . $block_id . '\', \'' . $this->getTitle() . '\');"></span>';
		} else {
			$title = '';
		}
		$title .= $this->getTitle();

		$content = '';
		$hitlimit = false;
		$usernews = ISGEDCOM ? getGedcomNews(WT_GED_ID) : getUserNews(WT_USER_ID); // should have one getNews function

		if (count($usernews) == 0) {
			$content .= "<div class='news_box'>" .
				"<div class='news_title'>" .
				WT_I18N::translate('There are no articles available') .
				"</div>" .
				"</div>";
		} else {
			$c = 0;
			foreach ($usernews as $news) {
				if (($limit === self::LIMIT_BY_NUMBER && ++$c > $flag) ||
					($limit === self::LIMIT_BY_AGE && (int) ((WT_TIMESTAMP - $news['date']) / SECS_PER_DAY) > $flag)) {
					$hitlimit = true;
					break;
				}
				if ($news["text"] == strip_tags($news["text"])) {
					$news["text"] = nl2br($news["text"], false);
				}
				$content .= "<div class='news_box'>" .
					"<div class='news_title'>" .
					WT_Filter::escapeHtml($news['title']) .
					"</div>" .
					"<div class='news_date'>" .
					format_timestamp($news['date']) .
					"</div>" .
					$news["text"];

				// Print Admin options for this News item
				if (SHOWCONTROLS) {
					$content .= '<hr>' .
						"<a href='index.php?ctype={$ctype}' onclick=\"modalDialog('module.php?mod=" . $this->getName() . "&amp;mod_action=edit&amp;news_id={$news['id']}&amp;ctype={$ctype}','" . WT_I18N::translate('Edit article') . "');return false;\">" . WT_I18N::translate('Edit') . "</a> | " .
						"<a href='index.php?ctype={$ctype}' onclick=\"modalDialog('module.php?mod=" . $this->getName() . "&amp;mod_action=deleteform&amp;news_id={$news['id']}','" . WT_I18N::translate('Delete article') . "');return false;\">" . WT_I18N::translate('Delete') . "</a>";
//						"<a href=\"module.php?mod=" . $this->getName() . "&amp;mod_action=delete&amp;news_id={$news['id']}&amp;ctype={$ctype}\" onclick=\"return confirm('" . WT_I18N::translate('Are you sure you want to delete this News entry?') . "');\">" . WT_I18N::translate('Delete') . "</a>";
				}
				$content .= "</div>";
			}
		}
		if (SHOWCONTROLS) {
			$content .= "<a href='index.php?ctype={$ctype}' onclick=\"modalDialog('module.php?mod=" . $this->getName() . "&amp;mod_action=add&amp;ctype={$ctype}','" . WT_I18N::translate('Add article') . "');return false;\">" . WT_I18N::translate('Add a new article') . "</a>";
			if ($hitlimit) {
				$content .= " | ";
			}
		}
		if ($hitlimit) {
			$content .= "<a href=\"index.php?archive=true&amp;ctype={$ctype}\">" . WT_I18N::translate('View archive') . "</a>";
			$content .= help_link('gedcom_news_archive');
		}

		if ($template) {
			// $id, $class, $title & $content are needed by the template
			$id = $this->getName() . $block_id;
			$class = $this->getName() . '_block';
			if ($limit === self::LIMIT_BY_SCROLLBAR) {
				require WT_THEME_DIR . 'templates/block_small_temp.php';
			} else {
				require WT_THEME_DIR . 'templates/block_main_temp.php';
			}
		} else {
			return $content;
		}
	}

	// Implement class WT_Module_Block
	public function loadAjax() {
		return false;
	}

	// Implement class WT_Module_Block
	public function isUserBlock() {
		return true;
	}

	// Implement class WT_Module_Block
	public function isGedcomBlock() {
		return true;
	}

	// Implement class WT_Module_Block
	public function configureBlock($block_id) {
		if (WT_Filter::postBool('save')) {
			set_block_setting($block_id, 'limit', WT_Filter::postInteger('limit'));
			set_block_setting($block_id, 'flag', WT_Filter::postInteger('flag'));
			exit;
		}

		$limit = get_block_setting($block_id, 'limit', self::LIMIT_NONE);
		$flag = get_block_setting($block_id, 'flag', 0);
		?>
		<td class="descriptionbox wrap width33">
			<?php echo WT_I18N::translate('Limit display by:') . help_link('gedcom_news_limit'); ?>
		</td>
		<td class="optionbox">
			<select name="limit">
				<option value='<?php echo self::LIMIT_NONE ?>' <?php echo $limit == self::LIMIT_NONE ? 'selected="selected"' : ''; ?>><?php echo WT_I18N::translate('No limit'); ?></option>
				<option value='<?php echo self::LIMIT_BY_AGE ?>' <?php echo $limit == self::LIMIT_BY_AGE ? 'selected="selected"' : ''; ?>><?php echo WT_I18N::translate('Age of item'); ?></option>
				<option value='<?php echo self::LIMIT_BY_NUMBER ?>' <?php echo $limit == self::LIMIT_BY_NUMBER ? 'selected="selected"' : ''; ?>><?php echo WT_I18N::translate('Number of items'); ?></option>
				<option value='<?php echo self::LIMIT_BY_SCROLLBAR ?>' <?php echo $limit == self::LIMIT_BY_SCROLLBAR ? 'selected="selected"' : ''; ?>><?php echo WT_I18N::translate('Add a scrollbar when block contents grow'); ?></option>
			</select>
		</td>
		</tr>
		<tr>
			<td class="descriptionbox wrap width33">
				<?php echo WT_I18N::translate('Limit:') . help_link('gedcom_news_flag'); ?>
			</td>
			<td class="optionbox">
				<input type="text" name="flag" size="4" maxlength="4" value="<?php echo $flag; ?>">
			</td>
		</tr>
		<?php
	}

	private function editForm($news) {
		$controller = new WT_Controller_Base();
		$controller
			->pageHeader();

		if (array_key_exists('ckeditor', WT_Module::getActiveModules())) {
			ckeditor_WT_Module::enableEditor($controller);
		}
		$ctype = $news['gedcom_id'] != null ? 'gedcom' : 'user';
		?>
		<form name="messageform" method="post" action="module.php?mod=<?php echo $this->getName(); ?>&amp;mod_action=save&amp;news_id=<?php echo $news['id']; ?>&amp;ctype=<?php echo $ctype; ?>" onsubmit="return modalDialogSubmitAjax(this);">
			<div class='givn-list'>
				<?php echo WT_Filter::getCsrf(); ?>
				<input type="hidden" name="date" value="<?php echo $news['date']; ?>">
				<label for="title" class="news_title"><?php echo WT_I18N::translate('Title'); ?>:</label>
				<input type="text" id="title" name="title" size="50" dir="auto" autofocus value="<?php echo $news['title']; ?>">
				<label for="text" class="news_title" style="display:block; padding:5px 0"><?php echo WT_I18N::translate('Entry Text:'); ?></label>
				<textarea id="text" name="text" class="html-edit" cols="80" rows="10" dir="auto"><?php echo WT_Filter::escapeHtml($news['text']); ?></textarea>
			</div>
			<input type="submit" value="<?php echo WT_I18N::translate('save'); ?>">
		</form>
		<?php
	}

	private function deleteForm($news) {
		Zend_Session::writeClose();
		$ctype = $news['gedcom_id'] != null ? 'gedcom' : 'user';
		?>
		<form name="msgdeleteform" method="post" action="module.php?mod=<?php echo $this->getName(); ?>&amp;mod_action=deleteitem&amp;news_id=<?php echo $news['id']; ?>&amp;ctype=<?php echo $ctype; ?>" onsubmit="return modalDialogSubmitAjax(this);">
			<?php echo WT_Filter::getCsrf(); ?>
			<fieldset>
				<div class='news_title'>
					<?php echo WT_I18N::translate('Are you sure you want to delete this article?'); ?>
				</div>
				<div>
					"<?php echo $news['title']?>"
				</div>
			</fieldset>
			<button type="submit" value="submit"><?php echo WT_I18N::translate('Delete'); ?></button>
		</form>
		<?php
	}


}
