<?php namespace Fisharebest\Localization;

/**
 * Class LanguageFy - Representation of the Western Frisian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageFy extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'fy';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryNl;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule1;
	}
}
