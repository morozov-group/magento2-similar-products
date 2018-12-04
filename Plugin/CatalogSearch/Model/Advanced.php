<?php
namespace Morozov\Similarity\Plugin\CatalogSearch\Model;

use Magento\Framework\Exception\LocalizedException;

class Advanced
{
    protected $defaultHelper;

    protected $apiHelper;

    protected $advancedSearchHelper;

    protected $request;

    public function __construct(
        \Morozov\Similarity\Helper\Data $defaultHelper,
        \Morozov\Similarity\Helper\Api $apiHelper,
        \Morozov\Similarity\Helper\AdvancedSearch $advancedSearchHelper,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->defaultHelper = $defaultHelper;
        $this->apiHelper = $apiHelper;
        $this->advancedSearchHelper = $advancedSearchHelper;
        $this->request = $request;
    }

    public function aroundAddFilters(
        \Magento\CatalogSearch\Model\Advanced $advanced,
        \Closure $proceed,
        array $values
    )
    {
        try {
            $proceed($values);
            if ($this->getSimilar()) {
                $this->addSimilarFilters($advanced, $this->getSimilar());
            }
        } catch (LocalizedException $e) {
            if ($this->getSimilar() && $this->detectTermsNotSpecifiedMsg($e->getMessage())) {
                $this->addSimilarFilters($advanced, $this->getSimilar());
            } else {
                throw $e;
            }
        }

        return $advanced;
    }

    protected function detectTermsNotSpecifiedMsg($message)
    {
        $res = stristr($message, (string)__('Please specify at least one search term.'));
        return $res;
    }

    protected function getSimilar()
    {
        $similar = $this->request->getParam($this->advancedSearchHelper->getSimilarVarName());
        return $similar;
    }

    protected function addSimilarFilters(\Magento\CatalogSearch\Model\Advanced $advanced, $similar)
    {
        $ids = [];
        try {
            $ids = @$this->apiHelper->getUpSells((int)$similar);
            $advanced->getProductCollection()
                ->addFieldToFilter('entity_id', ['in' => $ids])
            ;
        } catch (LocalizedException $e) {
            $this->defaultHelper->log('Advanced Search: ' . $e->getMessage());
        }
        if (!$ids) {
            throw new LocalizedException(__("Couldn't get similar products from the service.."));
        }
    }
}