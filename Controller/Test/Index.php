<?php
namespace Morozov\Similarity\Controller\Test;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class Index extends Action
{
    protected $defaultHelper;

    public function __construct(
        Context $context,
        \Morozov\Similarity\Helper\Data $defaultHelper
    ) {
        parent::__construct(
            $context
        );
        $this->defaultHelper = $defaultHelper;
    }

    public function execute()
    {
        // Api Helper


        // Data Helper
        var_dump($this->defaultHelper->getIsEnabled());
        var_dump($this->defaultHelper->getEmail());
        var_dump($this->defaultHelper->getUrl());
        var_dump($this->defaultHelper->getKey());
        var_dump($this->defaultHelper->getTimeout());

        var_dump($this->defaultHelper->getUpSellMaxCount());

        var_dump($this->defaultHelper->canUse());
        var_dump($this->defaultHelper->getExportDir());
        var_dump($this->defaultHelper->getProductsFile());
        var_dump($this->defaultHelper->getProductsFileUrl());

        $this->defaultHelper->log('TEST');

        //echo 'ee';
    }
}
