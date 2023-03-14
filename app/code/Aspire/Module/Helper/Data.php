<?php

namespace Aspire\Module\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Model\Session;

/**
 * Helper Data
 */
class Data extends AbstractHelper
{
    const CONFIGURATION_ENABLE = 'module/configuration/enable';
    const CONFIGURATION_APIURL = 'module/configuration/api_url';
    const API_USERNAME = 'module/configuration/api_username';
    const API_PASSWORD = 'module/configuration/api_password';
    const CONFIGURATION_BLOCK_PAGE = 'module/configuration/pages';
    const CONFIGURATION_USER_GROUP_BLOCK = 'module/configuration/customer_group_list';
    const CONFIGURATION_SUSPENDED_MESSAGE = 'module/configuration/suspended_message';
    const CRON_ENABLE = 'module/configurable_cron/enable';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var Session
     */
    protected $session;
    /**
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        Session $session
    )
    {
        parent::__construct($context);
        $this->scopeConfig = $scopeConfig;
        $this->session = $session;
    }

    /*
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->getValue(self::CONFIGURATION_ENABLE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /*
     * @return string
     */
    public function getBlockCustomerGroup()
    {
        return $this->scopeConfig->getValue(self::CONFIGURATION_USER_GROUP_BLOCK, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /*
     * @return string
     */
    public function getBlockPages()
    {
        return $this->scopeConfig->getValue(self::CONFIGURATION_BLOCK_PAGE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /*
     * @return string
     */
    public function getApiUrl()
    {
        return $this->scopeConfig->getValue(self::CONFIGURATION_APIURL, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /*
     * @return string
     */
    public function getApiUserName()
    {
        return $this->scopeConfig->getValue(self::API_USERNAME, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /*
     * @return string
     */
    public function getApiPassword()
    {
        return $this->scopeConfig->getValue(self::API_PASSWORD, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /*
     * @return bool
     */
    public function isCronEnabled()
    {
        return $this->scopeConfig->getValue(self::CRON_ENABLE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /*
     * @return string
     */
    public function getSuspendedMessage()
    {
        return $this->scopeConfig->getValue(self::CONFIGURATION_SUSPENDED_MESSAGE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /*
     * @return int
     */
    public function getGroupId() {
        if ($this->session->isLoggedIn()) {
            return $this->session->getCustomer()->getGroupId();
        }
    }
}