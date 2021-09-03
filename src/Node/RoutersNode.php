<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\SemanticVersionChecker\Node;

class RoutersNode
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $frontName;

    /**
     * @var string
     */
    private $scope;

    /**
     * @param string $id
     * @param string $frontName
     * @param string $scope
     */
    public function __construct(string $id, string $frontName, string $scope)
    {
        $this->id = $id;
        $this->frontName = $frontName;
        $this->scope = $scope;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFrontName(): string
    {
        return $this->frontName;
    }

    /**
     * @return string
     */
    public function getScope(): string
    {
        return $this->scope;
    }
}
