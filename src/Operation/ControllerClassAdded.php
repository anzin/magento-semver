<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\SemanticVersionChecker\Operation;

use PHPSemVerChecker\Configuration\LevelMapping;
use PHPSemVerChecker\Operation\ClassOperationUnary;
use PHPSemVerChecker\SemanticVersioning\Level;

class ControllerClassAdded extends ClassOperationUnary
{
    /**
     * Error codes.
     *
     * @var array
     */
    protected $code = 'C102';

    /**
     * Operation message.
     *
     * @var string
     */
    protected $reason = 'Controller class added';
}
