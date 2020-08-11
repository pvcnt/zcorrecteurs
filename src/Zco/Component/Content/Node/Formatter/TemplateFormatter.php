<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2020 Corrigraphie
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

namespace Zco\Component\Content\Node\Formatter;

use Zco\Component\Content\Node\NodeInterface;
use Zco\Component\Content\Field\Formatter\FormatterInterface as FieldFormatterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TemplateFormatter implements FormatterInterface
{
	protected $template;
	protected $formatters = array();
	protected $container;
	
	public function __construct(ContainerInterface $container, $template, array $formatters = array())
	{
		$this->container = $container;
		$this->template = $template;
		$this->formatters = $formatters;
	}
	
	public function add($key, FieldFormatterInterface $formatter)
	{
		$this->formatters[$key] = $formatter;
	}
	
	public function render(NodeInterface $node)
	{
		$engine = $this->container->get('core.templating');
		$variables = array('node' => $node);
		
		foreach ($node->getValues() as $name => $value)
		{
			if (isset($this->formatters[$name]))
			{
				$variables[$name] = $this->formatters[$name]->render($value, $node->getField($name));
			}
		}

		return $engine->render($template, $variables);
	}
	
	public function supports($name)
	{
		return true;
	}
}