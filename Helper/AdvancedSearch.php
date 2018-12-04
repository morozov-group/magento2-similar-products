<?php
namespace Morozov\Similarity\Helper;

class AdvancedSearch extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $similarVarName = 'similar';

    public function getSimilarVarName()
    {
        return $this->similarVarName;
    }

    public function getSimilarFormInput($value)
    {
        $input = '';
        if ($value) {
            $input = '<input type="hidden" name="' . $this->getSimilarVarName() . '" value="' . $value . '"/>';
        }
        return $input;
    }
}
