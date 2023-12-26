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

use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Serialize\Serializer\Json;

class Module
{
    /**
     * @var Data
     */
    private $logicraysHelper;

    /**
     * @var array|null
     */
    private $modules = null;

    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var Reader
     */
    private $moduleReader;

    /**
     * @var File
     */
    private $filesystem;

    /**
     * @var Json
     */
    private $json;

    /**
     * Module constructor.
     * @param Data $logicraysHelper
     * @param ModuleListInterface $moduleList
     * @param DataObjectFactory $dataObjectFactory
     * @param Reader $moduleReader
     * @param File $filesystem
     * @param Json $json
     */
    public function __construct(
        Data $logicraysHelper,
        ModuleListInterface $moduleList,
        DataObjectFactory $dataObjectFactory,
        Reader $moduleReader,
        File $filesystem,
        Json $json
    ) {
        $this->logicraysHelper = $logicraysHelper;
        $this->moduleList = $moduleList;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->moduleReader = $moduleReader;
        $this->filesystem = $filesystem;
        $this->json = $json;
    }

    /**
     * GetLogicraysModules function
     *
     * @return array
     */
    public function getLogicraysModules()
    {
        $modules = $this->moduleList->getNames();
        $dispatchResult = $this->dataObjectFactory->create()->setData($modules);
        $modules = $dispatchResult->toArray();
        sort($modules);
        $result = [];
        foreach ($modules as $moduleName) {
            if (strstr($moduleName, 'Logicrays_') === false
                || $moduleName === 'Logicrays_Base'
            ) {
                continue;
            }
            $result[] = $moduleName;
        }

        return $result;
    }

    /**
     * Get installed module info by composer.json.
     *
     * @param string $moduleCode
     * @return array|bool|float|int|mixed|string|null
     */
    public function getLocalModuleInfo($moduleCode)
    {
        try {
            $dir = $this->moduleReader->getModuleDir('', $moduleCode);
            $file = $dir . '/composer.json';
            $string = $this->filesystem->fileGetContents($file);
            $result = $this->json->unserialize($string);
            if (!is_array($result)
                || !array_key_exists('version', $result)
                || !array_key_exists('description', $result)
            ) {
                return '';
            }
            return $result;
        } catch (\Exception $e) {
            return [];
        }
    }
}
