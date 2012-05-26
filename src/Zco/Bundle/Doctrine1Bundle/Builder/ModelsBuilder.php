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

namespace Zco\Bundle\Doctrine1Bundle\Builder;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Génère les modèles contenus dans des fichiers de définition au format YAML.
 * Sont générés les modèles de base (*Table) ainsi que les nouveaux modèles 
 * vides lorsque des entités ont été ajoutées.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class ModelsBuilder
{
    private $kernel;
    
    /**
     * Constructeur.
     *
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }
    
    /**
     * Génère les modèles à partir de l'ensemble des schémas présents dans 
     * les différents bundles. Les modèles de base sont stockés dans le dossier 
     * de cache de l'environnement courant, les modèles surchargeables sont 
     * déplacés dans le bundle auquel ils appartiennent.
     *
     * @param string $cacheDir L'emplacement où stocker les fichiers cache
 	 * @param boolean $onlyBase Ne générer que les modèles de base
 	 * @param OutputInterface $output Interface d'affichage à utiliser
     */
    public function buildBaseModels($cacheDir, $onlyBase = false, OutputInterface $output = null)
    {
        if (!is_dir($cacheDir = $cacheDir.'/zco_doctrine1'))
        {
            if (false === @mkdir($cacheDir, 0777, true))
            {
                throw new \RuntimeException(sprintf('Unable to create the Doctrine cache directory "%s".', $cacheDir));
            }
        }
        elseif (!is_writable($cacheDir))
        {
            throw new \RuntimeException(sprintf('The Doctrine cache directory "%s" is not writeable for the current system user.', $cacheDir));
        }
		
        $models = array('options' => array(
            'collate' => 'utf8_unicode_ci', 
            'charset' => 'utf8',
        ));
	    $bundles = array();
	    
	    //Pour chaque schéma YAML présent dans un bundle, ajoute les modèles 
	    //contenus à la liste et note son bundle de provenance.
	    foreach ($this->kernel->getBundles() as $bundle)
		{
		    $configDir = $bundle->getPath().'/Resources/config/doctrine';
		    $entityDir = $bundle->getPath().'/Entity';
		    
		    if (!is_dir($configDir))
		    {
		        continue;
	        }
	        
	        if ($output)
	        {
	            $output->writeln(sprintf('Generating entities for bundle "<info>%s</info>"', $bundle->getName()));
	        }
	        
		    if (!is_dir($entityDir))
	        {
	            if (false === @mkdir($entityDir, 0777))
                {
                    throw new \RuntimeException(sprintf('Unable to create the entities directory directory "%s".', $entityDir));
                }
	        }
	        
	        //Pour chaque fichier YAML, on récupère les définitions de modèle 
	        //qu'il contient. On s'assure que chaque modèle ne soit défini qu'une fois.
	        foreach (glob($configDir.'/*.yml') as $file)
	        {
	            $schema = Yaml::parse($file);
	            foreach ($schema as $model => $definition)
	            {
	                if ($model === 'options')
	                {
	                    continue;
	                }
	                
	                if (isset($models[$model]))
	                {
	                    throw new \LogicException(sprintf(
	                        'Cannot redefine entity "%s" in "%s", already defined in "%"', 
	                        $model, $bundle->getName(), $bundles[$model]->getName()
	                    ));
	                }
                    
                    $bundles[$model] = $bundle;
	                $models[$model] = $definition;
	            }
	        }
		}
		
		//Génère un schéma consolidé regroupant toutes les définitions 
		//de tous les modèles de chacun des bundles.
		$file = $cacheDir.'/schema_'.microtime(true).'.yml';
		file_put_contents($file, Yaml::dump($models, 3));
		
		$import = new \Doctrine_Import_Schema();
        $import->setOptions(array(
            'suffix' => '.class.php', 
            'generateTableClasses' => true,
        ));
        $import->importSchema($file, 'yml', $cacheDir);
        unlink($file);
        
        //Répartit les modèles dans les bundles auxquels ils appartiennent.
        $iterator = new \DirectoryIterator($cacheDir);
        foreach ($iterator as $fileinfo)
        {
            $className = preg_replace('/(Table)?\.class\.php$/', '', $fileinfo->getFilename());
            if ($fileinfo->isFile() && isset($bundles[$className]) && !$onlyBase)
            {
                $targetPath = $bundles[$className]->getPath().'/Entity/'.$fileinfo->getFilename();
                
                //On s'assure de ne pas écraser de modèle déjà existant.
                if (!is_file($targetPath))
                {
                    copy($fileinfo->getRealpath(), $targetPath);
                }
                unlink($fileinfo->getRealpath());
            }
			elseif ($onlyBase)
			{
				unlink($fileinfo->getRealpath());
			}
        }
    }
}