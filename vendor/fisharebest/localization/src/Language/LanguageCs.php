<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule8;
use Fisharebest\Localization\Territory\TerritoryCz;

/**
 * Class LanguageCs - Representation of the Czech language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageCs extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'cs';
    }

    public function defaultTerritory()
    {
        return new TerritoryCz();
    }

    public function pluralRule()
    {
        return new PluralRule8();
    }
}
