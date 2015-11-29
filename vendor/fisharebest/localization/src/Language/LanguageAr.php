<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule12;
use Fisharebest\Localization\Script\ScriptArab;

/**
 * Class LanguageAr - Representation of the Arabic language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageAr extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'ar';
	}

	public function defaultScript() {
		return new ScriptArab;
	}

	public function pluralRule() {
		return new PluralRule12;
	}
}
