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

    public function beforeLoad(
        \Magento\Catalog\Model\ResourceModel\Product\Collection $subject,
        $printQuery = false,
        $logQuery = false
    ) {
        if ($this->_registry->registry('current_category') &&
            $similar = $this->_request->getParam('similar')) {
            if ($ids = $this->_helper->getUpSells($similar)) {
                $subject->addFieldToFilter('entity_id', ['in' => $ids]);
            }
        }
        return [$printQuery, $logQuery];
    }
}
