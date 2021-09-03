<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\SemanticVersionChecker\Analyzer\Factory;

use Magento\SemanticVersionChecker\Analyzer\Analyzer;
use Magento\SemanticVersionChecker\Analyzer\AnalyzerInterface;
use Magento\SemanticVersionChecker\Analyzer\RoutersXml\RoutesAnalyzer;
use Magento\SemanticVersionChecker\ClassHierarchy\DependencyGraph;
use Magento\SemanticVersionChecker\DbSchemaReport;
use Magento\SemanticVersionChecker\Analyzer\WebApiXml\WebApiAnalyzer;

class WebApiAnalizerFactory implements AnalyzerFactoryInterface
{
    /**
     * @inheritdoc
     */
    public function create(DependencyGraph $dependencyGraph = null): AnalyzerInterface
    {
        $report = new DbSchemaReport();

        $analyzers = [
            new WebApiAnalyzer($report)
        ];

        return new Analyzer($analyzers);
    }
}
