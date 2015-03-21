<?php namespace Fisharebest\Localization;

/**
 * Class LanguageBr - Representation of the Breton language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageBr extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'br';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryFr;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule2;
	}
}
