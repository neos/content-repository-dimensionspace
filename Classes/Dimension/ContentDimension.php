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

use Neos\Utility\Arrays;
use Neos\Flow\Annotations as Flow;

/**
 * The content dimension domain model
 */
#[Flow\Proxy(false)]
final class ContentDimension
{
    /**
     * all Content Dimension Values indexed by "specialization", so
     * you can answer questions like "what's the next-most generic value for the given value"
     *
     * @var array<string,ContentDimensionValue>
     */
    public readonly array $generalizations;

    /**
     * all Content Dimension Values indexed by "generalization", so
     * you can answer questions like "what are the next-most specialized values for the given value"
     *
     * returns an *array* of specializations for each key; so this is effectively an array of arrays
     *
     * @var array<string,array<string,ContentDimensionValue>>
     */
    public readonly array $specializations;

    /**
     * @param array<string,mixed> $configuration
     */
    public function __construct(
        public readonly ContentDimensionIdentifier $identifier,
        public readonly ContentDimensionValues $values,
        public readonly ContentDimensionValue $defaultValue,
        ContentDimensionValueVariationEdges $variationEdges,
        /** General configuration like UI, detection etc. */
        public readonly array $configuration = []
    ) {
        $generalizations = [];
        $specializations = [];
        foreach ($variationEdges as $variationEdge) {
            $generalizations[(string)$variationEdge->specialization] = $variationEdge->generalization;
            $specializations[(string)$variationEdge->generalization][(string)$variationEdge->specialization]
                = $variationEdge->specialization;
        }
        $this->generalizations = $generalizations;
        $this->specializations = $specializations;
    }

    public function getValue(string $value): ?ContentDimensionValue
    {
        return $this->values->getValue($value);
    }

    /**
     * @return array<string,ContentDimensionValue>
     */
    public function getRootValues(): array
    {
        return $this->values->getRootValues();
    }

    public function getGeneralization(ContentDimensionValue $dimensionValue): ?ContentDimensionValue
    {
        return $this->generalizations[(string)$dimensionValue] ?? null;
    }

    /**
     * @return array<string,ContentDimensionValue>
     */
    public function getSpecializations(ContentDimensionValue $dimensionValue): array
    {
        return $this->specializations[(string)$dimensionValue] ?? [];
    }

    public function traverseGeneralizations(ContentDimensionValue $dimensionValue, callable $callback): void
    {
        $callback($dimensionValue);
        if ($this->getGeneralization($dimensionValue)) {
            $this->traverseGeneralizations($this->getGeneralization($dimensionValue), $callback);
        }
    }

    /**
     * @throws Exception\GeneralizationIsInvalid
     */
    public function calculateSpecializationDepth(
        ContentDimensionValue $specialization,
        ContentDimensionValue $generalization
    ): ContentDimensionValueSpecializationDepth {
        $specializationDepth = 0;
        $currentGeneralization = $specialization;

        while ($currentGeneralization) {
            if ($currentGeneralization === $generalization) {
                return new ContentDimensionValueSpecializationDepth($specializationDepth);
            } else {
                $currentGeneralization = $this->getGeneralization($currentGeneralization);
                $specializationDepth++;
            }
        }

        throw Exception\GeneralizationIsInvalid::becauseComparedValueIsNoSpecialization(
            $generalization,
            $specialization,
            $this->identifier
        );
    }

    public function getConfigurationValue(string $path): mixed
    {
        $configuration = $this->configuration;

        return Arrays::getValueByPath($configuration, $path);
    }

    public function getMaximumDepth(): ContentDimensionValueSpecializationDepth
    {
        return $this->values->maximumDepth;
    }
}
