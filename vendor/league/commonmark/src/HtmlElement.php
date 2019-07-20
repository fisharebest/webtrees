<?php

namespace League\CommonMark;

use League\CommonMark\Util\Xml;

class HtmlElement
{
    /**
     * @var string
     */
    protected $tagName;

    /**
     * @var string[]
     */
    protected $attributes = [];

    /**
     * @var HtmlElement|HtmlElement[]|string
     */
    protected $contents;

    /**
     * @var bool
     */
    protected $selfClosing = false;

    /**
     * @param string                                $tagName     Name of the HTML tag
     * @param string[]                              $attributes  Array of attributes (values should be unescaped)
     * @param HtmlElement|HtmlElement[]|string|null $contents    Inner contents, pre-escaped if needed
     * @param bool                                  $selfClosing Whether the tag is self-closing
     */
    public function __construct(string $tagName, array $attributes = [], $contents = '', bool $selfClosing = false)
    {
        $this->tagName = $tagName;
        $this->attributes = $attributes;
        $this->selfClosing = $selfClosing;

        $this->setContents($contents ?? '');
    }

    /**
     * @return string
     */
    public function getTagName(): string
    {
        return $this->tagName;
    }

    /**
     * @return string[]
     */
    public function getAllAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param string $key
     *
     * @return string|null
     */
    public function getAttribute(string $key): ?string
    {
        if (!isset($this->attributes[$key])) {
            return null;
        }

        return $this->attributes[$key];
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return $this
     */
    public function setAttribute(string $key, string $value): self
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * @param bool $asString
     *
     * @return HtmlElement|HtmlElement[]|string
     */
    public function getContents(bool $asString = true)
    {
        if (!$asString || \is_string($this->contents)) {
            return $this->contents;
        }

        if (\is_array($this->contents)) {
            return \implode('', $this->contents);
        }

        return (string) $this->contents;
    }

    /**
     * Sets the inner contents of the tag (must be pre-escaped if needed)
     *
     * @param HtmlElement|HtmlElement[]|string $contents
     *
     * @return $this
     */
    public function setContents($contents): self
    {
        $this->contents = $contents ?? '';

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $result = '<' . $this->tagName;

        foreach ($this->attributes as $key => $value) {
            $result .= ' ' . $key . '="' . Xml::escape($value) . '"';
        }

        if ($this->contents !== '') {
            $result .= '>' . $this->getContents() . '</' . $this->tagName . '>';
        } elseif ($this->selfClosing) {
            $result .= ' />';
        } else {
            $result .= '></' . $this->tagName . '>';
        }

        return $result;
    }
}
