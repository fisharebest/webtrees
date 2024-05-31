<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule0;

/**
 * Class LanguageJbo - Representation of the Lojban language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageJbo extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'jbo';
    }

    public function pluralRule()
    {
        return new PluralRule0();
    }
}
