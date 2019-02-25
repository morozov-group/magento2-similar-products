<?php

namespace Morozov\Similarity\Plugin\Catalog\Model\ResourceModel\Product;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;
use Morozov\Similarity\Helper\Api;

class Collection
{

    /**
     * @var Registry
     */
    private $_registry;

    /**
     * @var Api
     */
    private $_helper;

    /**
     * @var RequestInterface
     */
    private $_request;

    /**
     * Collection constructor.
     * @param Registry $registry
     * @param Api $helper
     * @param RequestInterface $request
     */
    public function __construct(
        Registry $registry,
        Api $helper,
        RequestInterface $request
    ) {
        $this->_registry = $registry;
        $this->_helper = $helper;
        $this->_request = $request;
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $subject
     * @param bool $printQuery
     * @param bool $logQuery
     * @return array
     * @throws \Exception
     */
    public function beforeLoad(
        \Magento\Catalog\Model\ResourceModel\Product\Collection $subject,
        $printQuery = false,
        $logQuery = false
    ) {
        if (!$subject->isLoaded()) {
            $ids = $this->_getIds();
            if ($ids !== null) {
                if ($subject instanceof \Smile\ElasticsuiteCatalog\Model\ResourceModel\Product\Fulltext\Collection) {
                    $subject->addFieldToFilter('entity_id', ['in' => $ids]);
                }
                elseif ($subject instanceof \Magento\Catalog\Model\ResourceModel\Product\Collection) {
                    $subject->getSelect()->where('e.entity_id in (' . implode(',', $ids) . ')');
                }
            }
        }
        return [$printQuery, $logQuery];
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $subject
     * @param \Closure $proceed
     * @param bool $printQuery
     * @param bool $logQuery
     * @param null $sql
     * @return mixed
     * @throws \Zend_Db_Select_Exception
     */
    public function aroundPrintLogQuery(
        \Magento\Catalog\Model\ResourceModel\Product\Collection $subject,
        \Closure $proceed,
        $printQuery = false,
        $logQuery = false,
        $sql = null
    ) {
        $ids = $this->_getIds();
        if ($ids !== null) {
            $orders = $subject->getSelect()->getPart(\Zend_Db_Select::ORDER);
            foreach ($orders as $k => &$order) {
                if ($order[0] == 'cat_index.position') {
                    $order = new \Zend_Db_Expr(
                        "FIELD(e.entity_id, " . implode(',', $ids) . ")");
                };
            }
            $subject->getSelect()->setPart(\Zend_Db_Select::ORDER, $orders);
        }
        return $proceed($printQuery, $logQuery, $sql);
    }

    /**
     * @return array|bool
     * @throws \Exception
     */
    private function _getIds()
    {
        $ids = null;
        $similar = $this->_request->getParam('similar');
        if (
            (
                $this->_registry->registry('current_category') ||
                $this->_registry->registry('advanced_search_conditions')
            ) && $similar
        ) {
            $ids = $this->_helper->getUpSells($similar);
        }
        return $ids;
    }
}
