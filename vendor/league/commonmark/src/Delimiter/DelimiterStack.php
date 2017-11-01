<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Delimiter;

class DelimiterStack
{
    /**
     * @var Delimiter|null
     */
    protected $top;

    public function getTop()
    {
        return $this->top;
    }

    public function push(Delimiter $newDelimiter)
    {
        $newDelimiter->setPrevious($this->top);

        if ($this->top !== null) {
            $this->top->setNext($newDelimiter);
        }

        $this->top = $newDelimiter;
    }

    /**
     * @param Delimiter|null $stackBottom
     *
     * @return Delimiter|null
     */
    public function findEarliest(Delimiter $stackBottom = null)
    {
        $delimiter = $this->top;
        while ($delimiter !== null && $delimiter->getPrevious() !== $stackBottom) {
            $delimiter = $delimiter->getPrevious();
        }

        return $delimiter;
    }

    /**
     * @param Delimiter $delimiter
     */
    public function removeDelimiter(Delimiter $delimiter)
    {
        if ($delimiter->getPrevious() !== null) {
            $delimiter->getPrevious()->setNext($delimiter->getNext());
        }

        if ($delimiter->getNext() === null) {
            // top of stack
            $this->top = $delimiter->getPrevious();
        } else {
            $delimiter->getNext()->setPrevious($delimiter->getPrevious());
        }
    }

    /**
     * @param Delimiter|null $stackBottom
     */
    public function removeAll(Delimiter $stackBottom = null)
    {
        while ($this->top && $this->top !== $stackBottom) {
            $this->removeDelimiter($this->top);
        }
    }

    /**
     * @param string $character
     */
    public function removeEarlierMatches($character)
    {
        $opener = $this->top;
        while ($opener !== null) {
            if ($opener->getChar() === $character) {
                $opener->setActive(false);
            }

            $opener = $opener->getPrevious();
        }
    }

    /**
     * @param string|string[] $characters
     *
     * @return Delimiter|null
     */
    public function searchByCharacter($characters)
    {
        if (!is_array($characters)) {
            $characters = [$characters];
        }

        $opener = $this->top;
        while ($opener !== null) {
            if (in_array($opener->getChar(), $characters)) {
                break;
            }
            $opener = $opener->getPrevious();
        }

        return $opener;
    }

    /**
     * @param string|string[] $characters
     * @param callable        $callback
     * @param Delimiter       $stackBottom
     */
    public function iterateByCharacters($characters, $callback, Delimiter $stackBottom = null)
    {
        if (!is_array($characters)) {
            $characters = [$characters];
        }

        $openersBottom = array_fill_keys($characters, $stackBottom);

        // Find first closer above stackBottom
        $closer = $this->findEarliest($stackBottom);

        while ($closer !== null) {
            $closerChar = $closer->getChar();

            if (!$closer->canClose() || !in_array($closerChar, $characters)) {
                $closer = $closer->getNext();
                continue;
            }

            $oddMatch = false;
            $opener = $this->findMatchingOpener($closer, $openersBottom, $stackBottom, $oddMatch);
            if ($opener) {
                $closer = $callback($opener, $closer, $this);
            } elseif ($oddMatch) {
                $closer = $closer->getNext();
            } else {
                $oldCloser = $closer;
                $closer = $closer->getNext();
                // Set lower bound for future searches for openers:
                $openersBottom[$closerChar] = $oldCloser->getPrevious();
                if (!$oldCloser->canOpen()) {
                    // We can remove a closer that can't be an opener,
                    // once we've seen there's no matching opener:
                    $this->removeDelimiter($oldCloser);
                }
                continue;
            }
        }
    }

    /**
     * @param Delimiter      $closer
     * @param array          $openersBottom
     * @param Delimiter|null $stackBottom
     * @param bool           $oddMatch
     *
     * @return Delimiter|null
     */
    protected function findMatchingOpener(Delimiter $closer, $openersBottom, Delimiter $stackBottom = null, &$oddMatch = false)
    {
        $closerChar = $closer->getChar();
        $opener = $closer->getPrevious();

        while ($opener !== null && $opener !== $stackBottom && $opener !== $openersBottom[$closerChar]) {
            $oddMatch = ($closer->canOpen() || $opener->canClose()) && ($opener->getNumDelims() + $closer->getNumDelims()) % 3 === 0;
            if ($opener->getChar() === $closerChar && $opener->canOpen() && !$oddMatch) {
                return $opener;
            }

            $opener = $opener->getPrevious();
        }
    }

    /**
     * @param Delimiter      $closer
     * @param array          $openersBottom
     * @param Delimiter|null $stackBottom
     *
     * @return Delimiter|null
     *
     * @deprecated Use findMatchingOpener() instead.  This method will be removed in the next major release.
     */
    protected function findFirstMatchingOpener(Delimiter $closer, $openersBottom, Delimiter $stackBottom = null)
    {
        $closerChar = $closer->getChar();
        $opener = $closer->getPrevious();

        while ($opener !== null && $opener !== $stackBottom && $opener !== $openersBottom[$closerChar]) {
            if ($opener->getChar() === $closerChar && $opener->canOpen()) {
                return $opener;
            }

            $opener = $opener->getPrevious();
        }
    }
}
