<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees;

use Closure;
use Fisharebest\Webtrees\Elements\PedigreeLinkageType;

use function abs;
use function array_slice;
use function count;
use function in_array;
use function intdiv;
use function min;

/**
 * Class Relationship - define a relationship for a language.
 */
class Relationship
{
    // The basic components of a relationship.
    // These strings are needed for compatibility with the legacy algorithm.
    // Once that has been replaced, it may be more efficient to use integers here.
    public const SISTER   = 'sis';
    public const BROTHER  = 'bro';
    public const SIBLING  = 'sib';
    public const MOTHER   = 'mot';
    public const FATHER   = 'fat';
    public const PARENT   = 'par';
    public const DAUGHTER = 'dau';
    public const SON      = 'son';
    public const CHILD    = 'chi';
    public const WIFE     = 'wif';
    public const HUSBAND  = 'hus';
    public const SPOUSE   = 'spo';

    public const SIBLINGS = ['F' => self::SISTER, 'M' => self::BROTHER, 'U' => self::SIBLING];
    public const PARENTS  = ['F' => self::MOTHER, 'M' => self::FATHER, 'U' => self::PARENT];
    public const CHILDREN = ['F' => self::DAUGHTER, 'M' => self::SON, 'U' => self::CHILD];
    public const SPOUSES  = ['F' => self::WIFE, 'M' => self::HUSBAND, 'U' => self::SPOUSE];

    // Generates a name from the matched relationship.
    private Closure $callback;

    /** @var array<Closure> List of rules that need to match */
    private array $matchers;

    /**
     * @param Closure $callback
     */
    private function __construct(Closure $callback)
    {
        $this->callback = $callback;
        $this->matchers = [];
    }

    /**
     * Allow fluent constructor.
     *
     * @param string $nominative
     * @param string $genitive
     *
     * @return Relationship
     */
    public static function fixed(string $nominative, string $genitive): Relationship
    {
        return new self(fn () => [$nominative, $genitive]);
    }

    /**
     * Allow fluent constructor.
     *
     * @param Closure $callback
     *
     * @return Relationship
     */
    public static function dynamic(Closure $callback): Relationship
    {
        return new self($callback);
    }

    /**
     * Does this relationship match the pattern?
     *
     * @param array<Individual|Family> $nodes
     * @param array<string>            $patterns
     *
     * @return array<string>|null [nominative, genitive] or null
     */
    public function match(array $nodes, array $patterns): ?array
    {
        $captures = [];

        foreach ($this->matchers as $matcher) {
            if (!$matcher($nodes, $patterns, $captures)) {
                return null;
            }
        }

        if ($patterns === []) {
            return ($this->callback)(...$captures);
        }

        return null;
    }

    /**
     * @return Relationship
     */
    public function adopted(): Relationship
    {
        $this->matchers[] = static fn (array $nodes): bool => count($nodes) > 2 && $nodes[2]
                ->facts(['FAMC'], false, Auth::PRIV_HIDE)
                ->contains(fn (Fact $fact): bool => $fact->value() === '@' . $nodes[1]->xref() . '@' && $fact->attribute('PEDI') === PedigreeLinkageType::VALUE_ADOPTED);

        return $this;
    }

    /**
     * @return Relationship
     */
    public function adoptive(): Relationship
    {
        $this->matchers[] = static fn (array $nodes): bool => $nodes[0]
            ->facts(['FAMC'], false, Auth::PRIV_HIDE)
            ->contains(fn (Fact $fact): bool => $fact->value() === '@' . $nodes[1]->xref() . '@' && $fact->attribute('PEDI') === PedigreeLinkageType::VALUE_ADOPTED);

        return $this;
    }

    /**
     * @return Relationship
     */
    public function brother(): Relationship
    {
        return $this->relation([self::BROTHER]);
    }

    /**
     * Match the next relationship in the path.
     *
     * @param array<string> $relationships
     *
     * @return Relationship
     */
    protected function relation(array $relationships): Relationship
    {
        $this->matchers[] = static function (array &$nodes, array &$patterns) use ($relationships): bool {
            if (in_array($patterns[0] ?? '', $relationships, true)) {
                $nodes    = array_slice($nodes, 2);
                $patterns = array_slice($patterns, 1);

                return true;
            }

            return false;
        };

        return $this;
    }

