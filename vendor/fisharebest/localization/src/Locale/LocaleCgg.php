<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageCgg;

/**
 * Class LocaleCgg - Chiga
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleCgg extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Rukiga';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'RUKIGA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageCgg;
	}
}
