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
    protected $customerRepositoryInterface;
    public function __construct(
      ApiResponse $apiResponse,
      Session $session,
      CustomerRepositoryInterface $customerRepositoryInterface
    ) {
      $this->apiResponse = $apiResponse;
      $this->session = $session;
      $this->customerRepositoryInterface = $customerRepositoryInterface;
    }
    public function execute()
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/customer_status_cron.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('Cron job started');
        $logger->info('Cron job started-----');
        //$logger->info($this->apiResponse->getApiResponse());
        $linkField = $this->apiResponse->getApiResponse();
        //$linkField = $this->apiResponse->getMetadataPool()->getMetadata(ApiResponse::class)->getApiResponse();
        
        $logger->info('Cron job started+++++');
        $logger->info($linkField);
        //return $this;
        /*if ($value) {
            $customerDetail = $this->session->getData();
            $logger->info(print_r($customerDetail, true));
            $customer = $this->customerRepositoryInterface->getById($customerDetail['customer_id']);
            $customer->setData('customer_apistatus', $value);
            $customer->save();
        }*/

    }
}