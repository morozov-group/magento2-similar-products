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
        if ($ids = $this->_getIds()) {
            $subject->addFieldToFilter('entity_id', ['in' => $ids]);

        }
        return [$printQuery, $logQuery];
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $subject
     * @param \Closure $proceed
     * @param $attribute
     * @param string $dir
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|mixed
     * @throws \Exception
     */
    public function aroundAddAttributeToSort(
        \Magento\Catalog\Model\ResourceModel\Product\Collection $subject,
        \Closure $proceed,
        $attribute,
        $dir = 'ASC'
    ) {
        if (($ids = $this->_getIds()) &&
            !($subject instanceof \Smile\ElasticsuiteCatalog\Model\ResourceModel\Product\Fulltext\Collection)) {
            if ($attribute == 'position') {
                $subject->getSelect()->order(new \Zend_Db_Expr(
                        "FIELD(e.entity_id, " . implode(',', $ids) . ")")
                );
                return $subject;
            }
        }
        return $proceed($attribute, $dir);
    }

    /**
     * @return array|bool
     * @throws \Exception
     */
    private function _getIds()
    {
        $ids = false;
        if (
            (
                $this->_registry->registry('current_category') ||
                $this->_registry->registry('advanced_search_conditions')
            ) &&
            $similar = $this->_request->getParam('similar')
        ) {
            $ids = $this->_helper->getUpSells($similar);
        }
        return $ids;
    }
}
