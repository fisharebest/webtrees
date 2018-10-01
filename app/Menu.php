<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace Fisharebest\Webtrees;

/**
 * System for generating menus.
 */
class Menu
{
    /** @var string The text to be displayed in the mneu */
    private $label;

    /** @var string The target URL or href */
    private $link;

    /** @var string The CSS class used to style this menu item */
    private $class;

    /** @var string[] A list of optional HTML attributes, such as onclick or data-xxx */
    private $attrs;

    /** @var Menu[] An optional list of sub-menus. */
    private $submenus;

    /**
     * Constructor for the menu class
     *
     * @param string   $label    The label for the menu item
     * @param string   $link     The target URL
     * @param string   $class    A CSS class
     * @param string[] $attrs    Optional attributes, such as onclick or data-xxx
     * @param Menu[]   $submenus Any submenus
     */
    public function __construct($label, $link = '#', $class = '', array $attrs = [], array $submenus = [])
    {
        $this
            ->setLabel($label)
            ->setLink($link)
            ->setClass($class)
            ->setAttrs($attrs)
            ->setSubmenus($submenus);
    }

    /**
     * Render this menu using Bootstrap4 markup
     *
     * @return string
     */
    public function bootstrap4()
    {
        if ($this->submenus) {
            $submenus = '';
            foreach ($this->submenus as $submenu) {
                $attrs = '';
                foreach ($submenu->attrs as $key => $value) {
                    $attrs .= ' ' . $key . '="' . e($value) . '"';
                }

                $class = trim('dropdown-item ' . $submenu->class);
                $submenus .= '<a class="' . $class . '" href="' . e($submenu->link) . '"' . $attrs . '>' . $submenu->label . '</a>';
            }

            $class = trim('nav-item dropdown ' . $this->class);

            return
                '<li class="' . $class . '">' .
                '<a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">' .
                $this->label .
                '<span class="caret"></span></a>' .
                '<div class="dropdown-menu" role="menu">' .
                $submenus .
                '</div>' .
                '</li>';
        }

        $attrs = '';
        foreach ($this->attrs as $key => $value) {
            $attrs .= ' ' . $key . '="' . e($value) . '"';
        }

        $class = trim('nav-item ' . $this->class);

        return '<li class="' . $class . '"><a class="nav-link" href="' . e($this->link) . '"' . $attrs . '>' . $this->label . '</a></li>';
    }

    /**
     * Get the optional attributes.
     *
     * @return string[]
     */
    public function getAttrs(): array
    {
        return $this->attrs;
    }

    /**
     * Set the optional attributes.
     *
     * @param string[] $attrs
     *
     * @return $this
     */
    public function setAttrs(array $attrs): self
    {
        $this->attrs = $attrs;

        return $this;
    }

    /**
     * Get the class.
     *
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * Set the class.
     *
     * @param string $class
     *
     * @return $this
     */
    public function setClass($class): self
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Get the label.
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Set the label.
     *
     * @param string $label
     *
     * @return $this
     */
    public function setLabel($label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get the link.
     *
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * Set the link.
     *
     * @param string $link
     *
     * @return $this
     */
    public function setLink($link): self
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Add a submenu to this menu
     *
     * @param Menu $menu
     *
     * @return $this
     */
    public function addSubmenu($menu): self
    {
        $this->submenus[] = $menu;

        return $this;
    }

    /**
     * Render this menu as an HTML list
     *
     * @return string
     */
    public function getMenuAsList(): string
    {
        $attrs = '';
        foreach ($this->attrs as $key => $value) {
            $attrs .= ' ' . $key . '="' . e($value) . '"';
        }
        if ($this->link) {
            $link = ' href="' . e($this->link) . '"';
        } else {
            $link = '';
        }
        $html = '<a' . $link . $attrs . '>' . $this->label . '</a>';
        if ($this->submenus) {
            $html .= '<ul>';
            foreach ($this->submenus as $submenu) {
                $html .= $submenu->getMenuAsList();
            }
            $html .= '</ul>';
        }

        return '<li class="' . $this->class . '">' . $html . '</li>';
    }

    /**
     * Get the sub-menus.
     *
     * @return Menu[]
     */
    public function getSubmenus(): array
    {
        return $this->submenus;
    }

    /**
     * Set the sub-menus.
     *
     * @param Menu[] $submenus
     *
     * @return $this
     */
    public function setSubmenus(array $submenus): self
    {
        $this->submenus = $submenus;

        return $this;
    }
}
