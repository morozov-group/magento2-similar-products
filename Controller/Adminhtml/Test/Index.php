<?php
namespace Morozov\Similarity\Controller\Adminhtml\Test;


class Index extends \Magento\Backend\App\Action
{
    protected $_publicActions = ['index'];

    protected $defaultHelper;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Morozov\Similarity\Helper\Data $defaultHelper
    ) {
        parent::__construct(
            $context
        );
        $this->defaultHelper = $defaultHelper;
    }

    public function execute()
    {
        //var_dump($this->defaultHelper->canUse());
    }
}
