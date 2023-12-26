<?php
/**
 * Logicrays
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Logicrays
 * @package     Logicrays_Base
 * @copyright   Copyright (c) Logicrays (https://www.logicrays.com/)
 */
declare(strict_types=1);

namespace Logicrays\Base\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    /**
     * IsModuleEnable
     *
     * @param string $moduleName
     * @return bool
     */
    public function isModuleEnable($moduleName)
    {
        return $this->_moduleManager->isEnabled($moduleName);
    }
}
