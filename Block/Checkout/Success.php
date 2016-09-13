<?php
/**
 * MMDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDMMM
 * MDDDDDDDDDDDDDNNDDDDDDDDDDDDDDDDD=.DDDDDDDDDDDDDDDDDDDDDDDMM
 * MDDDDDDDDDDDD===8NDDDDDDDDDDDDDDD=.NDDDDDDDDDDDDDDDDDDDDDDMM
 * DDDDDDDDDN===+N====NDDDDDDDDDDDDD=.DDDDDDDDDDDDDDDDDDDDDDDDM
 * DDDDDDD$DN=8DDDDDD=~~~DDDDDDDDDND=.NDDDDDNDNDDDDDDDDDDDDDDDM
 * DDDDDDD+===NDDDDDDDDN~~N........8$........D ........DDDDDDDM
 * DDDDDDD+=D+===NDDDDDN~~N.?DDDDDDDDDDDDDD:.D .DDDDD .DDDDDDDN
 * DDDDDDD++DDDN===DDDDD~~N.?DDDDDDDDDDDDDD:.D .DDDDD .DDDDDDDD
 * DDDDDDD++DDDDD==DDDDN~~N.?DDDDDDDDDDDDDD:.D .DDDDD .DDDDDDDN
 * DDDDDDD++DDDDD==DDDDD~~N.... ...8$........D ........DDDDDDDM
 * DDDDDDD$===8DD==DD~~~~DDDDDDDDN.IDDDDDDDDDDDNDDDDDDNDDDDDDDM
 * NDDDDDDDDD===D====~NDDDDDD?DNNN.IDNODDDDDDDDN?DNNDDDDDDDDDDM
 * MDDDDDDDDDDDDD==8DDDDDDDDDDDDDN.IDDDNDDDDDDDDNDDNDDDDDDDDDMM
 * MDDDDDDDDDDDDDDDDDDDDDDDDDDDDDN.IDDDDDDDDDDDDDDDDDDDDDDDDDMM
 * MMDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDMMM
 *
 * @author José Castañeda <jose@qbo.tech>
 * @category qbo
 * @package qbo\PayPalPlusMx\
 * @copyright   qbo (http://www.qbo.tech)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * 
 * © 2016 QBO DIGITAL SOLUTIONS. 
 *
 */
namespace qbo\PayPalPlusMx\Block\Checkout;

class Success extends \Magento\Checkout\Block\Onepage\Success
{
    const XML_PATH_PENDING_PAYMENT_MESSAGE = 'payment/qbo_paypalplusmx/pending_payment_message';
    const XML_PATH_IS_METHOD_ACTIVE        = 'payment/qbo_paypalplusmx/active';
    const PAYPAL_LOGO                      = 'https://www.paypalobjects.com/webstatic/mktg/logo-center/logotipo_paypal_pagos_seguros.png';
    const PENDING_PAYMENT_STATUS_CODE      = 'payment_review';
    
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface 
     */
    private $_scopeconfig;
    /**
     *
     * @var \Magento\Sales\Model\OrderFactory 
     */
    private $_orderFactory;
    /**
     *
     * @var \Magento\Sales\Model\Order 
     */
    private $_order = false;
    /**
     * Constructor method
     * 
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = [],
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Sales\Model\OrderFactory $orderFactory
    )
    {
        parent::__construct($context, $checkoutSession, $orderConfig, $httpContext, $data);
        $this->_scopeconfig = $scopeConfig;
        $this->_orderFactory = $orderFactory;
    }
    /**
     * Get if method is active
     * 
     * @return bool
     */
    public function getIsMethodActive()
    {
        return $this->getConfigValue(self::XML_PATH_IS_METHOD_ACTIVE);
    }
    /**
     * Load current Order
     * 
     * @return \Magento\Sales\Model\Order
     */
    protected function  _initOrder()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $this->_order = $this->_orderFactory->create()->loadByIncrementId($this->getOrderId());
    }
    /**
     * Check if order has pending payment status
     * 
     * @return boolean
     */
    public function isPaymentPending()
    {
        $this->_initOrder();
        if($this->_order->getStatus() == self::PENDING_PAYMENT_STATUS_CODE){
            return true;
        }
        return false;
    }
    /**
     * Get if payment has pending status
     * 
     * @return string
     */
    public function getPendingMessage()
    {
        if($this->isPaymentPending() && $this->_order->getPayment()->getMethod() == \qbo\PayPalPlusMx\Model\Payment::CODE) {
            return $this->getConfigValue(self::XML_PATH_PENDING_PAYMENT_MESSAGE);
        }
        return '';
    }
    /**
     * Get Paypal logo for success page
     * 
     * @return srtring
     */
    public function getPayPalLogo()
    {
        $this->_initOrder();
        if(!$this->_order->getPayment()){
            return;
        }
        if($this->_order->getPayment()->getMethod() == \qbo\PayPalPlusMx\Model\Payment::CODE) {
           return self::PAYPAL_LOGO;
        }
    }
   /**
     * Get payment store config
     * 
     * @return string
     */
    public function getConfigValue($configPath)
    {
        $value =  $this->_scopeConfig->getValue(
            $configPath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ); 
        return $value;
    }
}