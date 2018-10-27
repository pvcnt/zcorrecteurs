<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012 Corrigraphie
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Zco\Bundle\AdminBundle\Menu;

/**
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class MenuItem
{
    private $name;
    private $uri;
    private $children = [];
    private $count = 0;
    private $label;
    private $displayed = true;

    /**
     * Constructor.
     *
     * @param string $name
     * @param array $options
     */
    public function __construct(string $name, array $options = [])
    {
        $this->name = $name;
        if (isset($options['count'])) {
            $this->count = (int)$options['count'];
        }
        if (isset($options['uri'])) {
            $this->uri = (string)$options['uri'];
        }
        if (isset($options['label'])) {
            $this->uri = (string)$options['label'];
        }
    }

    public function getChild($name): MenuItem
    {
        if (!isset($this->children[$name])) {
            $this->addChild($name);
        }

        return $this->children[$name];
    }

    public function addChild(string $name, array $options = []): MenuItem
    {
        $child = new MenuItem($name, $options);
        $this->children[$name] = $child;

        return $child;
    }

    /**
     * @return null|string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label ?? $this->name;
    }

    /**
     * @return MenuItem[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @return bool
     */
    public function isDisplayed(): bool
    {
        return $this->displayed;
    }

    /**
     * @param bool $displayed
     * @return MenuItem
     */
    public function setDisplay(bool $displayed): MenuItem
    {
        $this->displayed = $displayed;

        return $this;
    }

    /**
     * Vérifie l'existence d'un sous-menu.
     *
     * @param  string $name Le nom du sous-menu
     * @return bool
     */
    public function hasChild($name): bool
    {
        return isset($this->children[$name]);
    }

    /**
     * Retourne la valeur du compteur de tâches.
     *
     * @return integer La valeur du compteur
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @param int $count
     * @return MenuItem
     */
    public function setCount(int $count): MenuItem
    {
        $this->count = $count;

        return $this;
    }
}