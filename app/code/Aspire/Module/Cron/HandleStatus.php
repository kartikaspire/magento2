<?php

namespace Aspire\Module\Cron;
use Aspire\Module\Helper\ApiResponse;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
class HandleStatus
{
    protected $apiResponse;
     /**
      * @var Session
      */
      protected $session;
      proteched $customerRepositoryInterface;
    public function __construct(
      ApiResponse $apiResponse,
      Session $session,
      CustomerRepositoryInterface $customerRepositoryInterface;
    ) {
      $this->apiResponse = $apiResponse;
      $this->session = $session;
      $this->customerRepositoryInterface = $customerRepositoryInterface
    }
    public function execute()
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/customer_status_cron.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('Cron job started');
        $value = $this->apiResponse->getApiResponse();
        if ($value) {
            $customerDetail = $this->session->getData();
            $customer = $this->customerRepositoryInterface->getById($customerDetail['customer_id']);
            $customer->setData('customer_status', $value);
            $customer->save();
        }

    }
}