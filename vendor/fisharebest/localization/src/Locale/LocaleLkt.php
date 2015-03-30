<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageLkt;

/**
 * Class LocaleLkt - Lakota
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleLkt extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Lakȟólʼiyapi';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'LAKHOLIYAPI';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageLkt;
	}
}
