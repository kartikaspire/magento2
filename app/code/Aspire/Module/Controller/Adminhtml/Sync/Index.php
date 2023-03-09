<?php

namespace Aspire\Module\Controller\Adminhtml\Sync;

use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Controller\ResultFactory;
use Aspire\Module\Logger\CronLogger;
use Aspire\Module\Helper\ApiResponse;

/**
 * Controller Index
 */
class Index extends Action
{
    /**
     * @var PageFactory
    */
    protected $resultPageFactory;
    /**
     * @var CollectionFactory
    */
    protected $CollectionFactory;
    /**
     * @var CustomerRepositoryInterface
    */
    protected $customerRepositoryInterface;
    /**
     * @var CronLogger
    */
    protected $cronLogger;
    /**
      * @var \Magento\Framework\App\Config\ScopeConfigInterface
    */
    protected $scopeConfig;
    /**
     * @var ApiResponse
     */
    protected $apiResponse;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Filter $filter
     * @param CollectionFactory $CollectionFactory
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param ApiResponse $apiResponse
    */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Filter $filter,
        CollectionFactory $CollectionFactory,
        CustomerRepositoryInterface $customerRepositoryInterface,
        CronLogger $cronLogger,
        ApiResponse $apiResponse
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->CollectionFactory = $CollectionFactory;
        $this->filter = $filter;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->cronLogger = $cronLogger;
        $this->apiResponse = $apiResponse;
        parent::__construct($context);
    }
    /**
     * Controller Execute Method
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->CollectionFactory->create());
            foreach ($collection as $child) {
                if ($child->getData('entity_id')) {
                    $result = $this->apiResponse->getApiResponse($child->getData('entity_id'));
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
            $this->cronLogger->error($e->getMessage(), ['exception' => $e]);
        }
    }
}