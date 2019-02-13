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
        $ids = $this->_getIds();
        if ($ids) {
            $subject->addFieldToFilter('entity_id', ['in' => $ids]);

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
        if ($ids) {
            $orders = $subject->getSelect()->getPart(\Zend_Db_Select::ORDER);
            foreach ($orders as $k => &$order) {
                if(strpos($order, 'cat_index.position') !== false) {
                    $order = new \Zend_Db_Expr(
                        "FIELD(e.entity_id, " . implode(',', $ids) . ")");
                };
            }
        }
        return $proceed($printQuery, $logQuery, $sql);
    }

    /**
     * @return array|bool
     * @throws \Exception
     */
    private function _getIds()
    {
        $ids = false;
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
