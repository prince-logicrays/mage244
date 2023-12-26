<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-search-ultimate
 * @version   2.2.6
 * @copyright Copyright (C) 2023 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);

namespace Mirasvit\Search\Controller\Adminhtml\ScoreRule;

use Magento\Framework\App\ObjectManager;
use Mirasvit\Search\Controller\Adminhtml\AbstractScoreRule;
use Mirasvit\Search\Model\ScoreRule\Indexer\ScoreRuleIndexer;

class ApplyAll extends AbstractScoreRule
{
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $objectManager = ObjectManager::getInstance();

            /** @var ScoreRuleIndexer $scoreRuleIndexer */
            $scoreRuleIndexer = $objectManager->create(ScoreRuleIndexer::class);
            $scoreRuleIndexer->executeFull();

            $this->messageManager->addSuccessMessage((string)__('All rules were applied..'));

            return $resultRedirect->setPath('*/*/');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());

            return $resultRedirect->setPath('*/*/');
        }
    }
}
