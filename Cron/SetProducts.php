<?php
namespace Morozov\Similarity\Cron;

class SetProducts
{
    protected $defaultHelper;

    protected $apiHelper;

    public function __construct(
        \Morozov\Similarity\Helper\Data $defaultHelper,
        \Morozov\Similarity\Helper\Api $apiHelper
    )
    {
        $this->defaultHelper = $defaultHelper;
        $this->apiHelper = $apiHelper;
    }

    public function execute()
    {
        foreach($this->defaultHelper->getStores() as $store) {
            try {
                $this->defaultHelper->setStore($store);
                if ($this->defaultHelper->getCronEnabled()) {
                    $msg = "Pushing Products to the service (Store ID = {$store->getId()}): ";
                    $this->defaultHelper->log('');
                    $this->defaultHelper->log($msg);
                    $this->apiHelper->setAllProducts();
                    $msg = 'Done.';
                    $this->defaultHelper->log($msg);
                }
            } catch (\Exception $e) {
                $this->defaultHelper->log($e->getMessage());
            }
        }
    }
}