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
use Fisharebest\Webtrees\Bootstrap4;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\Functions\FunctionsDate;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Stats;
use Fisharebest\Webtrees\Tree;

/**
 * Class HtmlBlockModule
 */
class HtmlBlockModule extends AbstractModule implements ModuleBlockInterface {
	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module */
			I18N::translate('HTML');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “HTML” module */
			I18N::translate('Add your own text and graphics.');
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
	public function getBlock($block_id, $template = true, $cfg = []): string {
		global $ctype, $WT_TREE;

		$title          = $this->getBlockSetting($block_id, 'title', '');
		$content        = $this->getBlockSetting($block_id, 'html', '');
		$gedcom         = $this->getBlockSetting($block_id, 'gedcom');
		$show_timestamp = $this->getBlockSetting($block_id, 'show_timestamp', '0');
		$languages      = $this->getBlockSetting($block_id, 'languages');

		// Only show this block for certain languages
		if ($languages && !in_array(WT_LOCALE, explode(',', $languages))) {
			return '';
		}

		/*
		 * Select GEDCOM
		 */
		switch ($gedcom) {
			case '__current__':
				$stats = new Stats($WT_TREE);
				break;
			case '__default__':
				$tree = Tree::findByName(Site::getPreference('DEFAULT_GEDCOM'));
				if ($tree) {
					$stats = new Stats($tree);
				} else {
					$stats = new Stats($WT_TREE);
				}
				break;
			default:
				$tree = Tree::findByName($gedcom);
				if ($tree) {
					$stats = new Stats($tree);
				} else {
					$stats = new Stats($WT_TREE);
				}
				break;
		}

		/*
		* Retrieve text, process embedded variables
		*/
		$title   = $stats->embedTags($title);
		$content = $stats->embedTags($content);

		if ($show_timestamp === '1') {
			$content .= '<br>' . FunctionsDate::formatTimestamp($this->getBlockSetting($block_id, 'timestamp', WT_TIMESTAMP) + WT_TIMESTAMP_OFFSET);
		}

		if ($template) {
			if ($ctype === 'gedcom' && Auth::isManager($WT_TREE) || $ctype === 'user' && Auth::check()) {
				$config_url = Html::url('block_edit.php', ['block_id' => $block_id, 'ged' => $WT_TREE->getName()]);
			} else {
				$config_url = '';
			}

			return view('blocks/template', [
				'block'      => str_replace('_', '-', $this->getName()),
				'id'         => $block_id,
				'config_url' => $config_url,
				'title'      => $title,
				'content'    => $content,
			]);
		} else {
			return $content;
		}
	}

	/** {@inheritdoc} */
	public function loadAjax(): bool {
		return false;
	}

	/** {@inheritdoc} */
	public function isUserBlock(): bool {
		return true;
	}

	/** {@inheritdoc} */
	public function isGedcomBlock(): bool {
		return true;
	}

