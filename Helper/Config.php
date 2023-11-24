<?php

namespace PixieMedia\Suggestion\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    public const XML_PATH_RANDOM = 'pixie_suggested/options/random';
    public const XML_PATH_LIMIT = 'pixie_suggested/options/limit';
    public const XML_PATH_PANEL_TITLE = 'pixie_suggested/options/panel_title';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Config constructor.
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Load config value for random option
     *
     * @return mixed
     */
    public function hasRandom()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_RANDOM,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Load config value for limit option
     *
     * @return mixed
     */
    public function getLimit()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_LIMIT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Load config value for panel title option
     *
     * @return mixed
     */
    public function getTitle()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PANEL_TITLE,
            ScopeInterface::SCOPE_STORE
        );
    }
}
