<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Test\Gmagick;

use Imagine\Gmagick\Imagine;
use Imagine\Test\Draw\AbstractDrawerTest;

/**
 * @group gmagick
 */
class DrawerTest extends AbstractDrawerTest
{
    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\ImagineTestCaseBase::setUpBase()
     */
    protected function setUpBase()
    {
        parent::setUpBase();

        if (!class_exists('Gmagick')) {
            $this->markTestSkipped('Gmagick is not installed');
        }
    }

    protected function getImagine()
    {
        return new Imagine();
    }

    protected function isFontTestSupported()
    {
        return true;
    }
}
