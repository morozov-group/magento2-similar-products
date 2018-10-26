<?php
namespace Morozov\Similarity\Helper;

class Api extends \Magento\Framework\App\Helper\AbstractHelper
{
    const CHECK_IMAGE_FILE_EXISTS = true;

    const MASTER_URL       = 'https://master.similarity.morozov.group/';
    const PATH_REGIONS     = 'api/regions';

    const PATH_GET_UPSELLS = 'api/view/%s';
    const PATH_REINDEX     = 'api/reindex';

    protected $csvColumns = [
        'entity_id',
        'is_in_stock',
        'image'
    ];

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var \Morozov\Similarity\Helper\Data
     */
    protected $similarityHelper;

    /**
     * @var \Morozov\Similarity\Helper\Sql
     */
    protected $similaritySqlHelper;

    protected $directoryList;

    protected $storeManager;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Morozov\Similarity\Helper\Data $similarityHelper,
        \Morozov\Similarity\Helper\Sql $similaritySqlHelper,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->similarityHelper = $similarityHelper;
        $this->similaritySqlHelper = $similaritySqlHelper;
        $this->directoryList = $directoryList;
        $this->storeManager = $storeManager;
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

    public function collectProducts()
    {
        $csvDir = $this->similarityHelper->getExportDir();
        $pubMediaDir = $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

        if (!is_dir($csvDir)) {
            if (!mkdir($csvDir)) {
                throw new \Exception('Failed to create export directory..');
            }
        }
        if (!$f = fopen($this->similarityHelper->getProductsFile(), 'w+')) {
            throw new \Exception('Failed to create export Products file..');
        }
        fputcsv($f, $this->csvColumns);

        $resource = $this->resourceConnection;
        $read = $resource->getConnection('core_read');
        $res = $read->query($this->similaritySqlHelper->prepareExportProducts());
        if ($res) {
            $count = 0;
            while($row = $res->fetch(\PDO::FETCH_ASSOC)) {
                $images = explode(',', $row['images']);
                $image = $images[0];
                if (self::CHECK_IMAGE_FILE_EXISTS) {
                    $fileExists = file_exists($pubMediaDir . DIRECTORY_SEPARATOR . 'catalog' . DIRECTORY_SEPARATOR . 'product' . $image);
                    if (!$fileExists) {
                        continue;
                    }
                }
                $url = $mediaUrl . 'catalog/product' . $image;
                $csvRow = [
                    $row['entity_id'],
                    $row['is_in_stock'],
                    $url
                ];
                fputcsv($f, $csvRow);
                $count++;
            }
            $this->similarityHelper->log("Exported  $count  products");
        } else {
            throw new \Exception('Failed to execute SQL..');
        }

        fclose($f);
    }

    /**
     * Service <== Magento
     */
    public function setAllProducts()
    {
        $this->collectProducts();

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
