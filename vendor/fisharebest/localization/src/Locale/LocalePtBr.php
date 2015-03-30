<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\PluralRule\PluralRule2;
use Fisharebest\Localization\Territory\TerritoryBr;

/**
 * Class LocalePtBr - Brazilian Portuguese
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocalePtBr extends LocalePt {
	/** {@inheritdoc} */
	public function endonym() {
		return 'português do Brasil';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'PORTUGUES DO BRASIL';
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule2;
	}

	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryBr;
	}
}
