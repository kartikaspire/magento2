<?php
namespace Aspire\Module\Controller\Index;

use Aspire\Module\Helper\ApiResponse;

class Index extends \Magento\Framework\App\Action\Action
{
	/**
	* @var apiHeler
	*/
    protected $apiHeler;

    protected $pageFactory;

    public function __construct(
    	\Magento\Framework\App\Action\Context $context,
    	\Magento\Framework\View\Result\PageFactory $pageFactory,
         ApiResponse $apiHeler
    ) {
        $this->apiHeler = $apiHeler;
        $this->pageFactory = $pageFactory;
        parent::__construct($context);
    }       

    public function execute()
    {
    	$pageCreate = $this->pageFactory->create();
        $result = $this->apiHeler->getApiResponse();
        return $result;     
    }
}