<?php
namespace Morozov\Similarity\Observer\Collection;

use Magento\Framework\Event\ObserverInterface;

class ApplyLimitationsAfter implements ObserverInterface
{
    protected static $isFiltered = false;

    public function __construct(
    )
    {

    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $collection = $observer->getEvent()->getCollection();
        if ($this->detectProductCollection($collection)) {
            if ($this->detectFromCategoryView()) {
                if (!self::$isFiltered) {
                    //@TODO: filter collection by Product IDs received from the service
                    
                    //$collection->getSelect()->where('e.entity_id IN(?)', [1817, 1801, 1785, 1769, 1753, 1737, 1721, 1705, 1689]); // works :-)
                    //$collection->getSelect()->where('e.entity_id IN(?)', [1817, 1801, 1785, 1769, 1753, 1737, 1721, 1705, 1689,    1673, 1657, 1641, 1625, 1609, 1593, 1577, 1561, 1545,     1529, 1513, 1497, 1481, 1465, 1449, 1433, 1417, 1401,     1385, 1369, 1353, 1337, 1321, 1305, 1289, 1273, 1257,     1241, 1225, 1215, 1199, 1183, 1167, 1151, 1135, 1119,     1113, 1151, 1097, 1081, 1065, 1049]); // works :-)
                    self::$isFiltered = true;
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
            if ($trace['object'] instanceof \Magento\Catalog\Controller\Category\View) {
                $res = true;
                break;
            }
        }
        return $res;
    }
}
