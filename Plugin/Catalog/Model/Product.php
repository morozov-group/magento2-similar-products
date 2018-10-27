<?php
namespace Morozov\Similarity\Plugin\Catalog\Model;

class Product
{
    protected $collectionFactory;

    protected $defaultHelper;

    protected $apiHelper;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\Link\Product\CollectionFactory $collectionFactory,
        \Morozov\Similarity\Helper\Data $defaultHelper,
        \Morozov\Similarity\Helper\Api $apiHelper
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->defaultHelper = $defaultHelper;
        $this->apiHelper = $apiHelper;
    }

    public function afterGetUpSellProductCollection(
        \Magento\Catalog\Model\Product $product,
        $upSellProductsCollection
    )
    {
        if ($this->defaultHelper->canUse()) {
            try {
                if ($ids = $this->apiHelper->getUpSells($product->getEntityId())) {
                    $productCollection = $this->collectionFactory->create();
                    $productCollection
                        ->addAttributeToFilter('entity_id', ['in' => $ids])
                    ;
                    if ($this->defaultHelper->getUpSellMaxCount()) {
                        $productCollection
                            ->setPageSize($this->defaultHelper->getUpSellMaxCount())
                            ->setCurPage(1)
                        ;
                    }
                    return $productCollection;
                }
            } catch (\Exception $e) {
                $this->defaultHelper->log($e->getMessage());
            }
        }

        return $upSellProductsCollection;
    }
}