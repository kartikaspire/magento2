<?php
namespace Aspire\Module\Test\Unit\Observer;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
//use Aspire\Module\Observer\RestrictPage;

class RestrictPageTest extends \PHPUnit\Framework\TestCase
{
	/**
     * @var RestrictPage
     */
    protected $object;

    /**
     * Set up
     *
     * @return void
    */
    protected function setUp() :void
    {
        $objectManagerHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $className = \Aspire\Module\Observer\RestrictPage::class;
        $arguments = $objectManagerHelper->getConstructArguments($className);
        /*$this->context = $arguments['context'];*/

        $this->helper = $objectManagerHelper->getObject($className, $arguments);
    }

    /**
     * @return void
    */
    public function testExecute() :void
    {
        
    }
}
?>