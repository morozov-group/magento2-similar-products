<?php
namespace Morozov\Similarity\Controller\Test;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class Index extends Action
{
    protected $defaultHelper;

    protected $sqlHelper;

    protected $apiHelper;

    public function __construct(
        Context $context,
        \Morozov\Similarity\Helper\Data $defaultHelper,
        \Morozov\Similarity\Helper\Sql $sqlHelper,
        \Morozov\Similarity\Helper\Api $apiHelper
    ) {
        parent::__construct(
            $context
        );
        $this->defaultHelper = $defaultHelper;
        $this->sqlHelper = $sqlHelper;
        $this->apiHelper = $apiHelper;
    }

    public function execute()
    {
        // Api Helper
        //var_dump($this->apiHelper->getNearestRegion());
        //$this->apiHelper->collectProducts();
        //$this->apiHelper->setAllProducts();
        $ids = $this->apiHelper->getUpSells(1);
        var_dump($ids);

        /*
        // Sql Helper
        echo '<pre>';
        echo $this->sqlHelper->prepareExportProducts();
        echo '</pre>';
        */

        /*
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
        */

    }
}
