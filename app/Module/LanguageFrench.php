<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Localization\Locale\LocaleFr;
use Fisharebest\Localization\Locale\LocaleInterface;
use Fisharebest\Webtrees\Relationship;

/**
 * Class LanguageFrench.
 */
class LanguageFrench extends AbstractModule implements ModuleLanguageInterface
{
    use ModuleLanguageTrait;

    protected const SYMMETRIC_COUSINS = [
        1 => [
            'F' => ['cousine germaine', '$s de la cousine germaine'],
            'M' => ['cousin germain', '$s du cousin germain'],
            'U' => ['cousin germain', '%s du cousin germain' ]
        ],
        2 => [
            'F' => ['cousine issue de germain', '$s de la cousine issue de germain'],
            'M' => ['cousin issu de germain', '$s du cousin issu de germain'],
            'U' => ['cousin issu de germain', '%s du cousin issu de germain' ]
        ]
    ];

    protected const ASYMMETRIC_COUSINS = [
        1 => [
            'F' => ['down', 'petite-', 'cousine', 'de la ', 'de la '],
            'M' => ['down', 'petit-', 'cousin', 'du ', 'du '],
            'U' => ['down', 'petit-', 'cousin', 'du ', 'du ']
        ],
        -1 => [
            'F' => ['up', 'grand-', 'cousine', 'de la ', 'de la '],
            'M' => ['up', 'grand-', 'cousin', 'du ', 'du '],
            'U' => ['up', 'grand-', 'cousin', 'du ', 'du ']
        ],
    ];

    /**
     * @return LocaleInterface
     */
    public function locale(): LocaleInterface
    {
        return new LocaleFr();
    }

