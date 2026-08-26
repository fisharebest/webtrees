<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\I18N\Languages;

use Fisharebest\Webtrees\Encodings\UTF8;
use Fisharebest\Webtrees\Enums\PluralRule;
use Fisharebest\Webtrees\Relationship;

abstract readonly class AbstractPortuguese extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::TwoFormsSingularForOne;

    protected const string DATE_ABOUT         = 'por volta de %s';
    protected const string DATE_AFTER         = 'depois de %s';
    protected const string DATE_BEFORE        = 'antes de %s';
    protected const string DATE_BETWEEN_AND   = 'entre %s e %s';
    protected const string DATE_CALCULATED    = 'calculado em %s';
    protected const string DATE_ESTIMATED     = 'estimado em %s';
    protected const string DATE_FROM          = 'de %s';
    protected const string DATE_FROM_TO       = 'de %s até %s';
    protected const string DATE_INTERPRETED   = 'interpretado em %s';
    protected const string DATE_TO            = 'até %s';
    protected const string ERA_BCE            = '%s' . UTF8::NO_BREAK_SPACE . 'AEC';
    protected const string ERA_CE             = '%s' . UTF8::NO_BREAK_SPACE . 'EC';
    protected const string LIST_SEPARATOR_AND = ' e ';

    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'Janeiro',
        'Fevereiro',
        'Março',
        'Abril',
        'Maio',
        'Junho',
        'Julho',
        'Agosto',
        'Setembro',
        'Outubro',
        'Novembro',
        'Dezembro',
    ];

    protected const array GREGORIAN_MONTHS_GENITIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_LOCATIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_NOMINATIVE = [
        '',
        'Tishrei',
        'Cheshvan',
        'Kislev',
        'Tevet',
        'Shevat',
        'Adar I',
        'Adar II',
        'Adar',
        'Nissan',
        'Iyar',
        'Sivan',
        'Tamuz',
        'Av',
        'Elul',
    ];

    protected const array JEWISH_MONTHS_GENITIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_LOCATIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_INSTRUMENTAL = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_NOMINATIVE = [
        '',
        'Vindimiário',
        'Brumário',
        'Frimário',
        'Nivoso',
        'Pluvioso',
        'Ventoso',
        'Germinal',
        'Florial',
        'Pradial',
        'Messidor',
        'Termidor',
        'Fructidor',
        'dias complementares',
    ];

    protected const array FRENCH_MONTHS_GENITIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_LOCATIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_INSTRUMENTAL = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_NOMINATIVE = [
        '',
        'Farvardin',
        'Ordibehesht',
        'Khordad',
        'Tir',
        'Mordad',
        'Shahrivar',
        'Mehr',
        'Aban',
        'Azar',
        'Dey',
        'Bahman',
        'Esfand',
    ];

    protected const array JALALI_MONTHS_GENITIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_LOCATIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_INSTRUMENTAL = self::JALALI_MONTHS_NOMINATIVE;

    /**
     * @return array<Relationship>
     */
        /** @return array{string, string} */
    private function da(string $s): array
    {
        return [$s, '%s da ' . $s];
    }

    /** @return array{string, string} */
    private function do(string $s): array
    {
        return [$s, '%s do ' . $s];
    }

    /** @return array{string, string} */
    private function great(int $n, string $suffix, string $article): array
    {
        return [
            ($n === 1 ? 'bis' : ($n === 2 ? 'tris' : ($n === 3 ? 'tetra' : $n . '°'))) . $suffix,
            '%s ' . $article . ($n === 1 ? 'bis' : ($n === 2 ? 'tris' : ($n === 3 ? 'tetra' : $n . '°'))) . $suffix,
        ];
    }

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        return [
            // Adopted
            Relationship::fixed(...$this->da('mãe adotiva'))->adoptive()->mother(),
            Relationship::fixed(...$this->do('pai adotivo'))->adoptive()->father(),
            Relationship::fixed(...$this->do('pai/mãe adotivo/a'))->adoptive()->parent(),
            Relationship::fixed(...$this->da('filha adotiva'))->adopted()->daughter(),
            Relationship::fixed(...$this->do('filho adotivo'))->adopted()->son(),
            Relationship::fixed(...$this->do('filho/a adotivo/a'))->adopted()->child(),
            // Parents
            Relationship::fixed(...$this->da('mãe'))->mother(),
            Relationship::fixed(...$this->do('pai'))->father(),
            Relationship::fixed(...$this->do('pai/mãe'))->parent(),
            // Children
            Relationship::fixed(...$this->da('filha'))->daughter(),
            Relationship::fixed(...$this->do('filho'))->son(),
            Relationship::fixed(...$this->do('filho/a'))->child(),
            // Siblings
            Relationship::fixed(...$this->da('irmã gêmea'))->multiple()->sister(),
            Relationship::fixed(...$this->do('irmão gêmeo'))->multiple()->brother(),
            Relationship::fixed(...$this->do('gêmeo/a'))->multiple()->sibling(),
            Relationship::fixed(...$this->da('irmã mais velha'))->older()->sister(),
            Relationship::fixed(...$this->do('irmão mais velho'))->older()->brother(),
            Relationship::fixed(...$this->do('irmão/ã mais velho/a'))->older()->sibling(),
            Relationship::fixed(...$this->da('irmã mais nova'))->younger()->sister(),
            Relationship::fixed(...$this->do('irmão mais novo'))->younger()->brother(),
            Relationship::fixed(...$this->do('irmão/ã mais novo/a'))->younger()->sibling(),
            Relationship::fixed(...$this->da('irmã'))->sister(),
            Relationship::fixed(...$this->do('irmão'))->brother(),
            Relationship::fixed(...$this->do('irmão/ã'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$this->da('meia-irmã'))->parent()->daughter(),
            Relationship::fixed(...$this->do('meio-irmão'))->parent()->son(),
            Relationship::fixed(...$this->do('meio/a-irmão/ã'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$this->da('madrasta'))->parent()->wife(),
            Relationship::fixed(...$this->do('padrasto'))->parent()->husband(),
            Relationship::fixed(...$this->do('padrasto/madrasta'))->parent()->married()->spouse(),
            Relationship::fixed(...$this->da('enteada'))->married()->spouse()->daughter(),
            Relationship::fixed(...$this->do('enteado'))->married()->spouse()->son(),
            Relationship::fixed(...$this->do('enteado/a'))->married()->spouse()->child(),
            // Partners
            Relationship::fixed(...$this->da('ex-esposa'))->divorced()->partner()->female(),
            Relationship::fixed(...$this->do('ex-marido'))->divorced()->partner()->male(),
            Relationship::fixed(...$this->do('ex-cônjuge'))->divorced()->partner(),
            Relationship::fixed(...$this->da('noiva'))->engaged()->partner()->female(),
            Relationship::fixed(...$this->do('noivo'))->engaged()->partner()->male(),
            Relationship::fixed(...$this->da('esposa'))->wife(),
            Relationship::fixed(...$this->do('marido'))->husband(),
            Relationship::fixed(...$this->do('cônjuge'))->spouse(),
            Relationship::fixed(...$this->do('companheiro/a'))->partner(),
            // In-laws
            Relationship::fixed(...$this->da('sogra'))->married()->spouse()->mother(),
            Relationship::fixed(...$this->do('sogro'))->married()->spouse()->father(),
            Relationship::fixed(...$this->do('sogro/a'))->married()->spouse()->parent(),
            Relationship::fixed(...$this->da('nora'))->child()->wife(),
            Relationship::fixed(...$this->do('genro'))->child()->husband(),
            Relationship::fixed(...$this->do('genro/nora'))->child()->married()->spouse(),
            Relationship::fixed(...$this->da('cunhada'))->spouse()->sister(),
            Relationship::fixed(...$this->do('cunhado'))->spouse()->brother(),
            Relationship::fixed(...$this->da('cunhada'))->sibling()->wife(),
            Relationship::fixed(...$this->do('cunhado'))->sibling()->husband(),
            // Grandparents
            Relationship::fixed(...$this->da('avó'))->parent()->mother(),
            Relationship::fixed(...$this->do('avô'))->parent()->father(),
            Relationship::fixed(...$this->do('avô/avó'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$this->da('neta'))->child()->daughter(),
            Relationship::fixed(...$this->do('neto'))->child()->son(),
            Relationship::fixed(...$this->do('neto/a'))->child()->child(),
            // Aunts and uncles
            Relationship::fixed(...$this->da('tia'))->parent()->sister(),
            Relationship::fixed(...$this->do('tio'))->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed(...$this->da('sobrinha'))->sibling()->daughter(),
            Relationship::fixed(...$this->do('sobrinho'))->sibling()->son(),
            Relationship::fixed(...$this->da('sobrinha'))->married()->spouse()->sibling()->daughter(),
            Relationship::fixed(...$this->do('sobrinho'))->married()->spouse()->sibling()->son(),
            // Cousins
            Relationship::fixed(...$this->da('prima'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$this->do('primo'))->parent()->sibling()->son(),
            // Dynamic relationships
            Relationship::dynamic(fn (int $n) => $this->great($n - 1, 'avó', 'da '))->ancestor()->female(),
            Relationship::dynamic(fn (int $n) => $this->great($n - 1, 'avô', 'do '))->ancestor()->male(),
            Relationship::dynamic(fn (int $n) => $this->great($n - 1, 'avô/avó', 'do '))->ancestor(),
            Relationship::dynamic(fn (int $n) => $this->great($n - 2, 'neta', 'da '))->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->great($n - 2, 'neto', 'do '))->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $this->great($n - 2, 'neto/a', 'do '))->descendant(),
            Relationship::dynamic(fn (int $n) => $this->great($n - 1, 'tia', 'da '))->ancestor()->sister(),
            Relationship::dynamic(fn (int $n) => $this->great($n - 1, 'tio', 'do '))->ancestor()->brother(),
            Relationship::dynamic(fn (int $n) => $this->great($n - 1, 'sobrinha', 'da '))->sibling()->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->great($n - 1, 'sobrinha', 'da '))->married()->spouse()->sibling()->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->great($n - 1, 'sobrinho', 'do '))->sibling()->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $this->great($n - 1, 'sobrinho', 'do '))->married()->spouse()->sibling()->descendant()->male(),
        ];
    }
}
