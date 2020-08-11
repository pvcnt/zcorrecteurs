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

namespace Zco\Component\Templating\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

class FilterContentEvent extends Event
{
	private $content;
	private $template;
	private $request;
	
	public function __construct(Request $request, $content, $template = null)
	{
		$this->template = $template;
		$this->request = $request;
		$this->content = $content;
	}
	
	public function getRequest()
	{
		return $this->request;
	}
	
	public function getContent()
	{
		return $this->content;
	}
	
	public function getTemplate()
	{
		return $this->template;
	}
	
	public function setContent($content)
	{
		$this->content = $content;
	}
}