    /**
     * Pour les traducteurs français, certaines configurations peuvent avoir plusieurs traduction françaises possibles,
     * ou aucune. Voici les choix qui ont été faits (mais complètement ouvert à discussion):
     *
     * - je n'ai aucune intention de rentrer dans le débat de l'écriture inclusive, mais malheureusement un choix doit
     *   être fait: lorsque nécessaire dans les choix des articles ou accords, je m'en suis tenu à la recommandation de
     *   l'Académie Française d'utiliser la forme non marquée (et donc le masculin) pour le genre neutre.
     * - dans le cas de frère/sœur jumeau, j'évite le problème en utiliseant le substantif `jumeau` lorsque le sexe
     *   n'est pas connu, alors que j'utilise la structure `frère jumeau`/`sœur jum elle` lorsque le sexe est connu.
     * - `conjoint` a été choisi pour un couple non marié (`époux`/`épouse` lorsque les conjoints sont mariés).
     *   Une alternative est `partenaire`, mais `conjoint` est le terme déjà utilisé dans les traductions françaises.
     * - la notion de `foster` (qui peut traduire plusieurs réalités différentes en français) a été traduite dans le
     *   cadre de la `famille d'accueil`. Les suggestions sont les bienvenues.
     * - La situation des enfants dans les familles recomposées a été traduites:
     * - `frère`/`sœur` pour les enfants dont les deux parents sont les mêmes
     * - `demi-frère`/`demi-sœur` pour les enfants qui ont un parent en commun
     * - `quasi-frère`/`quasi-sœur` pour les enfants qui ne partagent aucun parent en commun, mais dont les parents
     *   sont en couple
     * - la notion d'âge entre frères/sœurs a été traduite par `grand frère`/`petit frère`, plutôt que des variants sur
     *   `frère aîné`/`frère cadet` ou `frère plus âgé`/`frère plus jeune`
     * - De manière arbitraire, au delà de deux `arrière-`, la forme est raccourcie par `arrière-(xN)-` avec N décrivant
     *   le nombre de degré. Techniquement, en français, il n'existe pas de forme raccourcie, mais je ne pense pas que
     *   ce soit une bonne idée de multiplier les `arrière-`. On pourrait utiliser les termes `quadrisaïeul` /
     *   `quinquisaïeul`  /`sextaïeul` / `septaïeul` /... mais ils me semblent assez peu usités.
     * - Pour les cousins, c'est la description selon le droit canon qui a été choisie (principalement car elle donne
     *   une meilleure visibilité de la distance à l'ancêtre commun que la description en droit civil), donc:
     * - l'enfant d'un oncle/tante est un `cousin germain`/`cousine germaine` (= cousins au 1er degré)
     * - les enfants de cousins germains sont des `cousins issus de germain` (= cousins au 2e degré)
     * - pour les enfants des cousins issus de germains, et ainsi de suite, la relation est décrite suivant le nombre
     *   de degré séparant les cousins de l'ancêtre commun:
     * - en cas de symétrie des chemins, ils sont dits `cousins au N-ème degré`
     * - en cas d'asymétrie des chemins, ils sont dit  `cousins du N-ème au M-ème degré`
     * - de plus, les notions de `grand-cousin` et `petit-cousin` ont été implémentées comme suit:
     * - un `(arrière-)grand-cousin` est l'enfant d'un `(arrière-)grand-oncle`/`grand-tante` (= cousin du 1er au N-ème degré)
     * - un `(arrière-)petit-cousin` est un `(arrière-)petit-neveu`/`petite-nièce` d'un parent (= cousin du Ner au 1er degré)
     *
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        $genitive = fn (string $s, string $genitive_link): array => [$s, '%s ' . $genitive_link . $s];

        $great = fn (int $n, string $suffix, string $genitive_link): array => $genitive(
            ($n > 2 ? 'arrière-(x' . $n . ')-' : str_repeat('arrière-', max($n, 0))) . $suffix,
            $n === 0 ? $genitive_link : 'de l’'
        );

        $compoundgreat =
            fn (int $n, string $first_level, string $suffix, string $genitive_none, string $genitive_first): array =>
                $great($n - 1, ($n > 0 ? $first_level : '') . $suffix, $n === 0 ? $genitive_none : $genitive_first);

        $symmetricCousin = fn (int $n, string $sex): array => self::SYMMETRIC_COUSINS[$n][$sex] ?? $genitive(
            $sex === 'F' ? 'cousine au ' . $n . '<sup>e</sup> degré' : 'cousin au ' . $n . '<sup>e</sup> degré',
            $sex === 'F'  ? 'de la ' : 'du '
        );

        $asymmetricCousin =
            static function (int $up, int $down, string $sex) use ($symmetricCousin, $compoundgreat, $genitive): array {
                if ($up === $down) {
                    return $symmetricCousin($up, $sex);
                }
                $fixed = self::ASYMMETRIC_COUSINS[$up][$sex] ?? self::ASYMMETRIC_COUSINS[-$down][$sex] ?? null;
                if ($fixed !== null) {
                    $fixed[0] = $fixed[0] === 'up' ? $up - 1 : $down - 1;
                    return $compoundgreat(...$fixed);
                }
                return $genitive(
                    $sex === 'F' ?
                        'cousine du ' . $down . '<sup>e</sup> au ' . $up . '<sup>e</sup> degré' :
                        'cousin du ' . $down . '<sup>e</sup> au ' . $up . '<sup>e</sup> degré',
                    $sex === 'F'  ? 'de la ' : 'du '
                );
            };

        return [
            // Adopted
            Relationship::fixed('mère adoptive', '%s de la mère adoptive')->adoptive()->mother(),
            Relationship::fixed('père adoptif', '%s du père adoptif')->adoptive()->father(),
            Relationship::fixed('parent adoptif', '%s du parent adoptif')->adoptive()->parent(),
            Relationship::fixed('fille adoptive', '%s de la fille adoptive')->adopted()->daughter(),
            Relationship::fixed('fils adoptif', '%s du fils adoptif')->adopted()->son(),
            Relationship::fixed('enfant adoptif', '%s de l’enfant adoptif')->adopted()->child(),
            // Fostered
            Relationship::fixed('mère d’accueil', '%s de la mère d’acceuil')->fostering()->mother(),
            Relationship::fixed('père d’accueil', '%s du père d’acceuil')->fostering()->father(),
            Relationship::fixed('parent d’accueil', '%s du parent d’acceuil')->fostering()->parent(),
            Relationship::fixed('fille accueillie', '%s de la fille accueillie')->fostered()->daughter(),
            Relationship::fixed('fils accueilli', '%s du fils accueilli')->fostered()->son(),
            Relationship::fixed('enfant accueilli', '%s de l’enfant accueilli')->fostered()->child(),
            // Parents
            Relationship::fixed('mère', '%s de la mère')->mother(),
            Relationship::fixed('père', '%s du père')->father(),
            Relationship::fixed('parent', '%s du parent')->parent(),
            // Children
            Relationship::fixed('fille', '%s de la fille')->daughter(),
            Relationship::fixed('fils', '%s du fils')->son(),
            Relationship::fixed('enfant', '%s de l’enfant')->child(),
            // Siblings
            Relationship::fixed('sœur jumelle', '%s de la sœur jumelle')->twin()->sister(),
            Relationship::fixed('frère jumeau', '%s du frère jumeau')->twin()->brother(),
            Relationship::fixed('jumeau', '%s du jumeau')->twin()->sibling(),
            Relationship::fixed('grande sœur', '%s de la grande sœur')->older()->sister(),
            Relationship::fixed('grand frère', '%s du grand frère')->older()->brother(),
            Relationship::fixed('grand frère/sœur', '%s du grand frère/sœur')->older()->sibling(),
            Relationship::fixed('petite sœur', '%s de la petite sœur')->younger()->sister(),
            Relationship::fixed('petit frère', '%s du petit-frère')->younger()->brother(),
            Relationship::fixed('petit frère/sœur', '%s du petit frère/sœur')->younger()->sibling(),
            Relationship::fixed('sœur', '%s de la sœur')->sister(),
            Relationship::fixed('frère', '%s du frère')->brother(),
            Relationship::fixed('frère/sœur', '%s du frère/sœur')->sibling(),
            // Half-family
            Relationship::fixed('demi-sœur', '%s de la demi-sœur')->parent()->daughter(),
            Relationship::fixed('demi-frère', '%s du demi-frère')->parent()->son(),
            Relationship::fixed('demi-frère/sœur', '%s du demi-frère/sœur')->parent()->child(),
            // Stepfamily
            Relationship::fixed('belle-mère', '%s de la belle-mère')->parent()->wife(),
            Relationship::fixed('beau-père', '%s du beau-père')->parent()->husband(),
            Relationship::fixed('beau-parent', '%s du beau-parent')->parent()->married()->spouse(),
            Relationship::fixed('belle-fille', '%s de la belle-fille')->married()->spouse()->daughter(),
            Relationship::fixed('beau-fils', '%s du beau-fils')->married()->spouse()->son(),
            Relationship::fixed('beau-fils/fille', '%s du beau-fils/fille')->married()->spouse()->child(),
            Relationship::fixed('quasi-sœur', '%s de la quasi-sœur')->parent()->spouse()->daughter(),
            Relationship::fixed('quasi-frère', '%s du quasi-frère')->parent()->spouse()->son(),
            Relationship::fixed('quasi-frère/sœur', '%s du quasi-frère/sœur')->parent()->spouse()->child(),
            // Partners
            Relationship::fixed('ex-épouse', '%s de l’ex-épouse')->divorced()->partner()->female(),
            Relationship::fixed('ex-époux', '%s de l’ex-époux')->divorced()->partner()->male(),
            Relationship::fixed('ex-conjoint', '%s de l’ex-conjoint')->divorced()->partner(),
            Relationship::fixed('fiancée', '%s de la fiancée')->engaged()->partner()->female(),
            Relationship::fixed('fiancé', '%s du fiancé')->engaged()->partner()->male(),
            Relationship::fixed('épouse', '%s de l’épouse')->wife(),
            Relationship::fixed('époux', '%s de l’époux')->husband(),
            Relationship::fixed('époux', '%s de l’époux')->spouse(),
            Relationship::fixed('conjoint', '%s du conjoint')->partner(),
            // In-laws
            Relationship::fixed('belle-mère', '%s de la belle-mère')->married()->spouse()->mother(),
            Relationship::fixed('beau-père', '%s du beau-père')->married()->spouse()->father(),
            Relationship::fixed('beau-parent', '%s du beau-parent')->married()->spouse()->parent(),
            Relationship::fixed('belle-fille', '%s de la belle-fille')->child()->wife(),
            Relationship::fixed('beau-fils', '%s du beau-fils')->child()->husband(),
            Relationship::fixed('beau-fils/belle-fille', '%s du beau-fils/belle-fille')->child()->married()->spouse(),
            Relationship::fixed('belle-sœur', '%s de la belle-sœur')->spouse()->sister(),
            Relationship::fixed('beau-frère', '%s du beau-frère')->spouse()->brother(),
            Relationship::fixed('beau-frère/belle-sœur', '%s du beau-frère/belle-sœur')->spouse()->sibling(),
            Relationship::fixed('belle-sœur', '%s de la belle-sœur')->sibling()->wife(),
            Relationship::fixed('beau-frère', '%s du beau-frère')->sibling()->husband(),
            Relationship::fixed('beau-frère/belle-sœur', '%s du beau-frère/belle-sœur')->sibling()->spouse(),
            // Grandparents and above
            Relationship::dynamic(fn (int $n) => $great($n - 1, 'grand-mère maternelle', 'de la '))->mother()->ancestor()->female(),
            Relationship::dynamic(fn (int $n) => $great($n - 1, 'grand-père maternel', 'du '))->mother()->ancestor()->male(),
            Relationship::dynamic(fn (int $n) => $great($n - 1, 'grand-mère paternelle', 'de la '))->father()->ancestor()->female(),
            Relationship::dynamic(fn (int $n) => $great($n - 1, 'grand-père paternel', 'du '))->father()->ancestor()->male(),
            Relationship::dynamic(fn (int $n) => $great($n - 2, 'grand-mère', 'de la '))->ancestor()->female(),
            Relationship::dynamic(fn (int $n) => $great($n - 2, 'grand-père', 'du '))->ancestor()->male(),
            Relationship::dynamic(fn (int $n) => $great($n - 2, 'grand-parent', 'du '))->ancestor(),
            // Grandchildren and below
            Relationship::dynamic(fn (int $n) => $great($n - 2, 'petite-fille', 'de la '))->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $great($n - 2, 'petit-fils', 'du '))->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $great($n - 2, 'petit-enfant', 'du'))->descendant(),
            // Collateral relatives
            Relationship::dynamic(fn (int $n) => $compoundgreat($n - 1, 'grand-', 'tante', 'de la ', 'de la '))->ancestor()->sister(),
            Relationship::dynamic(fn (int $n) => $compoundgreat($n - 1, 'grand-', 'tante par alliance', 'de la ', 'de la '))->ancestor()->sibling()->wife(),
            Relationship::dynamic(fn (int $n) => $compoundgreat($n - 1, 'grand-', 'oncle', 'de l’', 'du '))->ancestor()->brother(),
            Relationship::dynamic(fn (int $n) => $compoundgreat($n - 1, 'grand-', 'oncle par alliance', 'de l’', 'du '))->ancestor()->sibling()->husband(),
            Relationship::dynamic(fn (int $n) => $compoundgreat($n - 1, 'petite-', 'nièce', 'de la ', 'de la '))->sibling()->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $compoundgreat($n - 1, 'petite-', 'nièce par alliance', 'de la ', 'de la '))->married()->spouse()->sibling()->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $compoundgreat($n - 1, 'petit-', 'neveu', 'du ', 'du '))->sibling()->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $compoundgreat($n - 1, 'petit-', 'neveu par alliance', 'du ', 'du '))->married()->spouse()->sibling()->descendant()->male(),
            // Cousins (based on canon law)
            Relationship::dynamic(fn (int $n) => $symmetricCousin($n, 'F'))->symmetricCousin()->female(),
            Relationship::dynamic(fn (int $n) => $symmetricCousin($n, 'M'))->symmetricCousin()->male(),
            Relationship::dynamic(fn (int $up, int $down) => $asymmetricCousin($up, $down, 'F'))->cousin()->female(),
            Relationship::dynamic(fn (int $up, int $down) => $asymmetricCousin($up, $down, 'M'))->cousin()->male(),

        ];
    }
}
