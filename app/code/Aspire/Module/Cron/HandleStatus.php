<?php

namespace Aspire\Module\Cron;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Customer;
use Aspire\Module\Helper\ApiResponse;
use Aspire\Module\Helper\Data;
use Aspire\Module\Logger\Logger;

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
     * @var \Aspire\Module\Logger\Logger
     */
    protected $_logger;

    /**
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param Customer $customerCollection
     * @param ApiResponse $apiResponse
     * @param Data $data
     * @param Logger $logger
    */
    public function __construct(
      CustomerRepositoryInterface $customerRepositoryInterface,
      Customer $customerCollection,
      Data $data,
      Logger $logger,
      ApiResponse $apiResponse
    ) {
      $this->customerRepositoryInterface = $customerRepositoryInterface;
      $this->customerCollection = $customerCollection;
      $this->helper = $data;
      $this->_logger = $logger;
      $this->apiResponse = $apiResponse;
      $this->enableCron = $this->helper->isCronEnabled();
      $this->enableModule = $this->helper->isEnabled();
    }

    public function execute()
    {
        try {
            if ($this->enableCron) {
                if ($this->enableCron) {
                    $this->_logger->info('Cron job started');
                    $customerCollection = $this->customerCollection->getCollection()->addAttributeToSelect("*")->load();
                    $this->_logger->info('testresult');
                    $this->_logger->info(print_r($customerCollection->getData(), true));
                    if (!empty($customerCollection)) {
                        foreach ($customerCollection->getData() as $customer) {
                            if (isset($customer['entity_id'])) {
                                $result = $this->apiResponse->getApiResponse($customer['entity_id']);
                                $customer = $this->customerRepositoryInterface->getById($customer['entity_id']);
                                if (!empty($customer)) {
                                    $customer->setCustomAttribute('customer_apistatus', $result);
                                    $this->customerRepositoryInterface->save($customer);;
                                    $this->_logger->info($result);
                                    $this->_logger->info('Customer Status is updated');
                                }
                            } else {
                                $this->_logger->info('Customer Status is not updated');
                            }
                        }
                    } else {
                        $this->_logger->info('Customer is not valid');
                    }
                }
            }
        } catch(Exception $e) {
            $this->_logger->error($e->getMessage(), ['exception' => $e]);
        }
    }
}