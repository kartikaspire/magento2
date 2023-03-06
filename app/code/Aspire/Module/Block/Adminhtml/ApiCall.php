<?php

namespace Aspire\Module\Block\Adminhtml;

use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\State;
use Magento\Customer\Api\CustomerRepositoryInterface;

class ApiCall extends \Magento\Backend\Block\Widget\Grid\Container 
{
    const XML_CONFIGURATION_SETTING = 'module/configuration/enable';
    const XML_CONFIGURATION_APIURL = 'module/configuration/api_url';
    const XML_API_USERNAME = 'module/configuration/api_username';
    const XML_API_PASSWORD = 'module/configuration/api_password';
    /**
     * @var Curl
     */
    protected $curl;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var PageFactory
     */
    protected $resultRedirectFactory;
    /**
     * @var State
     */
    protected $state;
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;
    public function __construct(\Magento\Backend\Block\Widget\Context $context, Curl $curl, ScopeConfigInterface $scopeConfig, State $state, PageFactory $resultRedirectFactory, CustomerRepositoryInterface $customerRepository, array $data = []) {
        $this->curl = $curl;
        $this->scopeConfig = $scopeConfig;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->customerRepository = $customerRepository;
        $this->state = $state;
        parent::__construct($context, $data);
    }
    public function getApiCustomerStatus($id) {
        try {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/aspire_apiresponse_log_file.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info('Sync---------');
            $apiUrl = $this->scopeConfig->getValue(self::XML_CONFIGURATION_APIURL, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $logger->info($apiUrl);
            $apiUsername = $this->scopeConfig->getValue(self::XML_API_USERNAME, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $apiPassword = $this->scopeConfig->getValue(self::XML_API_PASSWORD, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $customer = $this->getCustomer($id);
            $logger->info($customer->getEmail());
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
            $response = $this->curl->getBody();
            $data = json_decode($response, TRUE);
            if (is_array($data)) {
                $logger->info(print_r($data, true));
                $is_suspended = $data['result']['user_info']['is_suspended'];
                return $is_suspended;
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
