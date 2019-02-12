<?php

namespace Morozov\Similarity\Plugin\ElasticsuiteCore\Client;

use Magento\Framework\App\RequestInterface;
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

    /**
     * Client constructor.
     * @param Api $helper
     * @param RequestInterface $request
     */
    public function __construct(
        Api $helper,
        RequestInterface $request
    ) {
        $this->_helper = $helper;
        $this->_request = $request;
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
        if (isset($params['body']['sort'])) {
            if ($similar = $this->_request->getParam('similar')) {
                if ($ids = $this->_helper->getUpSells($similar)) {
                    foreach ($params['body']['sort'] as $k => $v) {
                        if (isset($v['entity_id'])) {
                            unset($params['body']['sort'][$k]);
                        }
                    }
                    $params['body']['sort'][0] = [
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
        return [$params];
    }
}
