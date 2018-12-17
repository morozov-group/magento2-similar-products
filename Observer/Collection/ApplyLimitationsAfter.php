<?php
namespace Morozov\Similarity\Observer\Collection;

use Magento\Framework\Event\ObserverInterface;

class ApplyLimitationsAfter implements ObserverInterface
{
    protected static $isFiltered = false;

    protected $defaultHelper;

    protected $requestHelper;

    protected $apiHelper;

    public function __construct(
        \Morozov\Similarity\Helper\Data $defaultHelper,
        \Morozov\Similarity\Helper\Request $requestHelper,
        \Morozov\Similarity\Helper\Api $apiHelper
    )
    {
        $this->defaultHelper = $defaultHelper;
        $this->requestHelper = $requestHelper;
        $this->apiHelper = $apiHelper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $collection = $observer->getEvent()->getCollection();
        if ($this->detectProductCollection($collection)) {
            if ($this->detectFromCategoryView()) {
                if (!self::$isFiltered) {
                    self::$isFiltered = true;

                    //@TODO: filter collection by Product IDs received from the service
                    if ($similar = $this->requestHelper->getSimilar()) {
                        try {
                            if ($ids = @$this->apiHelper->getUpSells((int)$similar)) {
                                $collection->getSelect()->where('e.entity_id IN(?)', $ids);
                                return;
                            }
                            //$collection->getSelect()->where('e.entity_id IN(?)', [1817, 1801, 1785, 1769, 1753, 1737, 1721, 1705, 1689]); // works :-)
                            //$collection->getSelect()->where('e.entity_id IN(?)', [1817, 1801, 1785, 1769, 1753, 1737, 1721, 1705, 1689,    1673, 1657, 1641, 1625, 1609, 1593, 1577, 1561, 1545,     1529, 1513, 1497, 1481, 1465, 1449, 1433, 1417, 1401,     1385, 1369, 1353, 1337, 1321, 1305, 1289, 1273, 1257,     1241, 1225, 1215, 1199, 1183, 1167, 1151, 1135, 1119,     1113, 1151, 1097, 1081, 1065, 1049]); // works :-)
                        } catch (\Exception $e) {
                            $this->defaultHelper->log('Category: ' . $e->getMessage());
                        }
                        $collection->getSelect()->where('e.entity_id IS NULL');
                    }
                }
            }
        }
    }

    protected function detectProductCollection($collection)
    {
        $res = $collection instanceof \Magento\Catalog\Model\ResourceModel\Product\Collection;
        return $res;
    }

    protected function detectFromCategoryView()
    {
        $res = false;
        $traces = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 40);
        foreach($traces as $trace) {
            if (isset($trace['object'])) {
                //file_put_contents('l.log', "\n" . get_class($trace['object']), FILE_APPEND);
                if (($trace['object'] instanceof \Magento\Catalog\Controller\Category\View)  // Anchor => Yes
                 || ($trace['object'] instanceof \Magento\Catalog\Block\Category\View)       // Anchor => No
                ) {
                    $res = true;
                    break;
                }
            }
        }
        return $res;
    }
}
