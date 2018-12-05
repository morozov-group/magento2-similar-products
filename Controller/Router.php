<?php
namespace Morozov\Similarity\Controller;

class Router implements \Magento\Framework\App\RouterInterface
{
    const FRONT_NAME = 'similar';

    protected $actionFactory;

    protected $_response;

    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\App\ResponseInterface $response
    ) {
        $this->actionFactory = $actionFactory;
        $this->_response = $response;
    }

    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        if (!$this->getFrontName()) {
            return false;
        }

        if (stristr($request->getPathInfo(), '/' . $this->getFrontName())) {
            $request
                ->setModuleName('catalogsearch')
                ->setControllerName('advanced')
                ->setActionName('result')
            ;
            $request
                //->setParam('similar', 1)    // also works
                ->setQueryValue([
                    'similar' => 1,
                    /*
                    'product_list_order' => 'price', // name
                    'product_list_dir'   => 'desc',
                    'name'        => 'bottle',
                    'description' => 'water',
                    'sku'         => '24',
                    'price[from]' => '5',
                    'price[to]'   => '500',
                    */
                ])
            ;

            return $this->actionFactory->create(
                'Magento\Framework\App\Action\Forward',
                ['request' => $request]
            );
        }

        return false;
    }

    public function getFrontName()
    {
        return self::FRONT_NAME;
    }
}
