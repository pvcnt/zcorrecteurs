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

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Zco\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    /**
     * {@inheritdoc}
     */
    public function __construct($environment, $debug = false)
    {
        parent::__construct($environment, $debug);
        date_default_timezone_set('Europe/Paris');
        setlocale(LC_ALL, 'fr_FR.UTF-8');
        mb_internal_encoding('UTF-8');
    }

    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        $bundles = array(
            //Bundles génériques.
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Zco\Bundle\Doctrine1Bundle\ZcoDoctrine1Bundle(),
            new Zco\Bundle\VitesseBundle\ZcoVitesseBundle(),
            new Knp\Bundle\GaufretteBundle\KnpGaufretteBundle(),
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            new Bazinga\Bundle\GeocoderBundle\BazingaGeocoderBundle(),

            //Bundles nécessaires pour que les modules fonctionnent.
            new Zco\Bundle\CoreBundle\ZcoCoreBundle(),
            new Zco\Bundle\ParserBundle\ZcoParserBundle(),
            new Zco\Bundle\UserBundle\ZcoUserBundle(),

            //Modules du site.
            new Zco\Bundle\AdminBundle\ZcoAdminBundle(),
            new Zco\Bundle\PagesBundle\ZcoPagesBundle(),
            new Zco\Bundle\AuteursBundle\ZcoAuteursBundle(),
            new Zco\Bundle\BlogBundle\ZcoBlogBundle(),
            new Zco\Bundle\CaptchaBundle\ZcoCaptchaBundle(),
            new Zco\Bundle\CategoriesBundle\ZcoCategoriesBundle(),
            new Zco\Bundle\CitationsBundle\ZcoCitationsBundle(),
            new Zco\Bundle\DicteesBundle\ZcoDicteesBundle(),
            new Zco\Bundle\ForumBundle\ZcoForumBundle(),
            new Zco\Bundle\GroupesBundle\ZcoGroupesBundle(),
            new Zco\Bundle\MpBundle\ZcoMpBundle(),
            new Zco\Bundle\OptionsBundle\ZcoOptionsBundle(),
            new Zco\Bundle\QuizBundle\ZcoQuizBundle(),
            new Zco\Bundle\SearchBundle\ZcoSearchBundle(),
            new Zco\Bundle\RecrutementBundle\ZcoRecrutementBundle(),
            new Zco\Bundle\SondagesBundle\ZcoSondagesBundle(),
            new Zco\Bundle\StatsBundle\ZcoStatsBundle(),
            new Zco\Bundle\TagsBundle\ZcoTagsBundle(),
            new Zco\Bundle\TwitterBundle\ZcoTwitterBundle(),
            new Zco\Bundle\FileBundle\ZcoFileBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
        }

        return $bundles;
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config/config_' . $this->getEnvironment() . '.yml');

        // Environment variables should overwrite default parameters.
        // See: https://github.com/symfony/symfony/issues/7555#issuecomment-15856713
        // TODO: remove this when we migrate to Symfony 3.2
        $envParameters = $this->getEnvParameters();
        $loader->load(function (ContainerBuilder $container) use ($envParameters) {
            $container->getParameterBag()->add($envParameters);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheDir()
    {
        return (rtrim(getenv('SYMFONY_CACHE_DIR'), '/') ?: '/var/cache/symfony') . '/' . $this->environment;
    }

    /**
     * {@inheritdoc}
     */
    public function getLogDir()
    {
        return getenv('SYMFONY_LOG_DIR') ?: '/var/log/symfony';
    }

    protected function getEnvParameters()
    {
        // We override the default implementation to take into account $_ENV and not only $_SERVER, because
        // apparently Apache forwards environment variables into $_ENV and not $_SERVER. $_SERVER only contains
        // variable manually set with Apache's SetEnv directive.
        $parameters = array();
        foreach (array_merge($_ENV, $_SERVER) as $key => $value) {
            if (0 === strpos($key, 'SYMFONY__')) {
                $parameters[strtolower(str_replace('__', '.', substr($key, 9)))] = $value;
            }
        }

        return $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        parent::boot();
        foreach ($this->bundles as $name => $bundle) {
            if ($bundle instanceof AbstractBundle) {
                $bundle->preload();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function initializeContainer()
    {
        parent::initializeContainer();
        \Container::setInstance($this->container);
    }
}
