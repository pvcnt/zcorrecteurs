<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2019 Corrigraphie
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

use Zco\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        $bundles = array(
            //Bundles génériques.
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Zco\Bundle\Doctrine1Bundle\ZcoDoctrine1Bundle(),
            new Zco\Bundle\VitesseBundle\ZcoVitesseBundle(),
            new Avalanche\Bundle\ImagineBundle\AvalancheImagineBundle(),
            new Knp\Bundle\GaufretteBundle\KnpGaufretteBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),

            //Bundles nécessaires pour que les modules fonctionnent.
            new Zco\Bundle\CoreBundle\ZcoCoreBundle(),
            new Zco\Bundle\ParserBundle\ZcoParserBundle(),
            new Zco\Bundle\UserBundle\ZcoUserBundle(),
            
            //Modules du site.
            new Zco\Bundle\AccueilBundle\ZcoAccueilBundle(),
            new Zco\Bundle\AdminBundle\ZcoAdminBundle(),
            new Zco\Bundle\AideBundle\ZcoAideBundle(),
            new Zco\Bundle\AboutBundle\ZcoAboutBundle(),
            new Zco\Bundle\AuteursBundle\ZcoAuteursBundle(),
            new Zco\Bundle\BlogBundle\ZcoBlogBundle(),
            new Zco\Bundle\CaptchaBundle\ZcoCaptchaBundle(),
            new Zco\Bundle\CategoriesBundle\ZcoCategoriesBundle(),
            new Zco\Bundle\CitationsBundle\ZcoCitationsBundle(),
            new Zco\Bundle\DicteesBundle\ZcoDicteesBundle(),
            new Zco\Bundle\DonsBundle\ZcoDonsBundle(),
            new Zco\Bundle\EvolutionBundle\ZcoEvolutionBundle(),
            new Zco\Bundle\ForumBundle\ZcoForumBundle(),
            new Zco\Bundle\GroupesBundle\ZcoGroupesBundle(),
            new Zco\Bundle\InformationsBundle\ZcoInformationsBundle(),
            new Zco\Bundle\IpsBundle\ZcoIpsBundle(),
            new Zco\Bundle\MpBundle\ZcoMpBundle(),
            new Zco\Bundle\OptionsBundle\ZcoOptionsBundle(),
            new Zco\Bundle\QuizBundle\ZcoQuizBundle(),
            new Zco\Bundle\RechercheBundle\ZcoRechercheBundle(),
            new Zco\Bundle\RecrutementBundle\ZcoRecrutementBundle(),
            new Zco\Bundle\SondagesBundle\ZcoSondagesBundle(),
            new Zco\Bundle\StatistiquesBundle\ZcoStatistiquesBundle(),
            new Zco\Bundle\TagsBundle\ZcoTagsBundle(),
            new Zco\Bundle\FileBundle\ZcoFileBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
        }

        return $bundles;
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        if (is_file(__DIR__ . '/config/config_' . $this->getEnvironment() . '.yml')) {
            $loader->load(__DIR__ . '/config/config_' . $this->getEnvironment() . '.yml');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheDir()
    {
        return getenv('SYMFONY_CACHE_DIR') ?: parent::getCacheDir();
    }


    /**
     * {@inheritdoc}
     */
    public function getLogDir()
    {
        return getenv('SYMFONY_LOG_DIR') ?: parent::getLogDir();
    }
}
