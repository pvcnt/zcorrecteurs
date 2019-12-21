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

namespace Zco\Bundle\Doctrine1Bundle;

use Zco\Bundle\Doctrine1Bundle\Adapter\PDOAdapter;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bundle assurant une intégration basique de Doctrine1 dans Symfony2.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class ZcoDoctrine1Bundle extends Bundle
{
	/**
	 * {@inheritdoc}
	 */
	public function boot()
	{
		try
		{
			$dbh  = $this->container->get('zco_doctrine1.adapter.pdo');
			$conn = \Doctrine_Manager::connection($dbh, 'doctrine');
			$conn->setOption('dsn', $dbh->getDsn());
			$conn->setOption('username', $dbh->getUsername());
			$conn->setOption('password', $dbh->getPassword());
		}
		catch (\Exception $e)
		{
			if ($this->container->getParameter('kernel.debug'))
			{
				throw $e;
			}
			
			header('HTTP/1.1 503 Temporarily unavailable');
			readfile(__DIR__.'/Resources/views/sqlDown.html');
			exit();
		}

		//Configure le chargement des modèles.
		//TODO: mettre ça en cache lors de la génération des modèles.
		$directories = array();
		foreach ($this->container->get('kernel')->getBundles() as $bundle)
		{
		    if (is_dir($bundle->getPath().'/Entity'))
		    {
		        $directories[] = $bundle->getPath().'/Entity';
		    }
		}
		
		//Les modèles générés sont cherchés en cache, les autres sont cherchés 
		//directement dans les bundles.
		spl_autoload_register(function($className) use($directories)
		{
		    foreach ($directories as $dir)
		    {
		        if (is_file($file = $dir.'/'.$className.'.class.php'))
		        {
		            include($file);
		            return true;
		        }
		    }
		    
		    return false;
		});
		
		//Configure Doctrine.
		$manager = \Doctrine_Manager::getInstance();
		$manager->setAttribute(\Doctrine_Core::ATTR_TBLNAME_FORMAT, $this->container->getParameter('database.prefix').'%s');
	}
}