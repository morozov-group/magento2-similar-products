<?php
namespace Morozov\Similarity\Plugin\Catalog\Model;

class Product
{
    protected $collectionFactory;

    protected $defaultHelper;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\Link\Product\CollectionFactory $collectionFactory,
        \Morozov\Similarity\Helper\Data $defaultHelper
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->defaultHelper = $defaultHelper;
    }

    public function afterGetUpSellProductCollection(
        \Magento\Catalog\Model\Product $product,
        $upSellProductsCollection
    )
    {
        if ($this->defaultHelper->canUse()) {
            try {
                //@TODO: pull upsells from the service
                $productCollection = $this->collectionFactory->create();
                $productCollection->addAttributeToFilter('entity_id', ['in' => [7, 8]]);
                return $productCollection;
            } catch (\Exception $e) {
                $this->defaultHelper->log($e->getMessage());
            }
        }

        return $upSellProductsCollection;
    }
}