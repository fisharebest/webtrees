<?php

namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\Encodings\UTF8;
use InvalidArgumentException;
use Normalizer;
use Stringable;

/**
 * User submitted text.
 */
final readonly class Text implements Stringable
{
    private string $text;

    public function __construct(string $text)
    {
        // Replace invalid UTF-8 characters with the Unicode replacement character.
        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        // Replace combining characters with their precomposed equivalents.
        $this->text = Normalizer::normalize($text, Normalizer::FORM_C);
    }

    public function text(): string
    {
        return $this->text;
    }

    /**
     * Substring function that does not break grapheme clusters or combining diacritics.
     */
    public function substr(int $offset, int $length): self
    {
        // If the offset points to the middle of a grapheme cluster or combining diacritic, then move it.
        if ($offset < 0) {
            $offset = mb_strlen(grapheme_extract($this->text, mb_strlen($this->text) + $offset, GRAPHEME_EXTR_MAXCHARS));
        } else {
            $offset = mb_strlen(grapheme_extract($this->text, $offset, GRAPHEME_EXTR_MAXCHARS));
        }

        $text = grapheme_extract($this->text, $offset, $length);

        if ($text === false) {
            // Should never happen, as we validated the text in the constructor.
            $text = UTF8::REPLACEMENT_CHARACTER;
        }

        return new self($text);
    }

    /**
     * Truncate to fit in a database field.
     * Do not split any combining characters or grapheme clusters.
     */
    public function truncate(int $length): string
    {
        $text = grapheme_extract($this->text, $length, GRAPHEME_EXTR_MAXCHARS);

        if ($text === false) {
            // Should never happen, as we validated the text in the constructor.
            $text = UTF8::REPLACEMENT_CHARACTER;
        }

        return $text;
    }

    public function lengthBytes(): int
    {
        return strlen($this->text);
    }

    public function lengthChars(): int
    {
        return mb_strlen($this->text);
    }

    public function lengthGraphemes(): int
    {
        return grapheme_strlen($this->text);
    }

    public function __toString(): string
    {
        return $this->text;
    }
}
