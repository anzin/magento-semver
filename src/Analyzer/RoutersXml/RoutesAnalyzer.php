<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\SemanticVersionChecker\Analyzer\RoutersXml;

use Magento\SemanticVersionChecker\Analyzer\AnalyzerInterface;
use Magento\SemanticVersionChecker\Node\RoutersNode;
use Magento\SemanticVersionChecker\Operation\RoutesXml\RoutesChanged;
use Magento\SemanticVersionChecker\Registry\XmlRegistry;
use PHPSemVerChecker\Registry\Registry;
use PHPSemVerChecker\Report\Report;

class RoutesAnalyzer implements AnalyzerInterface
{
    /**
     * @var Report
     */
    private $report;

    /**
     * @param Report $report
     */
    public function __construct(Report $report)
    {
        $this->report = $report;
    }

    /**
     * Compared registryBefore and registryAfter find changes for routers.
     *
     * @param XmlRegistry|Registry $registryBefore
     * @param XmlRegistry|Registry $registryAfter
     *
     * @return Report
     */
    public function analyze($registryBefore, $registryAfter): Report
    {
        $nodesBefore = $this->getRoutesNode($registryBefore);
        $nodesAfter = $this->getRoutesNode($registryAfter);

        if ($nodesBefore === $nodesAfter) {
            return $this->report;
        }

        foreach ($nodesBefore as $moduleName => $moduleScoup) {
            /* @var RoutersNode $nodeBefore */

            foreach ($moduleScoup as $scope => $nodes) {
                $fileBefore = $registryBefore->mapping[XmlRegistry::NODES_KEY][$moduleName][$scope];

                foreach ($nodes as $key => $nodeBefore) {
                    // search nodesAfter the by name
                    $nodeAfter = $nodesAfter[$moduleName][$scope][$key] ?? false;

                    if ($nodeAfter !== false && $nodeBefore !== $nodeAfter) {
                        /* @var RoutersNode $nodeAfter */
                        $this->triggerNodeChange($nodeBefore, $nodeAfter, $fileBefore);

                        continue;
                    }
                }
            }
        }

        return $this->report;
    }

    /**
     * Get routes nodes.
     *
     * @param XmlRegistry $xmlRegistry
     *
     * @return array
     */
    private function getRoutesNode(XmlRegistry $xmlRegistry): array
    {
        $routersNodeList = [];

        foreach ($xmlRegistry->getNodes() as $moduleName => $nodeList) {
            foreach ($nodeList as $node) {
                if ($node instanceof RoutersNode === false) {
                    continue;
                }

                /** @var RoutersNode $node */
                $routersNodeList[$moduleName][$node->getScope()][] = $node;
            }
        }

        return $routersNodeList;
    }

    /**
     * Add node changed to report.
     *
     * @param RoutersNode $nodeBefore
     * @param RoutersNode $nodeAfter
     * @param string $beforeFilePath
     */
    private function triggerNodeChange(RoutersNode $nodeBefore, RoutersNode $nodeAfter, string $beforeFilePath): void
    {
        $bcFieldBefore = [
            'id' => $nodeBefore->getId(),
            'frontName' => $nodeBefore->getFrontName()
        ];
        $bcFieldAfter = [
            'id' => $nodeBefore->getId(),
            'frontName' => $nodeAfter->getFrontName()
        ];

        if ($bcFieldBefore === $bcFieldAfter) {
            return;
        }

        foreach ($bcFieldBefore as $fieldName => $valueBefore) {
            $valueAfter = $bcFieldAfter[$fieldName];

            if ($valueBefore !== $valueAfter) {
                $operation = new RoutesChanged($beforeFilePath, $fieldName);
                $this->report->add('routes', $operation);
            }
        }
    }
}
