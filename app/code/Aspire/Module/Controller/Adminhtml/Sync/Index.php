<?php

namespace Aspire\Module\Controller\Adminhtml\Sync;

use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Aspire\Module\Block\Adminhtml\ApiCall;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Controller\ResultFactory;

class Index extends Action
{
    protected $_coreRegistry = null;
    protected $resultPageFactory;
    protected $CollectionFactory;
    protected $apiCall;
    protected $customerRepositoryInterface;
    /**
  * @var \Magento\Framework\App\Config\ScopeConfigInterface
  */
  protected $scopeConfig;
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $registry,
        Filter $filter,
        CollectionFactory $CollectionFactory,
        ScopeConfigInterface $scopeConfig,
        ApiCall $apiCall,
        CustomerRepositoryInterface $customerRepositoryInterface
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->CollectionFactory = $CollectionFactory;
        $this->filter = $filter;
        $this->scopeConfig = $scopeConfig;
        $this->apiCall = $apiCall;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        parent::__construct($context);
    }
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->CollectionFactory->create());
            foreach ($collection as $child) {
                if ($child->getData('entity_id')) {
                   $result = $this->apiCall->getApiCustomerStatus($child->getData('entity_id'));
                    $child->getData('entity_id');
                    $customer = $this->customerRepositoryInterface->getById($child->getData('entity_id'));
                    $customer->setCustomAttribute('customer_apistatus', $result);
                    $this->customerRepositoryInterface->save($customer); 
                }
            }
            $this->messageManager->addSuccess(__('Your Records Synchronized Successfully'));
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            return $resultRedirect;
        } catch(Exception $e) {
            echo 'Exception Message: ' . $e->getMessage();
        }
    }
}