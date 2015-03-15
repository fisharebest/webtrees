<?php namespace Fisharebest\Localization;

/**
 * Class LanguageAr - Representation of the Arabic language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageAr extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'ar';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptArab;
	}
}
