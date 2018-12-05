<?php
namespace Morozov\Similarity\Observer\Block;

use Magento\Framework\Event\ObserverInterface;

class ToHtmlAfter implements ObserverInterface
{
    protected $advancedSearchHelper;

    public function __construct(
        \Morozov\Similarity\Helper\AdvancedSearch $advancedSearchHelper
    )
    {
        $this->advancedSearchHelper = $advancedSearchHelper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        $transport = $observer->getEvent()->getTransport();
        if ($this->detectAdvancedSearchForm($block)) {
            $html = $transport->getHtml();
            $html = $this->injectSimilarFormInput($block, $html);
            $transport->setHtml($html);
        }
    }

    protected function injectSimilarFormInput($block, $html)
    {
        if ($similar = $this->advancedSearchHelper->getSimilar()) {
            $url = str_replace(['/'], ['\/'], $block->getSearchPostUrl());
            $html = preg_replace(
                "/(<form(.)+($url)(.)+>)/i",
                "$1" . $this->advancedSearchHelper->getSimilarFormInput($similar),
                $html
            );
        }
        return $html;
    }

    protected function detectAdvancedSearchForm($block)
    {
        $res = $block instanceof \Magento\CatalogSearch\Block\Advanced\Form;
        return $res;
    }
}
