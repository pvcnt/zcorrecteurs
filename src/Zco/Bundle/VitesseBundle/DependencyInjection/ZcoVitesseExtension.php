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

namespace Zco\Bundle\VitesseBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Zco\Bundle\VitesseBundle\Javelin\DocblockParser;
use Zco\Bundle\VitesseBundle\Javelin\ResourceGraph;

/**
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class ZcoVitesseExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $config = $this->processConfiguration(new Configuration(), $configs);

        $cacheDir = $container->getParameterBag()->resolveValue('%kernel.cache_dir%/vitesse');
        $container->setParameter('zco_core.vitesse.cache_dir', $cacheDir);
        $container->setParameter('zco_core.vitesse.combine_assets', $config['combine_assets']);

        $definition = $container->getDefinition('zco_core.assetic.asset_manager');
        $resourceGraph = array();
        $aliases = array();

        // Crée le graph des ressources.
        foreach ($container->getParameter('kernel.bundles') as $bundle => $className) {
            $rc = new \ReflectionClass($className);
            $publicDir = dirname($rc->getFileName()) . '/Resources/public';
            if (!is_dir($publicDir)) {
                continue;
            }

            $finder = Finder::create()
                ->files()
                ->name('/\.js$/')
                ->name('/\.css$/')
                ->followLinks()
                ->in($publicDir);

            $i = 0;
            foreach ($finder as $file) {
                /** @var \SplFileInfo $file */
                $type = $file->getExtension();
                $defaultProvides = str_replace(DIRECTORY_SEPARATOR, '/', sprintf('@%s/Resources/public%s',
                    $bundle,
                    preg_replace('@^' . preg_quote($publicDir, '@') . '@', '', $file->getPathname())
                ));

                if (!preg_match('@/[*][*].*?[*]/@s', file_get_contents($file->getRealPath()), $matches)) {
                    $provides = $defaultProvides;
                    $requires = array();
                } else {
                    $parser = new DocblockParser();
                    list(, $metadata) = $parser->parse($matches[0]);

                    $provides = preg_split('/\s+/', trim(isset($metadata['provides']) ? $metadata['provides'] : $defaultProvides));
                    $requires = preg_split('/\s+/', trim(isset($metadata['requires']) ? $metadata['requires'] : ''));
                    $provides = array_filter($provides);
                    $requires = array_filter($requires);

                    if (count($provides) > 1) {
                        // NOTE: Documentation-only JS is permitted to @provide no targets.
                        throw new \InvalidArgumentException(sprintf(
                            'File "%s" must @provide at most one Vitesse target.',
                            $file->getPath()
                        ));
                    }

                    $provides = reset($provides);
                }

                $assetName = sha1($provides) . '_' . $type;
                $serviceName = 'zco_core.assetic.asset.' . $bundle . '_' . (++$i);

                $container->setDefinition(
                    $serviceName,
                    new Definition('Assetic\Asset\FileAsset', array($file->getRealpath()))
                );
                $container->addResource(new FileResource($file->getRealPath()));
                $definition->addMethodCall('set', array($assetName, new Reference($serviceName)));
                $resourceGraph[$assetName] = $requires;

                $aliases[$provides] = $assetName;
                if ($provides !== $defaultProvides) {
                    $aliases[$defaultProvides] = $assetName;
                }
            }
        }

        foreach ($resourceGraph as $name => $requires) {
            foreach ($requires as $i => $req) {
                if (!isset($aliases[$req])) {
                    unset($resourceGraph[$name][$i]);
                } else {
                    $resourceGraph[$name][$i] = $aliases[$req];
                }
            }
        }

        $graph = new ResourceGraph();
        $graph->addNodes($resourceGraph);
        $graph->setResourceGraph($resourceGraph);
        $graph->loadGraph();

        foreach ($resourceGraph as $provides => $requires) {
            $cycle = $graph->detectCycles($provides);
            if ($cycle) {
                throw new \RuntimeException(sprintf(
                    'Cycle detected in resource graph: %s.',
                    implode($cycle, ' => ')
                ));
            }
        }

        ksort($resourceGraph);
        $resourceGraph = var_export($resourceGraph, true);
        $resourceGraph = preg_replace('/\s+$/m', '', $resourceGraph);
        $resourceGraph = preg_replace('/array \(/', 'array(', $resourceGraph);

        ksort($aliases);
        $aliases = var_export($aliases, true);
        $aliases = preg_replace('/\s+$/m', '', $aliases);
        $aliases = preg_replace('/array \(/', 'array(', $aliases);

        if (!is_dir($cacheDir)) {
            if (false === @mkdir($cacheDir, 0777, true)) {
                throw new \RuntimeException(sprintf('Unable to create the Vitesse cache directory "%s".', $cacheDir));
            }
        } elseif (!is_writable($cacheDir)) {
            throw new \RuntimeException(sprintf('The Vitesse cache directory "%s" is not writeable for the current system user.', $cacheDir));
        }

        file_put_contents($cacheDir . '/resourceGraph.php', '<?php' . "\n" . 'return ' . $resourceGraph . ';');
        file_put_contents($cacheDir . '/aliases.php', '<?php' . "\n" . 'return ' . $aliases . ';');
    }
}
