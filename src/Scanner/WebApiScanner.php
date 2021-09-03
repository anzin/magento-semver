<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\SemanticVersionChecker\Scanner;

use DOMDocument;
use DOMNodeList;
use Magento\SemanticVersionChecker\Registry\XmlRegistry;
use PHPSemVerChecker\Registry\Registry;
use Magento\SemanticVersionChecker\Node\WebApiNode;

class WebApiScanner implements ScannerInterface
{
    /**
     * @var XmlRegistry
     */
    private $registry;

    /**
     * @var ModuleNamespaceResolver
     */
    private $getModuleNameByPath;

    /**
     * @param XmlRegistry $registry
     * @param ModuleNamespaceResolver $getModuleNameByPath
     */
    public function __construct(XmlRegistry $registry, ModuleNamespaceResolver $getModuleNameByPath)
    {
        $this->registry = $registry;
        $this->getModuleNameByPath = $getModuleNameByPath;
    }

    /**
     * @inheritdoc
     */
    public function scan(string $file): void
    {
        $doc = new DOMDocument();
        $doc->loadXML(file_get_contents($file));
        $this->registerRouterNode($doc->getElementsByTagName('route'), $file);
    }

    /**
     * @inheritdoc
     */
    public function getRegistry(): Registry
    {
        return $this->registry;
    }

    /**
     * Registration router node.
     *
     * @param DOMNodeList $virtualTypeNodes
     * @param string $fileName
     *
     * @return void
     */
    private function registerRouterNode(DOMNodeList $webapiNodes, string $fileName): void
    {
        $moduleName = $this->getModuleNameByPath->resolveByEtcDirFilePath($fileName);
        $this->getRegistry()->mapping[XmlRegistry::NODES_KEY][$moduleName] = $fileName;

        foreach ($webapiNodes as $node) {
            $url = $node->getAttribute('url');
            $method = $node->getAttribute('method');
            $this->registry->addXmlNode($moduleName, new WebApiNode($url, $method));
        }
    }
}
