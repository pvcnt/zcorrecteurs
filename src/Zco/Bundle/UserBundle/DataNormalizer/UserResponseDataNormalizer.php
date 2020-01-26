<?php

namespace Zco\Bundle\UserBundle\DataNormalizer;

use Zco\Bundle\CoreBundle\Filesystem\UrlResolver;

final class UserResponseDataNormalizer
{
    private $urlResolver;

    /**
     * Constructor.
     *
     * @param UrlResolver $urlResolver
     */
    public function __construct(UrlResolver $urlResolver)
    {
        $this->urlResolver = $urlResolver;
    }

    public function normalize(array $data)
    {
        $data['avatar_url'] = $this->urlResolver->resolveUrl($data['utilisateur_avatar']);

        return $data;
    }
}