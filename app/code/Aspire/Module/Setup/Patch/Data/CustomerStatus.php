<?php

namespace Aspire\Module\Setup\Patch\Data;

use Magento\Customer\Model\Attribute\Backend\Data\Boolean;
use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Customer\Setup\Patch\Data\UpdateIdentifierCustomerAttributesVisibility;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Customer\Model\ResourceModel\Attribute;

class CustomerStatus implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * @var Attribute
     */
    private $attributeResource;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CustomerSetupFactory $customerSetupFactory
     * @param Attribute $attributeResource
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CustomerSetupFactory $customerSetupFactory,
        Attribute $attributeResource
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeResource = $attributeResource;
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);

        /**
         * Add attribute
         */
        $customerSetup->addAttribute(
            Customer::ENTITY,
            'customer_status', // the attribute code
            [
                'type' => 'varchar', // database value type
                'label' => 'Customer Status', // Set the label
                'input' => 'text', // input type
                'backend' => Boolean::class, // backend model for boolean type attributes
                'position' => 100, // position in form
                'required' => false, // is it required for all customers?
                'system' => false, // is created by system
            ]
        );

        /**
         * Fetch the newly created attribute and set options to be used in forms
         */
        $customerAttribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'customer_status');

        $customerAttribute->setData('used_in_forms', [
            'adminhtml_customer',
            'adminhtml_checkout',
            'adminhtml_customer_address',
            'customer_account_edit',
            'customer_address_edit',
            'customer_register_address',
        ]);

        $this->attributeResource->save($customerAttribute);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [
            UpdateIdentifierCustomerAttributesVisibility::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }
}