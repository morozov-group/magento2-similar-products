<?php
namespace Morozov\Similarity\Helper;

use Magento\Framework\App\Helper\Context;

class Router extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $similarVarName = 'similar';

    protected $request;

    protected $productCollectionFactory;

    public function __construct(
        Context $context,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
    ) {
        $this->request = $request;
        $this->productCollectionFactory = $productCollectionFactory;
        parent::__construct($context);
    }

    public function getProductIdByUrl($url)
    {
        if ($urlKey = $this->getUrlKey($url)) {
            $collection = $this->productCollectionFactory->create();
            // Store View is already working in Collection
            $collection
                ->addAttributeToSelect('url_key')
                ->addAttributeToFilter('url_key', ['eq' => $urlKey])
            ;
            $product = $collection->getFirstItem();
            $id = $product ? $product->getEntityId() : null;
            return $id;
        }

        return null;
    }

    public function getUrlByProductId()
    {

    }

    protected function getUrlKey($url)
    {
        preg_match_all("/\/([^\/]+)\.html/i", $url, $matches);
        $urlKey = @$matches[1][0];
        return $urlKey;
    }
}
