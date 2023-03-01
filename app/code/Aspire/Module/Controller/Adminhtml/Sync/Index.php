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
        $collection = $this->filter->getCollection($this->CollectionFactory->create());

        $count = 0;
        foreach ($collection as $child) {
            $result = $this->apiCall->getApiCustomerStatus($child->getData('entity_id'));
            $customer = $this->customerRepositoryInterface->getById($child->getData('entity_id'));
            $customer->setData('customer_apistatus', $result);
            $customer->save();
            $count++;
        }
        exit;
    }
}