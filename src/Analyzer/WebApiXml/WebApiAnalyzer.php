<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\SemanticVersionChecker\Analyzer\WebApiXml;

use Magento\SemanticVersionChecker\Analyzer\AnalyzerInterface;
use Magento\SemanticVersionChecker\Node\WebApiNode;
use Magento\SemanticVersionChecker\Operation\WebApiXml\WebApiChanged;
use Magento\SemanticVersionChecker\Registry\XmlRegistry;
use PHPSemVerChecker\Registry\Registry;
use PHPSemVerChecker\Report\Report;

class WebApiAnalyzer implements AnalyzerInterface
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
     * Compared registryBefore and registryAfter find changes for webapi.
     *
     * @param XmlRegistry|Registry $registryBefore
     * @param XmlRegistry|Registry $registryAfter
     *
     * @return Report
     */
    public function analyze($registryBefore, $registryAfter): Report
    {
        $nodesBefore = $this->getWebApiNode($registryBefore);
        $nodesAfter = $this->getWebApiNode($registryAfter);

        if ($nodesBefore === $nodesAfter) {
            return $this->report;
        }

        foreach ($nodesBefore as $moduleName => $moduleNodes) {
            /* @var WebApiNode $nodeBefore */
            $fileBefore = $registryBefore->mapping[XmlRegistry::NODES_KEY][$moduleName];

            foreach ($moduleNodes as $key => $nodeBefore) {
                // search nodesAfter the by name
                $nodeAfter = $nodesAfter[$moduleName][$key] ?? false;

                if ($nodeAfter !== false && $nodeBefore !== $nodeAfter) {
                    /* @var WebApiNode $nodeAfter */
                    $this->triggerNodeChange($nodeBefore, $nodeAfter, $fileBefore);
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
    private function getWebApiNode(XmlRegistry $xmlRegistry): array
    {
        $webApiNodeList = [];

        foreach ($xmlRegistry->getNodes() as $moduleName => $nodeList) {
            foreach ($nodeList as $node) {
                if ($node instanceof WebApiNode === false) {
                    continue;
                }

                /** @var RoutersNode $node */
                $webApiNodeList[$moduleName][] = $node;
            }
        }

        return $webApiNodeList;
    }

    /**
     * Add node changed to report.
     *
     * @param WebApiNode $nodeBefore
     * @param WebApiNode $nodeAfter
     * @param string $beforeFilePath
     */
    private function triggerNodeChange(WebApiNode $nodeBefore, WebApiNode $nodeAfter, string $beforeFilePath): void
    {
        $bcFieldBefore = [
            'url' => $nodeBefore->getUrl(),
            'method' => $nodeBefore->getMethod()
        ];
        $bcFieldAfter = [
            'url' => $nodeAfter->getUrl(),
            'method' => $nodeAfter->getMethod()
        ];

        if ($bcFieldBefore === $bcFieldAfter) {
            return;
        }

        foreach ($bcFieldBefore as $fieldName => $valueBefore) {
            $valueAfter = $bcFieldAfter[$fieldName];

            if ($valueBefore !== $valueAfter) {
                $operation = new WebApiChanged($beforeFilePath, $fieldName);
                $this->report->add('webapi', $operation);
            }
        }
    }
}
