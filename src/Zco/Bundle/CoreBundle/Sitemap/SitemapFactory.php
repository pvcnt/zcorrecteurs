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

namespace Zco\Bundle\CoreBundle\Sitemap;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Zco\Bundle\ContentBundle\Domain\BlogDAO;
use Zco\Bundle\ContentBundle\Domain\ForumDAO;
use Zco\Bundle\ContentBundle\Domain\TopicDAO;
use Zco\Bundle\ContentBundle\Entity\QuizManager;
use Zco\Container;

final class SitemapFactory
{
    private $router;

    /**
     * Constructor.
     *
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function createSitemap(): Sitemap
    {
        $links = [];

        $links[] = new SitemapLink($this->generateUrl('zco_blog_index'), [
            'changefreq' => 'weekly',
            'priority' => '0.6',
        ]);
        foreach (BlogDAO::ListerBilletsId() as $billet) {
            $url = $this->generateUrl('zco_blog_show', ['id' => $billet['blog_id'], 'slug' => rewrite($billet['version_titre'])]);
            $links[] = new SitemapLink($url, [
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ]);
        }

        $links[] = new SitemapLink($this->generateUrl('zco_dictation_index'), array(
            'changefreq' => 'weekly',
            'priority' => '0.6',
        ));
        foreach (\Doctrine_Core::getTable('Dictee')->getAllId() as $dictee) {
            $url = $this->generateUrl('zco_dictation_show', ['id' => $dictee['id'], 'slug' =>  rewrite($dictee['titre'])]);
            $links[] = new SitemapLink($url, array(
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ));
        }

        $links[] = new SitemapLink($this->generateUrl('zco_forum_index'), array(
            'changefreq' => 'daily',
            'priority' => '0.7',
        ));
        foreach (ForumDAO::ListerSujetsId(array(34, 45, 42, 43, 44, 46, 47, 91, 92, 93, 94, 178)) as $topic) {
            if (!TopicDAO::sujetIsArchive($topic['sujet_id'])) {
                $url = $this->generateUrl('zco_topic_show', ['id' => $topic['sujet_id'], 'slug' => rewrite($topic['sujet_titre'])]);
                $links[] = new SitemapLink($url, array(
                    'changefreq' => 'weekly',
                    'priority' => '0.5',
                ));
            }
        }

        $links[] = new SitemapLink($this->generateUrl('zco_about_index'), [
            'changefreq' => 'monthly',
            'priority' => '0.3',
        ]);
        $links[] = new SitemapLink($this->generateUrl('zco_about_contact'), [
            'changefreq' => 'monthly',
            'priority' => '0.3',
        ]);
        $links[] = new SitemapLink($this->generateUrl('zco_about_team'), [
            'changefreq' => 'monthly',
            'priority' => '0.3',
        ]);
        $links[] = new SitemapLink($this->generateUrl('zco_about_corrigraphie'), [
            'changefreq' => 'monthly',
            'priority' => '0.3',
        ]);
        $links[] = new SitemapLink($this->generateUrl('zco_about_banners'), [
            'changefreq' => 'monthly',
            'priority' => '0.3',
        ]);
        $links[] = new SitemapLink($this->generateUrl('zco_about_opensource'), [
            'changefreq' => 'monthly',
            'priority' => '0.3',
        ]);

        $links[] = new SitemapLink($this->generateUrl('zco_donate_index'), [
            'changefreq' => 'monthly',
            'priority' => '0.2',
        ]);
        $links[] = new SitemapLink($this->generateUrl('zco_donate_otherWays'), [
            'changefreq' => 'monthly',
            'priority' => '0.2',
        ]);
        $links[] = new SitemapLink($this->generateUrl('zco_donate_fiscalDeduction'), [
            'changefreq' => 'monthly',
            'priority' => '0.2',
        ]);

        $links[] = new SitemapLink($this->generateUrl('zco_legal_mentions'), [
            'changefreq' => 'monthly',
            'priority' => '0.2',
        ]);
        $links[] = new SitemapLink($this->generateUrl('zco_legal_privacy'), [
            'changefreq' => 'monthly',
            'priority' => '0.2',
        ]);
        $links[] = new SitemapLink($this->generateUrl('zco_legal_rules'), [
            'changefreq' => 'monthly',
            'priority' => '0.2',
        ]);

        $links[] = new SitemapLink($this->generateUrl('zco_home'), [
            'changefreq' => 'daily',
            'priority' => '0.9',
        ]);

        $links[] = new SitemapLink($this->generateUrl('zco_quiz_index'), [
            'changefreq' => 'weekly',
            'priority' => '0.6',
        ]);
        $list = Container::get(QuizManager::class)->lister();
        foreach ($list as $quiz) {
            $url = $this->generateUrl('zco_quiz_show', ['id' => $quiz['id'], 'slug' => rewrite($quiz['nom'])]);
            $links[] = new SitemapLink($url, array(
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ));
        }

        $links[] = new SitemapLink($this->generateUrl('zco_search_index'), [
            'changefreq' => 'monthly',
            'priority' => '0.5',
        ]);

        $links[] = new SitemapLink($this->generateUrl('zco_user_session_register'), [
            'changefreq' => 'monthly',
            'priority' => '0.5',
        ]);
        $links[] = new SitemapLink($this->generateUrl('zco_user_session_login'), [
            'changefreq' => 'monthly',
            'priority' => '0.5',
        ]);

        return new Sitemap($links);
    }

    private function generateUrl(string $name, array $parameters = []): string
    {
        return $this->router->generate($name, $parameters, UrlGeneratorInterface::ABSOLUTE_URL);
    }
}