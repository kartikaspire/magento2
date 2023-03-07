<?php

namespace Aspire\Module\Observer;

use Magento\Framework\Event\ObserverInterface;
use Aspire\Module\Logger\Logger;
use Magento\Customer\Model\Context;
use Magento\Framework\Event\Observer;
use Aspire\Module\Helper\ApiResponse;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\ResponseFactory;
use Magento\Customer\Model\Session;
use Aspire\Module\Helper\Data;

class RestrictPage implements ObserverInterface 
{
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
     * @var ApiResponse
    */
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
    /**
     * @var Data
     */
    protected $data;

    /**
     * @param Logger $logger
     * @param Context $context
     * @param ApiResponse $apiResponse
     * @param UrlInterface $url
     * @param ResponseFactory $responseFactory
     * @param Session $session
     * @param Data $data
    */
    public function __construct(Logger $logger, Context $context, ApiResponse $apiResponse, UrlInterface $url, ResponseFactory $responseFactory, Session $session, Data $data) {
        $this->_logger = $logger;
        $this->apiResponse = $apiResponse;
        $this->url = $url;
        $this->session = $session;
        $this->helper = $data;
        $this->responseFactory = $responseFactory;
        $this->enableModule = $this->helper->isEnabled();
        $this->blockCustomerGroup = $this->helper->getBlockCustomerGroup();
        $this->blockCustomerPages = $this->helper->getBlockPages();
    }
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer) {
        try {
            if ($this->apiResponse->getArea() == self::FRONTEND) {
                if ($this->enableModule == 1) {
                    $apiStatusValue = $this->apiResponse->getApiResponse();
                    $customerDetail = $this->session->getData();
                    $this->_logger->info('Page Restrict Starts here');
                    if (array_key_exists("customer_id", $customerDetail)) {
                        $customerData = $this->apiResponse->getCustomer($customerDetail['customer_id']);
                        $admin_customer_status = ($customerData->getCustomAttribute('customer_apistatus') != '') ? $customerData->getCustomAttribute('customer_apistatus')->getValue() : '';
                        $this->_logger->info('admin_customer_status');
                        $this->_logger->info($admin_customer_status);
                        if (($apiStatusValue == 0) && (($admin_customer_status == 0) || ($admin_customer_status == ''))) {
                            $pageOptionArray = explode(',', $this->blockCustomerPages);
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
            $this->_logger->error($e->getMessage(), ['exception' => $e]);
        }
    }
}