	/**
	 * An HTML form to edit block settings
	 *
	 * @param int $block_id
	 *
	 * @return void
	 */
	public function configureBlock($block_id) {
		global $WT_TREE;

		if (Filter::postBool('save') && Filter::checkCsrf()) {
			$languages = Filter::postArray('lang');
			$this->setBlockSetting($block_id, 'gedcom', Filter::post('gedcom'));
			$this->setBlockSetting($block_id, 'title', Filter::post('title'));
			$this->setBlockSetting($block_id, 'html', Filter::post('html'));
			$this->setBlockSetting($block_id, 'show_timestamp', Filter::postBool('show_timestamp'));
			$this->setBlockSetting($block_id, 'timestamp', Filter::post('timestamp'));
			$this->setBlockSetting($block_id, 'languages', implode(',', $languages));
		}

		$templates = [
			I18N::translate('Keyword examples') => '#getAllTagsTable#',

			I18N::translate('Narrative description') => /* I18N: do not translate the #keywords# */ I18N::translate('This family tree was last updated on #gedcomUpdated#. There are #totalSurnames# surnames in this family tree. The earliest recorded event is the #firstEventType# of #firstEventName# in #firstEventYear#. The most recent event is the #lastEventType# of #lastEventName# in #lastEventYear#.<br><br>If you have any comments or feedback please contact #contactWebmaster#.'),

			I18N::translate('Statistics') => '<div class="gedcom_stats">
				<span style="font-weight: bold;"><a href="index.php?command=gedcom">#gedcomTitle#</a></span><br>
				' . I18N::translate('This family tree was last updated on %s.', '#gedcomUpdated#') . '
					<div class="row">
						<div class="col col-sm-4">
							<table class="table wt-facts-table">
								<tr>
									<th scope="row">' . I18N::translate('Individuals') . '</th>
									<td>#totalIndividuals#</td>
								</tr>
								<tr>
									<th scope="row">' . I18N::translate('Males') . '</th>
									<td>#totalSexMales#<br>#totalSexMalesPercentage#</td>
								</tr>
								<tr>
									<th scope="row">' . I18N::translate('Females') . '</th>
									<td>#totalSexFemales#<br>#totalSexFemalesPercentage#</td>
								</tr>
								<tr>
									<th scope="row">' . I18N::translate('Total surnames') . '</th>
									<td>#totalSurnames#</td>
								</tr>
								<tr>
									<th scope="row">' . I18N::translate('Families') . '</th>
									<td>#totalFamilies#</td>
								</tr>
								<tr>
									<th scope="row">' . I18N::translate('Sources') . '</th>
									<td>#totalSources#</td>
								</tr>
								<tr>
									<th scope="row">' . I18N::translate('Media objects') . '</th>
									<td>#totalMedia#</td>
								</tr>
								<tr>
									<th scope="row">' . I18N::translate('Repositories') . '</th>
									<td>#totalRepositories#</td>
								</tr>
								<tr>
									<th scope="row">' . I18N::translate('Events') . '</th>
									<td>#totalEvents#</td>
								</tr>
								<tr>
									<th scope="row">' . I18N::translate('Users') . '</th>
									<td>#totalUsers#</td>
								</tr>
							</table>
						</div>

						<div class="col col-sm-8">
							<table class="table wt-facts-table">
								<tr>
									<th scope="row">' . I18N::translate('Earliest birth') . '</th>
									<td>#firstBirth#</td>
								</tr>
								<tr>
									<th scope="row">' . I18N::translate('Latest birth') . '</th>
									<td>#lastBirth#</td>
								</tr>
								<tr>
									<th scope="row">' . I18N::translate('Earliest death') . '</th>
									<td>#firstDeath#</td>
								</tr>
								<tr>
									<th scope="row">' . I18N::translate('Latest death') . '</th>
									<td>#lastDeath#</td>
								</tr>
								<tr>
									<th scope="row">' . I18N::translate('Individual who lived the longest') . '</th>
									<td>#longestLife#</td>
								</tr>
								<tr>
									<th scope="row">' . I18N::translate('Average age at death') . '</th>
									<td>#averageLifespan#</td>
								</tr>
								<tr>
									<th scope="row">' . I18N::translate('Family with the most children') . '</th>
									<td>#largestFamilySize#<br>#largestFamily#</td>
								</tr>
								<tr>
									<th scope="row">' . I18N::translate('Average number of children per family') . '</th>
									<td>#averageChildren#</td>
								</tr>
							</table>
						</div>
					</div> 
					<br>
					<span style="font-weight: bold;">' . I18N::translate('Most common surnames') . '</span>
					<br>
					#commonSurnames#
				</div>',
		];

		$title          = $this->getBlockSetting($block_id, 'title', '');
		$html           = $this->getBlockSetting($block_id, 'html', '');
		$gedcom         = $this->getBlockSetting($block_id, 'gedcom', '__current__');
		$show_timestamp = $this->getBlockSetting($block_id, 'show_timestamp', '0');
		$languages      = explode(',', $this->getBlockSetting($block_id, 'languages'));

		?>
		<div class="row form-group">
			<label class="col-sm-3 col-form-label" for="title">
				<?= I18N::translate('Title') ?>
			</label>
			<div class="col-sm-9">
				<input class="form-control" type="text" id="title" name="title" value="<?= e($title) ?>">
			</div>
		</div>

		<div class="row form-group">
			<label class="col-sm-3 col-form-label" for="template">
				<?= I18N::translate('Templates') ?>
			</label>
			<div class="col-sm-9">
				<?= Bootstrap4::select([$html => I18N::translate('Custom')] + array_flip($templates), '', ['onchange' => 'this.form.html.value=this.options[this.selectedIndex].value; CKEDITOR.instances.html.setData(document.block.html.value);', 'id' => 'template']) ?>
				<p class="small text-muted">
					<?= I18N::translate('To assist you in getting started with this block, we have created several standard templates. When you select one of these templates, the text area will contain a copy that you can then alter to suit your site’s requirements.') ?>
				</p>
			</div>
		</div>

		<div class="row form-group">
			<label class="col-sm-3 col-form-label" for="gedcom">
				<?= I18N::translate('Family tree') ?>
			</label>
			<div class="col-sm-9">
				<?= Bootstrap4::select(['__current__' => I18N::translate('Current'), '__default__' => I18N::translate('Default')] + Tree::getNameList(), $gedcom, ['id' => 'gedcom', 'name' => 'gedcom']) ?>
			</div>
		</div>

		<div class="row form-group">
			<label class="col-sm-3 col-form-label" for="html">
				<?= I18N::translate('Content') ?>
			</label>
			<div class="col-sm-9">
				<p>
					<?= I18N::translate('As well as using the toolbar to apply HTML formatting, you can insert database fields which are updated automatically. These special fields are marked with <b>#</b> characters. For example <b>#totalFamilies#</b> will be replaced with the actual number of families in the database. Advanced users may wish to apply CSS classes to their text, so that the formatting matches the currently selected theme.') ?>
				</p>
			</div>
		</div>

		<div class="row form-group">
			<textarea name="html" id="html" class="html-edit" rows="10"><?= e($html) ?></textarea>
		</div>

		<fieldset class="form-group">
			<div class="row">
				<legend class="form-control-legend col-sm-3">
					<?= I18N::translate('Show the date and time of update') ?>
				</legend>
				<div class="col-sm-9">
					<?= Bootstrap4::radioButtons('show_timestamp', FunctionsEdit::optionsNoYes(), $show_timestamp, true) ?>
				</div>
			</div>
		</fieldset>

		<fieldset class="form-group">
			<div class="row">
				<legend class="form-control-legend col-sm-3">
					<?= I18N::translate('Show this block for which languages') ?>
				</legend>
				<div class="col-sm-9">
					<?= FunctionsEdit::editLanguageCheckboxes('lang', $languages) ?>
				</div>
			</div>
		</fieldset>

		<?php
	}
}
