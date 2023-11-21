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

namespace Goivvyllc\Indexer\Controller\Adminhtml\Index;

use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Indexer\IndexerRegistry;

class Index extends \Magento\Backend\App\Action implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'Goivvyllc_Indexer::index_button';
     
    private $_indexerRegistry;
     
    public function __construct(Context $context, IndexerRegistry $indexerRegistry)
    {
       $this->_indexerRegistry = $indexerRegistry;
       parent::__construct($context);     
    }

    public function execute()
    {
       $resultRedirect = $this->resultRedirectFactory->create();
       $indexer_id = $this->getRequest()->getParam('indexer_id');
       if($indexer_id){
           try{
            $index = $this->_indexerRegistry->get($indexer_id);
            if($index){
               $index->reindexAll();
               $this->messageManager->addSuccessMessage(__('The indexer has been reindexed')); 
            }else
               $this->messageManager->addErrorMessage(__('The indexer cannot be loaded'));               
           }catch(\Exception $e){
               $this->messageManager->addErrorMessage(__('There was an error reindexing the indexer'.$e->getMessage()));     
           }         
       }else
            $this->messageManager->addErrorMessage(__('I can\'t find an indexer to reindex.'));
       $resultRedirect->setPath('indexer/indexer/list');
       return $resultRedirect;
    }
}
