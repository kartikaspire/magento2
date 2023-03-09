<?php

namespace Aspire\Module\Cron;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Customer;
use Aspire\Module\Helper\ApiResponse;
use Aspire\Module\Helper\Data;
use Aspire\Module\Logger\CronLogger;

/**
 * Cron HandleStatus
 */
class HandleStatus
{
    /**
     * @var CustomerRepositoryInterface
    */
    protected $customerRepositoryInterface;
    /**
     * @var Customer
    */
    protected $customerCollection;
    /**
     * @var ApiResponse
     */
    protected $apiResponse;
    /**
     * @var Data
    */
    protected $data;
    /**
     * @var CronLogger
    */
    protected $cronLogger;

    /**
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param Customer $customerCollection
     * @param ApiResponse $apiResponse
     * @param Data $data
     * @param CronLogger $cronLogger
    */
    public function __construct(
      CustomerRepositoryInterface $customerRepositoryInterface,
      Customer $customerCollection,
      Data $data,
      CronLogger $cronLogger,
      ApiResponse $apiResponse
    ) {
      $this->customerRepositoryInterface = $customerRepositoryInterface;
      $this->customerCollection = $customerCollection;
      $this->helper = $data;
      $this->cronLogger = $cronLogger;
      $this->apiResponse = $apiResponse;
      $this->enableCron = $this->helper->isCronEnabled();
    }

    public function execute()
    {
        try {
            if ($this->enableCron == 1) {
                $this->cronLogger->info('Cron job started');
                $customerCollection = $this->customerCollection->getCollection()->addAttributeToSelect("*")->load();
                if (!empty($customerCollection)) {
                    foreach ($customerCollection->getData() as $customer) {
                        $result = $this->apiResponse->getApiResponse($customer['entity_id']);
                        $customer = $this->customerRepositoryInterface->getById($customer['entity_id']);
                        $customer->setCustomAttribute('customer_apistatus', $result);
                        $this->customerRepositoryInterface->save($customer);;
                        $this->cronLogger->info($result);
                    }
                }
            }
        } catch(Exception $e) {
            $this->cronLogger->error($e->getMessage(), ['exception' => $e]);
        }
    }
}