<?php

namespace Fortvision\Platform\Block\Customer\Form;

use Fortvision\Platform\Provider\GeneralSettings;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class FortvisionSubscription
 * @package Fortvision\Platform\Block\Customer\Form
 */
class FortvisionSubscription extends Template
{
    /**
     * @var array
     */
    protected $cusomerForm = [
        'signup',
        'both'
    ];

    /**
     * @var GeneralSettings
     */
    protected $generalSettings;

    /**
     * FortvisionSubscription constructor.
     * @param Context $context
     * @param GeneralSettings $generalSettings
     * @param array $data
     */
    public function __construct(
        Context $context,
        GeneralSettings $generalSettings,
        array $data = []
    ) {
        $this->generalSettings = $generalSettings;
        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    public function isAllowed()
    {
        return in_array($this->generalSettings->getPages(), $this->cusomerForm);
    }

    /**
     * @return mixed
     */
    public function getCheckboxText()
    {
        return $this->generalSettings->getCheckboxText();
    }

    /**
     * @return string
     */
    public function isDefaultChecked()
    {
        return $this->generalSettings->isDefaultChecked() ? 'checked' : '';
    }
}
