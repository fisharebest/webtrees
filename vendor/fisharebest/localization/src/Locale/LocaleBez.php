<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageBez;

/**
 * Class LocaleBez - Bena
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleBez extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Hibena';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'HIBENA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageBez;
	}
}
