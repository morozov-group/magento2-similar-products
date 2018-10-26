<?php
namespace Morozov\Similarity\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const LOG_FILE      = 'morozov_similarity.log';

    const PATH_ENABLED  = 'morozov_similarity/general/active';
    const PATH_URL      = 'morozov_similarity/general/url';
    const PATH_KEY      = 'morozov_similarity/general/key';
    const PATH_EMAIL    = 'morozov_similarity/general/email';
    const PATH_PASSWORD = 'morozov_similarity/general/password';
    const PATH_TIMEOUT  = 'morozov_similarity/general/timeout';

    const PATH_UPSELL_MAXCOUNT = 'morozov_similarity/upsell_options/upsell_max_count';

    const EXPORT_DIR    = 'morozov_similarity';
    const PRODUCTS_FILE = 'products.csv';

    /**
     * @var \Psr\Log\LoggerInterface
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

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList
    ) {
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->directoryList = $directoryList;
        parent::__construct(
            $context
        );
    }


    public function log($message)
    {
        $this->logger->log(null, $message);
    }

    public function getIsEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::PATH_ENABLED, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getUrl()
    {
        return $this->scopeConfig->getValue(self::PATH_URL, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getKey()
    {
        return $this->scopeConfig->getValue(self::PATH_KEY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getEmail()
    {
        return $this->scopeConfig->getValue(self::PATH_EMAIL, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getPassword()
    {
        return $this->scopeConfig->getValue(self::PATH_PASSWORD, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getTimeout()
    {
        return $this->scopeConfig->getValue(self::PATH_TIMEOUT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getUpSellMaxCount()
    {
        return (int)$this->scopeConfig->getValue(self::PATH_UPSELL_MAXCOUNT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function canUse()
    {
        $res = $this->getIsEnabled() && $this->getUrl() && $this->getKey();
        $res = $res && (!$this->storeManager->getStore()->isAdmin());
        return $res;
    }

    public function getExportDir()
    {
        $pubMediaDir = $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $dir = $pubMediaDir . DIRECTORY_SEPARATOR . self::EXPORT_DIR;
        return $dir;
    }

    public function getProductsFile()
    {
        $file = $this->getExportDir() . DIRECTORY_SEPARATOR . self::PRODUCTS_FILE;
        return $file;
    }

    public function getProductsFileUrl()
    {
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $url = $mediaUrl . self::EXPORT_DIR . '/' . self::PRODUCTS_FILE;
        return $url;
    }
}
