<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRuleOneTwoOther;
use Fisharebest\Localization\Script\ScriptCans;
use Fisharebest\Localization\Territory\TerritoryCa;

/**
 * Class LanguageEl - Representation of the Modern Greek (1453-) language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageIu extends AbstractLanguage implements LanguageInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'iu';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptCans;
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryCa;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRuleOneTwoOther;
	}
}
