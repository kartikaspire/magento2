<?php
namespace Aspire\Module\Logger;

use Monolog\Logger;

class CronHandler extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * Logging level
     * @var int
     */
    protected $loggerType = Logger::INFO;

    /**
     * File name
     * @var string
     */
    protected $fileName = '/var/log/customer_cron_status.log';
}