<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSaq;

/**
 * Class LocaleSaq - Samburu
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSaq extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Kisampur';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'KISAMPUR';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageSaq;
	}
}
