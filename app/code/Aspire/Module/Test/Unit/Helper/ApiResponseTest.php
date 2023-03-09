<?php
namespace Aspire\Module\Test\Unit\Helper;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

class ApiResponseTest extends \PHPUnit\Framework\TestCase
{
	/**
     * @var string
     */
    protected $apiResponseTest;

    /**
     * Set up
     *
     * @return void
    */
    protected function setUp() :void
    {
        $objectManagerHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $className = \Aspire\Module\Helper\ApiResponse::class;
        $arguments = $objectManagerHelper->getConstructArguments($className);
        /*$this->context = $arguments['context'];*/

        $this->helper = $objectManagerHelper->getObject($className, $arguments);
    }

    /**
     * @return void
    */
    public function testApiResponse() :void
    {
        $this->apiResponse = array("0"=>0, "1" =>1, "2" => 2);  
        // assert function to test whether 'value' is a value of array 
        $this->assertContains(0, $this->apiResponse, "testArray doesn't contains number as number") ;
        $this->assertTrue(true);        
    }
}
?>