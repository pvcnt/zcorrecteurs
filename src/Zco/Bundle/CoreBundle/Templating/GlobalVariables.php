<?php

namespace Zco\Bundle\CoreBundle\Templating;

use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables as BaseGlobalVariables;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class GlobalVariables extends BaseGlobalVariables
{
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }

    public function googleAnalyticsAccount()
    {
        return $this->container->getParameter('analytics_account');
    }

    public function searchSection()
    {
        $module = $this->getRequest()->attributes->get('_module');

        return ($module === 'blog') ? 'blog' : 'forum';
    }

    public function adminCount()
    {
        static $count;
        if (!isset($count)) {
            $count = $this->container->get(\Zco\Bundle\AdminBundle\Admin::class)->count();
        }

        return $count;
    }

    public function randomQuoteHtml()
    {
        $cache = $this->container->get('cache');
        if (($html = $cache->fetch('header_citations')) === false) {
            $citation = $this->container->get('zco.repository.quotes')->getRandom();
            $html = '';
            if ($citation) {
                $html = render_to_string('ZcoContentBundle:Quotes:header.html.php', compact('citation'));
            }
            $cache->save('header_citations', $html, 3600);
        }

        return $html;
    }
}