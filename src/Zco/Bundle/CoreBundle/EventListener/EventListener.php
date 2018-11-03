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

namespace Zco\Bundle\CoreBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Zco\Bundle\CategoriesBundle\Domain\CategoryDAO;

/**
 * Subscriber principal du module central du site.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class EventListener implements EventSubscriberInterface
{
    use ContainerAwareTrait;

    private $maintenance = false;

    /**
     * {@inheritdoc}
     */
    static public function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array('onKernelRequest', 33),
            /* Le RouterListener par défaut a une priorité de 32, on se place juste avant */
        );
    }

    /**
     * Détermine le contrôleur à utiliser en fonction de la requête entrante.
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        //On n'est pas sur une page correspondant à un format standard d'URL
        //géré par ce listener, ou bien le routage a déjà été fait.
        if (!isset($_GET['page']) || $request->attributes->has('_controller')) {
            return;
        }

        //Initialisation des variables pour zCorrecteurs::VerifierFormatageUrl().
        !isset($_GET['id']) && $_GET['id'] = '';
        !isset($_GET['id2']) && $_GET['id2'] = '';
        !isset($_GET['p']) && $_GET['p'] = '';
        !isset($_GET['titre']) && $_GET['titre'] = '';

        //On sélectionne le module et l'action en fonction des segments de l'URL.
        if (!empty($_GET['page'])) {
            $module = $_GET['page'];
            $action = (!empty($_GET['act'])) ? $_GET['act'] : 'index';
        } else {
            $module = 'accueil';
            $action = 'index';
        }

        $action = str_replace('-', '_', $action);
        $camelizedAction = $this->camelize($action);

        //On vérifie qu'un bundle au nom du module existe bien. Par convention,
        //tous les bundles directement liés au site de zCorrection et gérés par
        //ce listener ne sont pas préfixés par un nom de vendor.
        $bundle = 'Zco' . ucfirst($module) . 'Bundle';
        if (!array_key_exists($bundle, $this->container->getParameter('kernel.bundles'))) {
            return;
        }
        $bundle = $this->container->get('kernel')->getBundle($bundle);

        //Cas 1 : on tente de charger un contrôleur à sa place dans le bon
        //espace de noms.
        $namespacedAction1 = $bundle->getNamespace() . '\\Controller\\' . $camelizedAction . 'Controller';
        $namespacedAction2 = $bundle->getNamespace() . '\\Controller\\DefaultController';
        if (class_exists($namespacedAction1)) {
            $class = $namespacedAction1;
            $method = 'defaultAction';
        } elseif (class_exists($namespacedAction2)) {
            $class = $namespacedAction2;
            $method = lcfirst($camelizedAction) . 'Action';
        } else {
            //Les contrôleurs ne sont pas (encore) contenus dans des namespaces et
            //doivent donc être trouvés et inclus à la main.
            if (is_file($bundle->getPath() . '/Controller/actions.class.php')) {
                include_once($bundle->getPath() . '/Controller/actions.class.php');
            }

            if (is_file($bundle->getPath() . '/Controller/' . $camelizedAction . 'Action.class.php')) {
                $class = $camelizedAction . 'Action';
                include_once($bundle->getPath() . '/Controller/' . $class . '.class.php');
                $method = 'execute';
            } elseif (class_exists(ucfirst($module) . 'Actions')) {
                $class = ucfirst($module) . 'Actions';
                $method = 'execute' . $camelizedAction;
            }
        }

        //On définit l'attribut _controller de la requête pour recoller avec le
        //comportement standard de Symfony, ainsi que quelques autres pour des
        //raisons de compatibilité.
        if (isset($class) && isset($method) && method_exists($class, $method)) {
            $request->attributes->set('_controller', $class . '::' . $method);
            $request->attributes->set('_module', $module);
            $request->attributes->set('_action', $action);
            $request->attributes->set('_bundle', $bundle->getName());

            unset($_GET['page'], $_GET['act']);

            $this->populateDefaultMeta();
        }
    }

    /**
     * S'occupe d'affecter un titre et une description par défaut à la page
     * à partir des informations sur la catégorie courante. Doit être fait
     * AVANT l'exécution du code du contrôleur car certains font des
     * \Page::$titre .= 'xxx' mais après le choix du contrôleur (pour connaître
     * la catégorie actuelle).
     */
    private function populateDefaultMeta()
    {
        if (!empty(\Page::$titre)) {
            return;
        }

        $IdCat = CategoryDAO::GetIDCategorieCourante(true);
        $InfosCategorie = CategoryDAO::InfosCategorie($IdCat);

        if (empty($InfosCategorie)) {
            return;
        }

        \Page::$titre = htmlspecialchars($InfosCategorie['cat_nom']);
        if (!empty($InfosCategorie['cat_description'])) {
            \Page::$titre .= ' - ' . htmlspecialchars($InfosCategorie['cat_description']);
        }
        \Page::$description = htmlspecialchars($InfosCategorie['cat_description']);

        // Si un des paramètres est toujours vide, on tente une récursion,
        //en remontant l'arbre.
        if (empty(\Page::$description)) {
            $Parents = CategoryDAO::ListerParents($IdCat, false);
            $Parents = array_reverse($Parents);
            foreach ($Parents as $cat) {
                if (empty(\Page::$description) && !empty($cat['cat_description'])) {
                    \Page::$description = htmlspecialchars($cat['cat_description']);
                    break;
                }
            }
        }
    }

    /**
     * Convertit une chaîne_avec_des_underscores en une chaîneEnCamelCase.
     *
     * @param  string $str
     * @return string La nouvelle chaîne
     */
    private function camelize($str)
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $str)));
    }
}