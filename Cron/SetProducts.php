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
        try {
            //if ($this->defaultHelper->canUse()) {
                $this->apiHelper->setAllProducts();
            //}
        } catch (\Exception $e) {
            $this->defaultHelper->log($e->getMessage());
        }
    }
}