<?php

namespace Aspire\Module\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Aspire\Module\Logger\Logger;
use Magento\Framework\App\Helper\Context;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\App\State;

class ApiResponse extends AbstractHelper 
{
    const XML_CONFIGURATION_SETTING = 'module/configuration/enable';
    const XML_CONFIGURATION_APIURL = 'module/configuration/api_url';
    const XML_API_USERNAME = 'module/configuration/api_username';
    const XML_API_PASSWORD = 'module/configuration/api_password';
    /**
     * @var Context
     */
    protected $context;
    /**
     * @var Curl
     */
    protected $curl;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var \Aspire\Module\Logger\Logger
     */
    protected $_logger;
    /**
     * @var Session
     */
    protected $session;
    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;
    /**
     * @var State
     */
    protected $state;
    public function __construct(Context $context, Curl $curl, ScopeConfigInterface $scopeConfig, Logger $logger, Session $session, CustomerRepositoryInterface $customerRepository, State $state, RedirectFactory $resultRedirectFactory) {
        parent::__construct($context);
        $this->curl = $curl;
        $this->scopeConfig = $scopeConfig;
        $this->_logger = $logger;
        $this->session = $session;
        $this->customerRepository = $customerRepository;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->state = $state;
    }
    public function getApiResponse() {
        try {
            $this->_logger->info('Api Code Starts here---');
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $apiUrl = $this->scopeConfig->getValue(self::XML_CONFIGURATION_APIURL, $storeScope);
            $this->_logger->info('API Url');
            $this->_logger->info($apiUrl);
            $apiUsername = $this->scopeConfig->getValue(self::XML_API_USERNAME, $storeScope);
            $apiPassword = $this->scopeConfig->getValue(self::XML_API_PASSWORD, $storeScope);
            if ($this->session->isLoggedIn()) {
                $customerDetail = $this->session->getData();
                $customer = $this->getCustomer($customerDetail['customer_id']);
                $url = $apiUrl . '?email=' . $customer->getEmail();
                //set curl options
                $this->curl->setOption(CURLOPT_USERPWD, $apiUsername . ":" . $apiPassword);
                $this->curl->setOption(CURLOPT_HEADER, 0);
                $this->curl->setOption(CURLOPT_TIMEOUT, 60);
                $this->curl->setOption(CURLOPT_RETURNTRANSFER, true);
                $this->curl->setOption(CURLOPT_CUSTOMREQUEST, 'GET');
                //set curl header
                $this->curl->addHeader("Content-Type", "application/json");
                //get request with url
                $request = $this->curl->get($url);
                $this->_logger->info('API Request Log Here');
                $this->_logger->info(print_r($request, true));
                //read response
                $response = $this->curl->getBody();
                $this->_logger->info('API Response Log Here');
                $this->_logger->info(print_r($response, true));
                $data = json_decode($response, TRUE);
                $this->_logger->info(print_r($data, true));
                if ($data) {
                    $is_suspended = $data['result']['user_info']['is_suspended'];
                    return $is_suspended;
                }
            } else {
                //$resultRedirect = $this->resultRedirectFactory->create();
                //$resultRedirect->setPath('customer/account/login/');
                //return $resultRedirect;
                
            }
        }
        catch(Exception $e) {
            echo 'Exception Message: ' . $e->getMessage();
        }
    }
    public function getCustomer($id) {
        return $this->customerRepository->getById($id);
    }
    public function getArea() {
        return $this->state->getAreaCode();
    }
    public function getGroupId() {
        if ($this->session->isLoggedIn()) {
            return $this->session->getCustomer()->getGroupId();
        }
    }
}
