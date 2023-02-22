<?php
namespace Aspire\Module\Helper;
 
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Aspire\Module\Logger\Logger;
use Magento\Framework\App\Helper\Context;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\CustomerRepositoryInterface;
 
class ApiResponse extends AbstractHelper {

  const XML_CONFIGURATION_SETTING = 'module/configuration/enable';
  const XML_CONFIGURATION_APIURL = 'module/configuration/api_url';

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

  protected $customerRepository;
   
  public function __construct(
    Context $context,
    Curl $curl,
    ScopeConfigInterface $scopeConfig,
    Logger $logger,
    Session $session,
    CustomerRepositoryInterface $customerRepository
  ) {
    parent::__construct($context);
    $this->curl = $curl;
    $this->scopeConfig = $scopeConfig;
    $this->_logger = $logger;
    $this->session = $session;
    $this->customerRepository = $customerRepository;
  }
   
  public function getApiResponse() 
  {
    $this->_logger->info('Api Code Starts here---');
    $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
    $apiUrl = $this->scopeConfig->getValue(self::XML_CONFIGURATION_APIURL, $storeScope);
    $customerDetail = $this->session->getData();
    $customer = $this->getCustomer($customerDetail['customer_id']);
    $this->_logger->info($customer->getEmail());
    /*$URL = 'www.example.com';
    $username = 'username';
    $password = 'password';
   
    //set curl options
    $this->curl->setOption(CURLOPT_USERPWD, $username . ":" . $password);
    $this->curl->setOption(CURLOPT_HEADER, 0);
    $this->curl->setOption(CURLOPT_TIMEOUT, 60);
    $this->curl->setOption(CURLOPT_RETURNTRANSFER, true);
    $this->curl->setOption(CURLOPT_CUSTOMREQUEST, 'GET');
    //set curl header
    $this->curl->addHeader("Content-Type", "application/json");
    //get request with url
    $this->curl->get($URL);
    //read response
    $response = $this->curl->getBody();
    return $response;*/
  }

  public function getCustomer($id)
  { 
    return $this->customerRepository->getById($id);
  }  
}