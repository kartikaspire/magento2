<?php

namespace Aspire\Module\Controller\Adminhtml\Sync;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Index extends \Magento\Backend\App\Action
{
	const XML_CONFIGURATION_SETTING = 'module/configuration/enable';
    const XML_CONFIGURATION_APIURL = 'module/configuration/api_url';
    const XML_API_USERNAME = 'module/configuration/api_username';
    const XML_API_PASSWORD = 'module/configuration/api_password';

    /**
  	* @var \Magento\Framework\App\Config\ScopeConfigInterface
  	*/
  	protected $scopeConfig;
  	public function __construct(
    ScopeConfigInterface $scopeConfig
  	) {
		$this->scopeConfig = $scopeConfig;
	}
	public function execute() {
		echo "Hi";
		echo "By";
		$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
    $apiUrl = $this->scopeConfig->getValue(self::XML_CONFIGURATION_APIURL, $storeScope);
    $apiUsername = $this->scopeConfig->getValue(self::XML_API_USERNAME, $storeScope);
		//echo $this->apiResponse->getApiResponse();
	}
}
?>
