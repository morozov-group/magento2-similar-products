<?php

namespace Morozov\Similarity\Plugin\ElasticsuiteCore\Client;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;
use Morozov\Similarity\Helper\Api;

class Client
{

    /**
     * @var Api
     */
    private $_helper;

    /**
     * @var RequestInterface
     */
    private $_request;

    private $_registry;

    /**
     * Client constructor.
     * @param Api $helper
     * @param RequestInterface $request
     * @param Registry $registry
     */
    public function __construct(
        Api $helper,
        RequestInterface $request,
        Registry $registry
    ) {
        $this->_helper = $helper;
        $this->_request = $request;
        $this->_registry = $registry;
    }

    /**
     * @param \Smile\ElasticsuiteCore\Client\Client $subject
     * @param $params
     * @return array
     * @throws \Exception
     */
    public function beforeSearch(
        \Smile\ElasticsuiteCore\Client\Client $subject,
        $params
    ) {
        if (
            (
                $this->_registry->registry('current_category') ||
                $this->_registry->registry('advanced_search_conditions')
            )
            && $similar = $this->_request->getParam('similar')
        ) {
            if ($ids = $this->_helper->getUpSells($similar)) {
                if (isset($params['body']['sort'])) {
                    foreach ($params['body']['sort'] as $k => $v) {
                        if (isset($v['category.position'])) {
                            $params['body']['sort'][$k] = [
                                '_script' => [
                                    'type' => 'number',
                                    'script' => [
                                        'inline' => "params.sortOrder.indexOf((int)doc['entity_id'].value)",
                                        'params' => [
                                            'sortOrder' => $ids
                                        ]
                                    ]
                                ]
                            ];
                        }
                    }
                }
            }
        }
        return [$params];
    }
}
