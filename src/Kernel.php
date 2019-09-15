<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2018 Corrigraphie
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

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as HttpKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

final class Kernel extends HttpKernel
{
    use MicroKernelTrait;

    const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        $bundles = array(
            // Vendor bundles.
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Zco\Bundle\Doctrine1Bundle\ZcoDoctrine1Bundle(),
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),

            // Infrastructure bundles.
            new Zco\Bundle\CoreBundle\ZcoCoreBundle(),
            new Zco\Bundle\VitesseBundle\ZcoVitesseBundle(),
            new Zco\Bundle\UserBundle\ZcoUserBundle(),

            // Module bundles.
            new Zco\Bundle\AdminBundle\ZcoAdminBundle(),
            new Zco\Bundle\PagesBundle\ZcoPagesBundle(),
            new Zco\Bundle\BlogBundle\ZcoBlogBundle(),
            new Zco\Bundle\CaptchaBundle\ZcoCaptchaBundle(),
            new Zco\Bundle\ContentBundle\ZcoContentBundle(),
            new Zco\Bundle\DicteesBundle\ZcoDicteesBundle(),
            new Zco\Bundle\ForumBundle\ZcoForumBundle(),
            new Zco\Bundle\GroupesBundle\ZcoGroupesBundle(),
            new Zco\Bundle\MpBundle\ZcoMpBundle(),
            new Zco\Bundle\OptionsBundle\ZcoOptionsBundle(),
            new Zco\Bundle\QuizBundle\ZcoQuizBundle(),
            new Zco\Bundle\FileBundle\ZcoFileBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
        }

        return $bundles;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader)
    {
        $confDir = __DIR__ . '/config';
        $loader->load($confDir . '/packages/*' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/packages/' . $this->environment . '/**/*' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/services' . self::CONFIG_EXTS, 'glob');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
        $confDir = __DIR__ . '/config';
        $routes->import($confDir . '/routes/*' . self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir . '/routes/' . $this->environment . '/**/*' . self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir . '/routes' . self::CONFIG_EXTS, '/', 'glob');
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheDir()
    {
        return (getenv('SYMFONY_CACHE_DIR') ?: $this->getProjectDir() . '/var/cache') . '/' . $this->environment;
    }

    /**
     * {@inheritdoc}
     */
    public function getLogDir()
    {
        return getenv('SYMFONY_LOG_DIR') ?: $this->getProjectDir() . '/var/log';
    }

    /**
     * {@inheritdoc}
     */
    public function getRootDir()
    {
        // Default implementation uses reflection, this should be faster.
        return __DIR__;
    }

    /**
     * {@inheritdoc}
     */
    public function getProjectDir()
    {
        // Default implementation uses reflection, this should be faster.
        return __DIR__ . '/..';
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