    /**
     * The number of ancestors may be different to the number of descendants
     *
     * @return Relationship
     */
    public function cousin(): Relationship
    {
        return $this->ancestor()->sibling()->descendant();
    }

    /**
     * @return Relationship
     */
    public function descendant(): Relationship
    {
        return $this->repeatedRelationship(self::CHILDREN);
    }

    /**
     * Match a repeated number of the same type of component
     *
     * @param array<string> $relationships
     *
     * @return Relationship
     */
    protected function repeatedRelationship(array $relationships): Relationship
    {
        $this->matchers[] = static function (array &$nodes, array &$patterns, array &$captures) use ($relationships): bool {
            $limit = min(intdiv(count($nodes), 2), count($patterns));

            for ($generations = 0; $generations < $limit; ++$generations) {
                if (!in_array($patterns[$generations], $relationships, true)) {
                    break;
                }
            }

            if ($generations > 0) {
                $nodes      = array_slice($nodes, 2 * $generations);
                $patterns   = array_slice($patterns, $generations);
                $captures[] = $generations;

                return true;
            }

            return false;
        };

        return $this;
    }

    /**
     * @return Relationship
     */
    public function sibling(): Relationship
    {
        return $this->relation(self::SIBLINGS);
    }

    /**
     * @return Relationship
     */
    public function ancestor(): Relationship
    {
        return $this->repeatedRelationship(self::PARENTS);
    }

    /**
     * @return Relationship
     */
    public function child(): Relationship
    {
        return $this->relation(self::CHILDREN);
    }

    /**
     * @return Relationship
     */
    public function daughter(): Relationship
    {
        return $this->relation([self::DAUGHTER]);
    }

    /**
     * @return Relationship
     */
    public function divorced(): Relationship
    {
        return $this->marriageStatus('DIV');
    }

    /**
     * Match a marriage status
     *
     * @param string $status
     *
     * @return Relationship
     */
    protected function marriageStatus(string $status): Relationship
    {
        $this->matchers[] = static function (array $nodes) use ($status): bool {
            $family = $nodes[1] ?? null;

            if ($family instanceof Family) {
                $fact = $family->facts(['ENGA', 'MARR', 'DIV', 'ANUL'], true, Auth::PRIV_HIDE)->last();

                if ($fact instanceof Fact) {
                    switch ($status) {
                        case 'MARR':
                            return $fact->tag() === 'FAM:MARR';

                        case 'DIV':
                            return $fact->tag() === 'FAM:DIV' || $fact->tag() === 'FAM:ANUL';

                        case 'ENGA':
                            return $fact->tag() === 'FAM:ENGA';
                    }
                }
            }

            return false;
        };

        return $this;
    }

    /**
     * @return Relationship
     */
    public function engaged(): Relationship
    {
        return $this->marriageStatus('ENGA');
    }

    /**
     * @return Relationship
     */
    public function father(): Relationship
    {
        return $this->relation([self::FATHER]);
    }

    /**
     * @return Relationship
     */
    public function female(): Relationship
    {
        return $this->sex('F');
    }

    /**
     * Match the sex of the current individual
     *
     * @param string $sex
     *
     * @return Relationship
     */
    protected function sex(string $sex): Relationship
    {
        $this->matchers[] = static fn(array $nodes): bool => $nodes[0]->sex() === $sex;

        return $this;
    }

    /**
     * @return Relationship
     */
    public function fostered(): Relationship
    {
        $this->matchers[] = static fn (array $nodes): bool => count($nodes) > 2 && $nodes[2]
                ->facts(['FAMC'], false, Auth::PRIV_HIDE)
                ->contains(fn (Fact $fact): bool => $fact->value() === '@' . $nodes[1]->xref() . '@' && $fact->attribute('PEDI') === PedigreeLinkageType::VALUE_FOSTER);

        return $this;
    }

    /**
     * @return Relationship
     */
    public function fostering(): Relationship
    {
        $this->matchers[] = static fn (array $nodes): bool => $nodes[0]
            ->facts(['FAMC'], false, Auth::PRIV_HIDE)
            ->contains(fn (Fact $fact): bool => $fact->value() === '@' . $nodes[1]->xref() . '@' && $fact->attribute('PEDI') === PedigreeLinkageType::VALUE_FOSTER);

        return $this;
    }

