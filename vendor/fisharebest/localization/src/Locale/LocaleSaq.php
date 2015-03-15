<?php namespace Fisharebest\Localization;

/**
 * Class LocaleSaq - Samburu
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSaq extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Kisampur';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'KISAMPUR';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageSaq;
	}
}
