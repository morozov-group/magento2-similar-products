<?php
namespace Morozov\Similarity\Controller;

class Router implements \Magento\Framework\App\RouterInterface
{
    const FRONT_NAME = 'similar';

    protected $actionFactory;

    protected $response;

    protected $routerHelper;

    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\App\ResponseInterface $response,
        \Morozov\Similarity\Helper\Router $routerHelper
    ) {
        $this->actionFactory = $actionFactory;
        $this->response = $response;
        $this->routerHelper = $routerHelper;
    }

    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        if (!$this->getFrontName()) {
            return false;
        }

        if (stristr($request->getPathInfo(), '/' . $this->getFrontName())) {
            if ($productId = $this->routerHelper->getProductIdByUrl($request->getPathInfo())) {
                $request
                    ->setModuleName('catalogsearch')
                    ->setControllerName('advanced')
                    ->setActionName('result');
                $request
                    //->setParam('similar', 1)    // also works
                    ->setQueryValue([
                        'similar' => $productId,
                        /*
                        'product_list_order' => 'price', // name
                        'product_list_dir'   => 'desc',
                        'name'        => 'bottle',
                        'description' => 'water',
                        'sku'         => '24',
                        'price[from]' => '5',
                        'price[to]'   => '500',
                        */
                    ]);

                return $this->actionFactory->create(
                    'Magento\Framework\App\Action\Forward',
                    ['request' => $request]
                );
            }
        }

        return false;
    }

    public function getFrontName()
    {
        return self::FRONT_NAME;
    }
}
