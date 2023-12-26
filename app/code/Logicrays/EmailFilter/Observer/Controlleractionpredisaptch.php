<?php
/**
 * Logicrays
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Logicrays
 * @package     Logicrays_EmailFilter
 * @copyright   Copyright (c) Logicrays (https://www.logicrays.com/)
 */
declare(strict_types=1);

namespace Logicrays\EmailFilter\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\UrlInterface;
use Magento\Checkout\Model\Session;
use Magento\Customer\Model\Session as CustomerSession;
use Logicrays\EmailFilter\Helper\Data;
use Magento\Framework\App\Response\Http;

/**
 * Class Controlleractionpredisaptch called sales order place before
 */
class Controlleractionpredisaptch implements ObserverInterface
{
    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var ResultFactory
     */
    protected $resultFactory;

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var ResponseFactory
     */
    private $responseFactory;

    /**
     * @var Http
     */
    protected $http;

    /**
     * Controlleractionpredisaptch constructor
     *
     * @param RequestInterface $request
     * @param ManagerInterface $messageManager
     * @param ResponseFactory $responseFactory
     * @param UrlInterface $url
     * @param Session $checkoutSession
     * @param CustomerSession $session
     * @param Data $helper
     * @param Http $http
     * @param ResultFactory $resultFactory
     */
    public function __construct(
        RequestInterface $request,
        ManagerInterface $messageManager,
        ResponseFactory $responseFactory,
        UrlInterface $url,
        Session $checkoutSession,
        CustomerSession $session,
        Data $helper,
        Http $http,
        ResultFactory $resultFactory
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->helper = $helper;
        $this->session = $session;
        $this->request = $request;
        $this->messageManager = $messageManager;
        $this->resultFactory = $resultFactory;
        $this->responseFactory = $responseFactory;
        $this->url = $url;
        $this->http = $http;
    }

    /**
     * This function check admin settings and based on it email send
     *
     * @param Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->helper->isEnabled() && $this->helper->getCheckoutRestriction()) {
            $order = $observer->getEvent()->getOrder();
            $checkoutSession = $this->checkoutSession;
            
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $postData = $order->getShippingAddress()->getEmail();
            $emailExpression = preg_split('/\r\n|[\r\n]/', $this->helper->getEmailrestricton());
            $isValidEmail = true;
            foreach ($emailExpression as $expression) {
                preg_match('/'.$expression.'/', $postData, $matches);

                if (!empty($matches)) {
                    $isValidEmail = false;
                    throw new NoSuchEntityException(__('Sorry, your e-mail address is not available at this store.'));
                }
            }
        }
    }
}
