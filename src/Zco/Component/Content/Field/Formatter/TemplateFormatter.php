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

namespace Zco\Component\Content\Field\Formatter;

use Zco\Component\Content\Field\FieldInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TemplateFormatter implements FormatterInterface
{
	protected $container;
	protected $template;
	
	public function __construct(ContainerInterface $container, $template)
	{
		$this->container = $container;
		$this->template = $template;
	}
	
	public function render($value, FieldInterface $field)
	{
		$engine = $this->container->get('core.templating');
		$variables = array('field' => $field, 'value' => $value);

		return $engine->render($template, $variables);
	}
	
	public function supports($name)
	{
		return true;
	}
}