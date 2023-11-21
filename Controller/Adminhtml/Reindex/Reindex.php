<?php
/**
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to sales@goivvy.com so we can send you a copy immediately.
 *
 * @component  Goivvyllc_Indexer
 * @copyright  Copyright (c) 2023 GOIVVY LLC (https://www.goivvy.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Goivvy.com <sales@goivvy.com>
 */

namespace Goivvyllc\Indexer\Controller\Adminhtml\Reindex;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\Indexer\IndexerRegistry;

class Reindex extends \Magento\Backend\App\Action implements HttpPostActionInterface
{
    const ADMIN_RESOURCE = 'Goivvyllc_Indexer::index_action';
    
    private $_indexerRegistry;
     
    public function __construct(Context $context,IndexerRegistry $indexerRegistry) 
    {
       $this->_indexerRegistry = $indexerRegistry;
       parent::__construct($context);     
    }

    public function execute()
    {
        $indexerIds = $this->getRequest()->getParam('indexer_ids');
        if (!is_array($indexerIds)) {
            $this->messageManager->addErrorMessage(__('Please select indexers.'));
        } else {
            try {
                foreach ($indexerIds as $indexerId) {
                    $model = $this->_indexerRegistry->get($indexerId);
                    $model->reindexAll();
                }
                $this->messageManager->addSuccess(
                    __('%1 indexer(s) were reindexed.', count($indexerIds))
                );
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException(
                    $e,
                    __("We couldn't reindex indexer(s) because of an error.")
                );
            }
        }
        return $this->resultRedirectFactory->create()->setPath('indexer/indexer/list');
    }
}
