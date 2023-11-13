<?php

namespace Fortvision\Platform\Model\History;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

/**
 * Class Action
 * @package Fortvision\Platform\Model\History
 */
class Action extends AbstractSource
{
    const ADD_SUBSCRIPTION = 'addSubscription';
    const USER_LOGIN = 'userLogin';
    const ADD_TO_CART = 'addToCart';
    const REMOVE_FROM_CART = 'removeFromCart';
    const CART_UPDATE = 'cartUpdate';
    const CART_CHECKOUT = 'cartCheckout';

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = [
                ['value' => self::USER_LOGIN, 'label' => __('User Login')],
                ['value' => self::ADD_SUBSCRIPTION, 'label' => __('Add Subcription')],
                ['value' => self::ADD_TO_CART, 'label' => __('Add To Cart')],
                ['value' => self::REMOVE_FROM_CART, 'label' => __('Remove From Cart')],
                ['value' => self::CART_UPDATE, 'label' => __('Cart Update')],
                ['value' => self::CART_CHECKOUT, 'label' => __('Cart Checkout')],
            ];
        }
        return $this->_options;
    }
}
