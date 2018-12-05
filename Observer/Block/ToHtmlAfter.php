<?php
namespace Morozov\Similarity\Observer\Block;

use Magento\Framework\Event\ObserverInterface;

class ToHtmlAfter implements ObserverInterface
{
    protected $requestHelper;

    public function __construct(
        \Morozov\Similarity\Helper\Request $requestHelper
    )
    {
        $this->requestHelper = $requestHelper;
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
        if ($similar = $this->requestHelper->getSimilar()) {
            $url = str_replace(['/'], ['\/'], $block->getSearchPostUrl());
            $html = preg_replace(
                "/(<form(.)+($url)(.)+>)/i",
                "$1" . $this->requestHelper->getSimilarFormInput($similar),
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
