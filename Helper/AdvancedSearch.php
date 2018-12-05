<?php
namespace Morozov\Similarity\Helper;

use Magento\Framework\App\Helper\Context;

class AdvancedSearch extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $similarVarName = 'similar';

    protected $request;

    public function __construct(
        Context $context,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->request = $request;
        parent::__construct($context);
    }

    public function getSimilarVarName()
    {
        return $this->similarVarName;
    }

    public function getSimilar()
    {
        $similar = $this->request->getParam($this->getSimilarVarName());
        return $similar;
    }

    public function getSimilarFormInput($value = '')
    {
        $input = '';
        if ($value) {
            $input = '<input type="hidden" name="' . $this->getSimilarVarName() . '" value="' . $value . '"/>';
        }
        return $input;
    }
}
