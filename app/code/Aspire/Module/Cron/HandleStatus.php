<?php

namespace Aspire\Module\Cron;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Customer;
use Aspire\Module\Block\Adminhtml\ApiCall;
class HandleStatus
{
    protected $customerRepositoryInterface;
    protected $customerCollection;
    protected $apiCall;
    public function __construct(
      CustomerRepositoryInterface $customerRepositoryInterface,
      Customer $customerCollection,
      ApiCall $apiCall
    ) {
      $this->customerRepositoryInterface = $customerRepositoryInterface;
      $this->customerCollection = $customerCollection;
      $this->apiCall = $apiCall;
    }
    public function execute()
    {
        try {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/customer_status_cron.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info('Cron job started');
            $customerCollection = $this->customerCollection->getCollection()->addAttributeToSelect("*")->load();
            $logger->info('customer collection');
            foreach ($customerCollection->getData() as $customer) {
                $result = $this->apiCall->getApiCustomerStatus($customer['entity_id']);
                $customer = $this->customerRepositoryInterface->getById($customer['entity_id']);
                $customer->setCustomAttribute('customer_apistatus', $result);
                $this->customerRepositoryInterface->save($customer);
                $logger->info($result);
            }
        } catch(Exception $e) {
            echo 'Exception Message: ' . $e->getMessage();
        }
    }
}