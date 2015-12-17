<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Script\ScriptHebr;

/**
 * Class LanguageYi - Representation of the Yiddish language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageYi extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'yi';
	}

	public function defaultScript() {
		return new ScriptHebr;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
