<?php namespace Fisharebest\Localization;

/**
 * Class LanguageYi - Representation of the Yiddish language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageYi extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'yi';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptHebr;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule1;
	}
}
