<?php

/*
 * This file is part of the Neos.ContentRepository.DimensionSpace package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

declare(strict_types=1);

namespace Neos\ContentRepository\DimensionSpace\Dimension;

use Neos\Flow\Annotations as Flow;

/**
 * A directed edge connecting two dimension space points declaring them specialization and generalization
 */
#[Flow\Proxy(false)]
final class ContentDimensionValueVariationEdge
{
    public function __construct(
        public readonly ContentDimensionValue $specialization,
        public readonly ContentDimensionValue $generalization,
    ) {
    }
}
