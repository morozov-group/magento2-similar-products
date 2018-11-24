<?php
namespace Morozov\Similarity\Helper;

class Api extends \Magento\Framework\App\Helper\AbstractHelper
{
    const MASTER_URL       = 'https://master.similarity.morozov.group/';
    const PATH_REGIONS     = 'api/regions';

    const PATH_GET_UPSELLS = 'api/view/%s';
    const PATH_REINDEX     = 'api/reindex';

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var \Morozov\Similarity\Helper\Data
     */
    protected $similarityHelper;

    /**
     * @var \Morozov\Similarity\Helper\Product
     */
    protected $productHelper;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Morozov\Similarity\Helper\Data $similarityHelper,
        \Morozov\Similarity\Helper\Product $productHelper
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->similarityHelper = $similarityHelper;
        $this->productHelper = $productHelper;
        parent::__construct(
            $context
        );
    }

    /**
     * Service ==> Magento
     */
    public function getUpSells($productId)
    {
        $url = $this->similarityHelper->getUrl() . sprintf(self::PATH_GET_UPSELLS, $productId);
        if (!$response = @file_get_contents($url)) {
            return [];
        }
        $response = str_replace("NaN", '"NaN"', $response);
        $items = \Zend_Json::decode($response);  // error
        $tempArr = [];
        foreach ($items as $item) {
            if ($item[1] > 0.0000001) {
                $tempArr[$item[0][0]['entity_id']] = $item[0][0]['image'] . $item[1];
            }
        }
        $ids = array_keys(array_unique($tempArr));
        return $ids;
    }

    /**
     * Service <== Magento
     */
    public function setAllProducts()
    {
        //$this->collectProducts();
        $this->productHelper->collect();

        //@TODO: send CSV file to service
        $url = $this->similarityHelper->getUrl() . self::PATH_REINDEX;
        $data = [
            'key' => $this->similarityHelper->getKey(),
            'file' => $this->similarityHelper->getProductsFileUrl()
        ];
        $json = \Zend_Json::encode($data);
        //var_dump($json);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($json)
        ]);
        curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, (int)$this->similarityHelper->getTimeout());
        $result = curl_exec($ch);

        $info = curl_getinfo($ch);
        $error = curl_errno($ch);
        $this->similarityHelper->log($url);
        $this->similarityHelper->log($info['http_code']);
        $this->similarityHelper->log($result);
        if ($error) {
            $message = curl_error($ch);
            throw new \Exception($error . ' ' . $message);
        }

        curl_close($ch);
    }

    public static function cmpImages($a, $b)
    {
        return (int)$a['position_default'] >= (int)$b['position_default'];
    }

    public function getNearestRegion()
    {
        $config = file_get_contents(self::MASTER_URL . self::PATH_REGIONS);

        $now = function () {
            return time() + microtime(true);
        };

        $distances = json_decode($config, true);
        array_walk($distances, function (&$region, $url) use ($now) {
            $start = $now();
            file_get_contents($region);
            $region = number_format($now() - $start, 6);
        });

        asort($distances);
        reset($distances);
        $nearestRegion = key($distances);
        return $nearestRegion;
    }
}
