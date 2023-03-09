<?php
 
namespace Aspire\Module\Model\Source;

/**
 * CustomerStatusDropdown Selection
 */
class CustomerStatusDropdown extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource 
{
    public function getAllOptions() 
    {
        $type = [];
        $type[] = [
                'value' => '',
                'label' => '--Select--'
            ];
        $type[] = [
                'value' => 0,
                'label' => 'Active'
            ];
        $type[] = [
                'value' => 1,
                'label' => 'Blocked'
            ];
        $type[] = [
                'value' => 2,
                'label' => 'Suspended'
            ];
        return $type;
    }
    
    public function getOptionText($value) 
    {
        foreach ($this->getAllOptions() as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return false;
    }
}