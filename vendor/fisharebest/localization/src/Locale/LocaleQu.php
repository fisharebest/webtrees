<?php namespace Fisharebest\Localization;

/**
 * Class LocaleQu - Quechua
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleQu extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Runasimi';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'RUNASIMI';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageQu;
	}

	/** {@inheritdoc} */
	protected function percentFormat() {
		return '%s' . self::NBSP . self::PERCENT;
	}
}
