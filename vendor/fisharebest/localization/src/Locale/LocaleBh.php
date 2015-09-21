<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageBh;

/**
 * Class LocaleBh - Bihari
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleBh extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	protected function digitsGroup() {
		return 2;
	}

	/** {@inheritdoc} */
	public function endonym() {
		return 'Bihari';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'BIHARI';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageBh;
	}
}
