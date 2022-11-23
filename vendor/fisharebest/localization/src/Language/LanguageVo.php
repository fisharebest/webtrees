<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;

/**
 * Class LanguageVo - Representation of the Volapük language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageVo extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'vo';
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
