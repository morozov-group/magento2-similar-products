<?php
namespace Morozov\Similarity\Helper;

class Product extends \Magento\Framework\App\Helper\AbstractHelper
{
    const CHECK_IMAGE_FILE_EXISTS = true;

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

    public function collect()
    {
        $csvDir = $this->similarityHelper->getExportDir();
        $pubMediaDir = $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl() . 'media';

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
        $res = $read->query($this->similaritySqlHelper->prepareExportProducts((int)$this->similarityHelper->getStoreId()));
        if ($res) {
            $count = 0;
            while($row = $res->fetch(\PDO::FETCH_ASSOC)) {
                $images = explode(',', $row['images']);
                $image = $images[0];
                if ($this->similarityHelper->getImageCheckEnabled()) {
                    $isFile = is_file($pubMediaDir . DIRECTORY_SEPARATOR . 'catalog' . DIRECTORY_SEPARATOR . 'product' . $image);
                    if (!$isFile) {
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
}
