<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Script\ScriptEthi;
use Fisharebest\Localization\Territory\TerritoryEr;

/**
 * Class LanguageTif - Representation of the Tigre language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageTig extends AbstractLanguage implements LanguageInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'tig';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptEthi;
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryEr;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule1; //
	}
}
