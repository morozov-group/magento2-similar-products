<?php
namespace Morozov\Similarity\Model\ResourceModel\Product\Link\Product;

class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection
{
    // override parent
    public function setPositionOrder($dir = self::SORT_ORDER_ASC)
    {
        $where = $this->getSelect()->getPart(\Zend_Db_Select::WHERE);
        foreach($where as &$w) {
            $w = str_replace('`', '', $w);
            preg_match('/e.entity_id[\s]+IN[\s]*\(([^)]+)\)/i', $w, $matches);
            if (isset($matches[1])) {
                $this->getSelect()->order(new \Zend_Db_Expr("FIELD(e.entity_id, {$matches[1]})"));
            }
        }

        return $this;
    }
}
