<?php

namespace Aspire\Module\Observer;

use Magento\Framework\Event\ObserverInterface;
use Aspire\Module\Logger\Logger;
use Magento\Customer\Model\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Serialize\SerializerInterface;
use Aspire\Module\Helper\ApiResponse;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\ResponseFactory;
use Magento\Customer\Model\Session;

class RestrictPage implements ObserverInterface 
{
    const XML_CONFIGURATION_BLOCK_PAGE = 'module/configuration/pages';
    const XML_CONFIGURATION_ENABLE = 'module/configuration/enable';
    const XML_CONFIGURATION_USER_GROUP_BLOCK = 'module/configuration/customer_group_list';
    const FRONTEND = 'frontend';
    /**
     * @var \Aspire\Module\Logger\Logger
     */
    protected $_logger;
    /**
     * @var Context
     */
    protected $context;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    protected $serializer;
    protected $apiResponse;
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;
    /**
     * @var \Magento\Framework\App\ResponseFactory
     */
    protected $responseFactory;
    /**
     * @var Session
     */
    protected $session;
    public function __construct(Logger $logger, Context $context, ScopeConfigInterface $scopeConfig, SerializerInterface $serializer, ApiResponse $apiResponse, UrlInterface $url, ResponseFactory $responseFactory, Session $session) {
        $this->scopeConfig = $scopeConfig;
        $this->_logger = $logger;
        $this->serializer = $serializer;
        $this->apiResponse = $apiResponse;
        $this->url = $url;
        $this->session = $session;
        $this->enableModule = $this->scopeConfig->getValue(self::XML_CONFIGURATION_ENABLE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $this->blockCustomerGroup = $this->scopeConfig->getValue(self::XML_CONFIGURATION_USER_GROUP_BLOCK, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $this->responseFactory = $responseFactory;
    }
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer) {
        try {
            if ($this->apiResponse->getArea() == self::FRONTEND) {
                if ($this->enableModule == 1) {
                    $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                    $pageValue = $this->scopeConfig->getValue(self::XML_CONFIGURATION_BLOCK_PAGE, $storeScope);
                    $apiStatusValue = $this->apiResponse->getApiResponse();
                    $customerDetail = $this->session->getData();
                    $this->_logger->info('Page Restrict Starts here');
                    if (array_key_exists("customer_id", $customerDetail)) {
                        $customerData = $this->apiResponse->getCustomer($customerDetail['customer_id']);
                        $admin_customer_status = ($customerData->getCustomAttribute('customer_apistatus') != '') ? $customerData->getCustomAttribute('customer_apistatus')->getValue() : '';
                        $this->_logger->info('admin_customer_status');
                        $this->_logger->info($admin_customer_status);
                        if (($apiStatusValue == 0) && (($admin_customer_status == 0) || ($admin_customer_status == ''))) {
                            $this->_logger->info('admin_customer_status-----');
                            $pageOptionArray = explode(',', $pageValue);
                            $fullPageName = $observer->getEvent()->getRequest()->getFullActionName();
                            $customerGroupIdArray = explode(',', $this->blockCustomerGroup);
                            $customerGroupId = $this->apiResponse->getGroupId();
                            if (in_array($fullPageName, $pageOptionArray) && in_array($customerGroupId, $customerGroupIdArray)) {
                                $redirectionUrl = $this->url->getUrl();
                                $redirectController = $observer->getControllerAction();
                                $redirectController->getResponse()->setRedirect($redirectionUrl);
                                return $this;
                            }
                        }
                    }
                }
            }
        }
        catch(Exception $e) {
            echo 'Exception Message: ' . $e->getMessage();
        }
    }
}
