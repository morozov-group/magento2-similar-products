<?php
namespace Morozov\Similarity\Controller\Test;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class Index extends Action
{
    protected $defaultHelper;

    protected $sqlHelper;

    protected $apiHelper;

    protected $storeManager;

    public function __construct(
        Context $context,
        \Morozov\Similarity\Helper\Data $defaultHelper,
        \Morozov\Similarity\Helper\Sql $sqlHelper,
        \Morozov\Similarity\Helper\Api $apiHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct(
            $context
        );
        $this->defaultHelper = $defaultHelper;
        $this->sqlHelper = $sqlHelper;
        $this->apiHelper = $apiHelper;
        $this->storeManager = $storeManager;
    }

    public function execute()
    {
        /*
        foreach($this->defaultHelper->getStores() as $store) {
            echo '<pre>';
            //var_dump($key);
            //var_dump($value['code']);
            //var_dump(get_class($value)); // Magento\Store\Model\Store\Interceptor
            var_dump($store->getId());
            var_dump($store->getCode());
            //@file_put_contents('l.log', print_r(array_keys($value), 1), FILE_APPEND);
            echo '</pre>';
        }
        exit;
        */

        // Api Helper
        //var_dump($this->apiHelper->getNearestRegion());
        //$this->apiHelper->collectProducts();
        //$this->apiHelper->setAllProducts();
        //$ids = $this->apiHelper->getUpSells(1);
        //var_dump($ids);

        /*
        // Sql Helper
        echo '<pre>';
        echo $this->sqlHelper->prepareExportProducts();
        echo '</pre>';
        */

        var_dump($this->defaultHelper->getCronEnabled());
        var_dump($this->defaultHelper->getImageCheckEnabled());
        exit;

        //$this->defaultHelper->setScopeCode('default');
        //var_dump($this->defaultHelper->getUpSellMaxCount());
        //exit;

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
