<?php

namespace Neos\ContentRepository\DimensionSpace\Tests\Unit\Dimension;

/*
 * This file is part of the Neos.ContentRepository.DimensionSpace package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */
use Neos\ContentRepository\DimensionSpace\Dimension;
use Neos\ContentRepository\DimensionSpace\Dimension\Exception\ContentDimensionValueIsInvalid;
use Neos\Flow\Tests\UnitTestCase;

/**
 * Test cases for content dimension values
 */
class ContentDimensionValueTest extends UnitTestCase
{
    /**
     * @test
     */
    public function initializationThrowsExceptionForEmptyValue()
    {
        $this->expectException(ContentDimensionValueIsInvalid::class);
        new Dimension\ContentDimensionValue('');
    }

    /**
     * @test
     */
    public function specializationDepthDefaultsToZero()
    {
        $subject = new Dimension\ContentDimensionValue('value');

        $this->assertEquals(
            new Dimension\ContentDimensionValueSpecializationDepth(0),
            $subject->getSpecializationDepth()
        );
    }
}
