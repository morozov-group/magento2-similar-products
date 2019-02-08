<?php

namespace Morozov\Similarity\Block\Widget;


use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ProductRepository;
use Magento\CatalogWidget\Block\Product\ProductsList;
use Magento\Framework\Module\Manager;
use Magento\Framework\Serialize\Serializer\Json;

class Upsell extends ProductsList
{

    /**
     * @var ProductRepository
     */
    private $_productRepository;

    private $_itemCollection;

    /**
     * @var Manager
     */
    private $_moduleManager;

    /**
     * @var string
     */
    protected $_template = "Magento_CatalogWidget::product/widget/content/grid.phtml";

    /**
     * Upsell constructor.
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param Visibility $catalogProductVisibility
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Rule\Model\Condition\Sql\Builder $sqlBuilder
     * @param \Magento\CatalogWidget\Model\Rule $rule
     * @param \Magento\Widget\Helper\Conditions $conditionsHelper
     * @param ProductRepository $productRepository
     * @param Manager $moduleManager
     * @param array $data
     * @param Json|null $json
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Rule\Model\Condition\Sql\Builder $sqlBuilder,
        \Magento\CatalogWidget\Model\Rule $rule,
        \Magento\Widget\Helper\Conditions $conditionsHelper,
        ProductRepository $productRepository,
        Manager $moduleManager,
        array $data = [],
        ?Json $json = null
    ) {
        $this->_productRepository = $productRepository;
        $this->_moduleManager = $moduleManager;
        parent::__construct($context, $productCollectionFactory, $catalogProductVisibility, $httpContext, $sqlBuilder, $rule, $conditionsHelper, $data, $json);
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|\Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function createCollection()
    {
        $productId = $this->getData('parent_product_id');
        $product = $this->_productRepository->getById($productId);
        /* @var $product \Magento\Catalog\Model\Product */
        $this->_itemCollection = $product->getUpSellProductCollection()->setPositionOrder()->addStoreFilter();
        if ($this->_moduleManager->isEnabled('Magento_Checkout')) {
            $this->_addProductAttributesAndPrices($this->_itemCollection);
        }
        $this->_itemCollection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());

        $this->_itemCollection->load();

        /**
         * Updating collection with desired items
         */
        $this->_eventManager->dispatch(
            'catalog_product_upsell',
            ['product' => $product, 'collection' => $this->_itemCollection, 'limit' => null]
        );

        foreach ($this->_itemCollection as $product) {
            $product->setDoNotUseCategoryId(true);
        }

        return $this->_itemCollection;
    }
}