<?php

namespace Aspire\Module\Block\Adminhtml;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Aspire\Module\Helper\Data;
use GuzzleHttp\Client;
use GuzzleHttp\ClientFactory;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ResponseFactory;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Framework\Serialize\SerializerInterface;

class ApiCall extends \Magento\Backend\Block\Widget\Grid\Container 
{
    /**
     * API request endpoint
     */
    const API_REQUEST_ENDPOINT = '?email=';

    /**
     * @var ResponseFactory
     */
    protected $responseFactory;

    /**
     * @var ClientFactory
     */
    protected $clientFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;
    /**
     * @var SerializerInterface
     */
    protected $serializer;
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @param Context $context
     * @param CustomerRepositoryInterface $customerRepository
     * @param Data $helper
     * @param ClientFactory $clientFactory
     * @param ResponseFactory $responseFactory
     * @param SerializerInterface $serializer
     * @param array $data
    */
    public function __construct(\Magento\Backend\Block\Widget\Context $context, CustomerRepositoryInterface $customerRepository, Data $helper, ClientFactory $clientFactory, ResponseFactory $responseFactory, SerializerInterface $serializer, array $data = []) {
        
        $this->customerRepository = $customerRepository;
        $this->helper = $helper;
        $this->clientFactory = $clientFactory;
        $this->responseFactory = $responseFactory;
        $this->serializer = $serializer;
        parent::__construct($context, $data);
    }

    /**
     * @return int
     */
    public function getApiCustomerStatus($id) {
        try {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/aspire_apiresponse_log_file.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info('Sync---------');
            $apiUrl = $this->helper->getApiUrl();
            $apiUsername = $this->helper->getApiUserName();
            $apiPassword = $this->helper->getApiPassword();
            $customer = $this->getCustomer($id);
            $response = $this->doRequest(static::API_REQUEST_ENDPOINT . $customer->getEmail());
            $status = $response->getStatusCode(); // 200 status code
            $responseBody = $response->getBody();
            $responseContent = $responseBody->getContents();
            $this->_logger->info('set get data------');
            $this->_logger->info(print_r($responseContent, true));
            $data = $this->serializer->unserialize($responseContent);
            if ($data) {
              $is_suspended = $data['result']['user_info']['is_suspended'];
              $this->_logger->info($is_suspended);
              return $is_suspended;
            }
        }
        catch(Exception $e) {
            echo 'Exception Message: ' . $e->getMessage();
        }
    }

    /**
     * @return array
     */
    public function getCustomer($id) {
        return $this->customerRepository->getById($id);
    }

    private function doRequest(
        string $uriEndpoint,
        array $params = [],
        string $requestMethod = Request::HTTP_METHOD_GET
    ): Response {
        /** @var Client $client */
        $apiUrl = $this->helper->getApiUrl();
        $client = $this->clientFactory->create(['config' => [
            'base_uri' => $apiUrl
        ]]);

        try {
            $response = $client->request(
                $requestMethod,
                $uriEndpoint,
                $params
            );
        } catch (GuzzleException $exception) {
            /** @var Response $response */
            $response = $this->responseFactory->create([
                'status' => $exception->getCode(),
                'reason' => $exception->getMessage()
            ]);
        }

        return $response;
    }
}
