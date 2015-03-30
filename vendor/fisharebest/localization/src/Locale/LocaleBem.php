<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageBem;

/**
 * Class LocaleBem - Bemba
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleBem extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Ichibemba';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'ICHIBEMBA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageBem;
	}
}