    /**
     * @return Relationship
     */
    public function husband(): Relationship
    {
        return $this->married()->relation([self::HUSBAND]);
    }

    /**
     * @return Relationship
     */
    public function married(): Relationship
    {
        return $this->marriageStatus('MARR');
    }

    /**
     * @return Relationship
     */
    public function male(): Relationship
    {
        return $this->sex('M');
    }

    /**
     * @return Relationship
     */
    public function mother(): Relationship
    {
        return $this->relation([self::MOTHER]);
    }

    /**
     * @return Relationship
     */
    public function older(): Relationship
    {
        $this->matchers[] = static function (array $nodes): bool {
            $date1 = $nodes[0]->facts(['BIRT'], false, Auth::PRIV_HIDE)->map(fn (Fact $fact): Date => $fact->date())->first() ?? new Date('');
            $date2 = $nodes[2]->facts(['BIRT'], false, Auth::PRIV_HIDE)->map(fn (Fact $fact): Date => $fact->date())->first() ?? new Date('');

            return Date::compare($date1, $date2) > 0;
        };

        return $this;
    }

    /**
     * @return Relationship
     */
    public function parent(): Relationship
    {
        return $this->relation(self::PARENTS);
    }

    /**
     * @return Relationship
     */
    public function sister(): Relationship
    {
        return $this->relation([self::SISTER]);
    }

    /**
     * @return Relationship
     */
    public function son(): Relationship
    {
        return $this->relation([self::SON]);
    }

    /**
     * @return Relationship
     */
    public function spouse(): Relationship
    {
        return $this->married()->partner();
    }

    /**
     * @return Relationship
     */
    public function partner(): Relationship
    {
        return $this->relation(self::SPOUSES);
    }

    /**
     * The number of ancestors must be the same as the number of descendants
     *
     * @return Relationship
     */
    public function symmetricCousin(): Relationship
    {
        $this->matchers[] = static function (array &$nodes, array &$patterns, array &$captures): bool {
            $count = count($patterns);

            $n = 0;

            // Ancestors
            while ($n < $count && in_array($patterns[$n], Relationship::PARENTS, true)) {
                $n++;
            }

            // No ancestors?  Not enough path left for descendants?
            if ($n === 0 || $n * 2 + 1 !== $count) {
                return false;
            }

            // Siblings
            if (!in_array($patterns[$n], Relationship::SIBLINGS, true)) {
                return false;
            }

            // Descendants
            for ($descendants = $n + 1; $descendants < $count; ++$descendants) {
                if (!in_array($patterns[$descendants], Relationship::CHILDREN, true)) {
                    return false;
                }
            }


            $nodes      = array_slice($nodes, 2 * (2 * $n + 1));
            $patterns   = [];
            $captures[] = $n;

            return true;
        };

        return $this;
    }

    /**
     * @return Relationship
     */
    public function twin(): Relationship
    {
        $this->matchers[] = static function (array $nodes): bool {
            $date1 = $nodes[0]->facts(['BIRT'], false, Auth::PRIV_HIDE)->map(fn (Fact $fact): Date => $fact->date())->first() ?? new Date('');
            $date2 = $nodes[2]->facts(['BIRT'], false, Auth::PRIV_HIDE)->map(fn (Fact $fact): Date => $fact->date())->first() ?? new Date('');

            return
                $date1->isOK() &&
                $date2->isOK() &&
                abs($date1->julianDay() - $date2->julianDay()) < 2 &&
                $date1->minimumDate()->day > 0 &&
                $date2->minimumDate()->day > 0;
        };

        return $this;
    }

    /**
     * @return Relationship
     */
    public function wife(): Relationship
    {
        return $this->married()->relation([self::WIFE]);
    }

    /**
     * @return Relationship
     */
    public function younger(): Relationship
    {
        $this->matchers[] = static function (array $nodes): bool {
            $date1 = $nodes[0]->facts(['BIRT'], false, Auth::PRIV_HIDE)->map(fn (Fact $fact): Date => $fact->date())->first() ?? new Date('');
            $date2 = $nodes[2]->facts(['BIRT'], false, Auth::PRIV_HIDE)->map(fn (Fact $fact): Date => $fact->date())->first() ?? new Date('');

            return Date::compare($date1, $date2) < 0;
        };

        return $this;
    }
}
