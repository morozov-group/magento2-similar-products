<?php
namespace Morozov\Similarity\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const LOG_FILE      = 'morozov_similarity.log';

    const PATH_ENABLED  = 'morozov_similarity/general/active';
    const PATH_EMAIL    = 'morozov_similarity/general/email';
    const PATH_URL      = 'morozov_similarity/general/url';
    const PATH_KEY      = 'morozov_similarity/general/key';
    const PATH_TIMEOUT  = 'morozov_similarity/general/timeout';

    const PATH_UPSELL_MAXCOUNT = 'morozov_similarity/upsell_options/upsell_max_count';

    const EXPORT_DIR    = 'morozov_similarity';
    const PRODUCTS_FILE = 'products.csv';

    /**
     * Uses external CSV file with Products to process
     */
    const LOCAL_MODE    = 0;

    /**
     * @var \Magento\Framework\Logger\Monolog
     */
    protected $logger;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $directoryList;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    //protected $scopeCode;

    protected $store;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\App\State $state
    ) {
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->directoryList = $directoryList;
        $this->state = $state;

        $logPath = $this->directoryList->getPath('log');
        $handler = new \Monolog\Handler\StreamHandler($logPath . DIRECTORY_SEPARATOR . self::LOG_FILE);
        $this->logger->pushHandler($handler);

        parent::__construct(
            $context
        );
    }

    public function log($message)
    {
        $this->logger->log(\Psr\Log\LogLevel::INFO, $message);
    }

    public function getIsEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::PATH_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getScopeCode()
        );
    }

    public function getEmail()
    {
        return $this->scopeConfig->getValue(
            self::PATH_EMAIL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getScopeCode()
        );
    }

    public function getUrl()
    {
        return $this->scopeConfig->getValue(
            self::PATH_URL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getScopeCode()
        );
    }

    public function getKey()
    {
        return $this->scopeConfig->getValue(
            self::PATH_KEY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getScopeCode()
        );
    }

    public function getTimeout()
    {
        return $this->scopeConfig->getValue(
            self::PATH_TIMEOUT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getScopeCode()
        );
    }

    public function getUpSellMaxCount()
    {
        return (int)$this->scopeConfig->getValue(
            self::PATH_UPSELL_MAXCOUNT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getScopeCode()
        );
    }

    public function canUse()
    {
        //var_dump($this->state->getAreaCode());                  // frontend  adminhtml
        //var_dump(\Magento\Framework\App\Area::AREA_ADMINHTML);  // adminhtml
        //var_dump(\Magento\Framework\App\Area::AREA_FRONTEND);   // frontend
        $res = $this->getIsEnabled() && $this->getUrl() && $this->getKey();
        $res = $res && ($this->state->getAreaCode() != \Magento\Framework\App\Area::AREA_ADMINHTML);
        return $res;
    }

    public function getExportDir()
    {
        $pubMediaDir = $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $dir = $pubMediaDir . DIRECTORY_SEPARATOR . self::EXPORT_DIR;
        return $dir;
    }

    protected function getProductsFileName()
    {
        $filename = $this->getStore() ? "products_{$this->getStoreId()}.csv" : 'products.csv';
        return $filename;
    }

    public function getProductsFile()
    {
        $file = $this->getExportDir() . DIRECTORY_SEPARATOR . $this->getProductsFileName();
        return $file;
    }

    public function getProductsFileUrl()
    {
        if (self::LOCAL_MODE) {
            return 'https://www.bragardusa.net/media/morozov_similarity/products-m23.csv';
        }

        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $url = $mediaUrl . self::EXPORT_DIR . '/' . self::PRODUCTS_FILE;
        return $url;
    }

    /*
    public function setScopeCode($scopeCode)
    {
        $this->scopeCode = $scopeCode;
    }
    */

    public function setStore($store)
    {
        $this->store = $store;
    }

    public function getStore()
    {
        return $this->store;
    }

    public function getScopeCode()
    {
        $code = $this->getStore() ? $this->getStore()->getCode() : null;
        return $code;
    }

    public function getStoreId()
    {
        $id = $this->getStore() ? (int)$this->getStore()->getId() : null;
        return $id;
    }

    public function getStores()
    {
        $stores = $this->storeManager->getStores();
        return $stores;
    }
}
