<?php

namespace LR\CustomPayment\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\ResourceConnection;

class CustomPaymentPlaceOrder implements ObserverInterface
{
    public const CUSTOM_PAYMENT_ACTIVE = 'payment/custompayment/active';
    public const CUSTOM_PAYMENT_STATUS = 'payment/custompayment/order_status';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Undocumented function
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        ResourceConnection $resourceConnection,
        \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory $invoiceCollectionFactory,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        \Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepository,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->resourceConnection = $resourceConnection;
        $this->_invoiceCollectionFactory = $invoiceCollectionFactory;
        $this->_invoiceService = $invoiceService;
        $this->_transactionFactory = $transactionFactory;
        $this->_invoiceRepository = $invoiceRepository;
        $this->_orderRepository = $orderRepository;
    }

    /**
     * Update dispatch date
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return string
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->getModuleEnable() == 1) {
            $order = $observer->getEvent()->getOrder();
            $paymentMethodName = $order->getPayment()->getMethod();
            if ($paymentMethodName == 'custompayment') {
                $orderIncrementId = $order->getIncrementId();
                $orderStatus = $this->getStatus();
                $connection = $this->resourceConnection->getConnection();
                $tableName = $this->resourceConnection->getTableName('sales_order_status_state');
                $select = $connection->select()
                    ->from($tableName)
                    ->where('status = ?', $orderStatus);
                $getTableData = $connection->fetchAll($select);
                $orderState = $getTableData[0]['state'];
                $order->setState($orderState)->setStatus($orderStatus);
                $order->save();
            }
        }
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function getModuleEnable()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::CUSTOM_PAYMENT_ACTIVE, $storeScope);
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function getStatus()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::CUSTOM_PAYMENT_STATUS, $storeScope);
    }
}
