<?php

namespace Aspire\Module\Cron;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Customer;
use Aspire\Module\Block\Adminhtml\ApiCall;
use Magento\Framework\App\Config\ScopeConfigInterface;
class HandleStatus
{
    const XML_CRON_ENABLE = 'module/configurable_cron/enable';
    protected $customerRepositoryInterface;
    protected $customerCollection;
    protected $apiCall;
    protected $scopeConfig;
    public function __construct(
      CustomerRepositoryInterface $customerRepositoryInterface,
      Customer $customerCollection,
      ApiCall $apiCall,
      ScopeConfigInterface $scopeConfig
    ) {
      $this->customerRepositoryInterface = $customerRepositoryInterface;
      $this->customerCollection = $customerCollection;
      $this->apiCall = $apiCall;
      $this->scopeConfig = $scopeConfig;
      $this->enableCron = $this->scopeConfig->getValue(self::XML_CRON_ENABLE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    public function execute()
    {
        try {
            if ($this->enableCron == 1) {
                $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/customer_status_cron.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);
                $logger->info('Cron job started');
                $customerCollection = $this->customerCollection->getCollection()->addAttributeToSelect("*")->load();
                foreach ($customerCollection->getData() as $customer) {
                    $result = $this->apiCall->getApiCustomerStatus($customer['entity_id']);
                    $customer = $this->customerRepositoryInterface->getById($customer['entity_id']);
                    $customer->setCustomAttribute('customer_apistatus', $result);
                    $this->customerRepositoryInterface->save($customer);
                    $logger->info($result);
                }
            }
        } catch(Exception $e) {
            echo 'Exception Message: ' . $e->getMessage();
        }
    }
}