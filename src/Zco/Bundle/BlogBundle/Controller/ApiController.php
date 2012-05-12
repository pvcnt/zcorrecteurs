<?php

/**
 * Copyright 2012 Corrigraphie
 * 
 * This file is part of zCorrecteurs.fr.
 *
 * zCorrecteurs.fr is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * zCorrecteurs.fr is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with zCorrecteurs.fr. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Zco\Bundle\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends Controller
{
	public function indexAction(Request $request)
	{
		$articles = \Doctrine_Core::getTable('Blog')
			->query(array(
				'status'    => \Blog::STATUS_PUBLISHED,
				'scheduled' => false, 
				'#order_by' => '-date',
			));
		
		$json = array();
		foreach ($articles as $i => $article)
		{
			$json[] = $article->export();
		}
		
		return new Response(json_encode($json));
	}
	
	public function categoryAction(Request $request, $id)
	{
		$articles = \Doctrine_Core::getTable('Blog')
			->query(array(
				'status'    => \Blog::STATUS_PUBLISHED,
				'scheduled' => false, 
				'#order_by' => '-date', 
				'category_id' => $id,
			));
		
		$json = array();
		foreach ($articles as $i => $article)
		{
			$json[] = $article->export();
		}
		
		return new Response(json_encode($json));
	}
	
	public function articleAction(Request $request, $id)
	{
		$articles = \Doctrine_Core::getTable('Blog')
			->query(array(
				'id'        => $id,
				'status'    => \Blog::STATUS_PUBLISHED,
				'scheduled' => false,
			));
		
		$json = array();
		foreach ($articles as $i => $article)
		{
			$json[] = $article->export();
		}
		
		return new Response(json_encode($json));
	}
}