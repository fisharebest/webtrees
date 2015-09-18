<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule0;
use Fisharebest\Localization\Script\ScriptJava;
use Fisharebest\Localization\Territory\TerritoryId;

/**
 * Class LanguageJv - Representation of the Javanese language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageJv extends AbstractLanguage implements LanguageInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'jv';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptJava;
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryId;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule0;
	}
}
