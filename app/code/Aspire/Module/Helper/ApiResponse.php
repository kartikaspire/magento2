<?php
declare(strict_types=1);

namespace Aspire\Module\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Aspire\Module\Logger\Logger;
use Magento\Framework\App\Helper\Context;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\State;
use Aspire\Module\Helper\Data;
use GuzzleHttp\Client;
use GuzzleHttp\ClientFactory;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ResponseFactory;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * ApiResponse
 */
class ApiResponse extends AbstractHelper 
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
     * @var Context
     */
    protected $context;
    /**
     * @var \Aspire\Module\Logger\Logger
     */
    protected $_logger;
    /**
     * @var Session
     */
    protected $session;
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;
    /**
     * @var State
     */
    protected $state;
    /**
     * @var Data
     */
    protected $data;
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @param Context $context
     * @param Logger $logger
     * @param Session $session
     * @param CustomerRepositoryInterface $customerRepository
     * @param State $state
     * @param Data $data
    */
    public function __construct(Context $context, Logger $logger, Session $session, CustomerRepositoryInterface $customerRepository, State $state, Data $data, ClientFactory $clientFactory, ResponseFactory $responseFactory, SerializerInterface $serializer) {
        parent::__construct($context);
        $this->_logger = $logger;
        $this->session = $session;
        $this->customerRepository = $customerRepository;
        $this->state = $state;
        $this->helper = $data;
        $this->clientFactory = $clientFactory;
        $this->responseFactory = $responseFactory;
        $this->serializer = $serializer;
    }

    /**
     * @return int
     */
    public function getApiResponse($id) {
        try {
            $this->_logger->info('Api Code Starts here---');
            $apiUrl = $this->helper->getApiUrl();
            $apiUsername = $this->helper->getApiUserName();
            $apiPassword = $this->helper->getApiPassword();
            $customer = $this->getCustomer($id);
            $response = $this->doRequest(static::API_REQUEST_ENDPOINT . $customer->getEmail());
            $status = $response->getStatusCode(); // 200 status code
            $responseBody = $response->getBody();
            $responseContent = $responseBody->getContents();
            if (!empty($responseContent)) {
                $this->_logger->info('Response Body');
                $this->_logger->info(print_r($responseContent, true));
                $data = $this->serializer->unserialize($responseContent);
                if ($data) {
                    $is_suspended = $data['result']['user_info']['is_suspended'];
                    $this->_logger->info($is_suspended);
                    return $is_suspended;
                }
            }
        }
        catch(Exception $e) {
            $this->_logger->error($e->getMessage(), ['exception' => $e]);
        }
    }

    /**
     * @return array
     */
    public function getCustomer($id) {
        return $this->customerRepository->getById($id);
    }

    /**
     * @return string
     */
    public function getArea() {
        return $this->state->getAreaCode();
    }

    /**
     * @return int
     */
    public function getGroupId() {
        if ($this->session->isLoggedIn()) {
            return $this->session->getCustomer()->getGroupId();
        }
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
