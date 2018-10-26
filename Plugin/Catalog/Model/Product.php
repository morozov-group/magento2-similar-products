<?php
namespace Morozov\Similarity\Plugin\Catalog\Model;

class Product
{
    public function afterGetUpSellProductCollection(
        \Magento\Catalog\Model\Product $product,
        $upSellProductsCollection
    )
    {
        //@TODO: pull upsells from the service

        return $upSellProductsCollection;
    }
}