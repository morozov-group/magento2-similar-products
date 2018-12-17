<?php
namespace Morozov\Similarity\Observer\Collection;

use Magento\Framework\Event\ObserverInterface;

class ApplyLimitationsAfter implements ObserverInterface
{
    public function __construct(
    )
    {

    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $collection = $observer->getEvent()->getCollection();
        if ($this->detectProductCollection($collection)) {
            //@TODO: filter collection by Product IDs received from the service

            //file_put_contents('l.log', "\n" . get_class($collection->getEntity()), FILE_APPEND);
            // http://sim.m2/women/tops-women.html
            //$collection->getSelect()->where('e.entity_id IN(?)', [1817, 1801, 1785, 1769, 1753, 1737, 1721, 1705, 1689]); // works :-)
            //$collection->getSelect()->where('e.entity_id IN(?)', [1817, 1801, 1785, 1769, 1753, 1737, 1721, 1705, 1689,    1673, 1657, 1641, 1625, 1609, 1593, 1577, 1561, 1545,     1529, 1513, 1497, 1481, 1465, 1449, 1433, 1417, 1401,     1385, 1369, 1353, 1337, 1321, 1305, 1289, 1273, 1257,     1241, 1225, 1215, 1199, 1183, 1167, 1151, 1135, 1119,     1113, 1151, 1097, 1081, 1065, 1049]); // works :-)
            //file_put_contents('l.log', "\n" . $collection->getSelect()->assemble(), FILE_APPEND);

            // TEST category (Anchor => No)
            //$collection->getSelect()->where('e.entity_id IN(?)', [2046, 2022, 1988, 1995, 2015,   1817, 1801, 1785, 1769, 1753, 1737, 1721, 1705, 1689, 1673, 1657]); // works :-)
        }
    }

    protected function detectProductCollection($collection)
    {
        $res = $collection instanceof \Magento\Catalog\Model\ResourceModel\Product\Collection;
        return $res;
    }
}
