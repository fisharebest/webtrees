<?php namespace Fisharebest\Localization;

/**
 * Class LanguageOc - Representation of the Occitan language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageOc extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'oc';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryFr;
	}
}
