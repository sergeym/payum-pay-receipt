<?php

namespace Sergeym\Payum\PayReceipt;

use Payum\Core\GatewayFactoryInterface;
use Sergeym\Payum\PayReceipt\Action\CaptureAction;
use Sergeym\Payum\PayReceipt\Action\NotifyAction;
use Sergeym\Payum\PayReceipt\Action\StatusAction;
use Sergeym\Payum\PayReceipt\Action\FillOrderDetailsAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\PaymentFactory as CorePaymentFactory;
use Payum\Core\PaymentFactoryInterface;

class PaymentFactory implements PaymentFactoryInterface
{
    /**
     * @var PaymentFactoryInterface
     */
    protected $corePaymentFactory;

    /**
     * @var array
     */
    private $defaultConfig;

    /**
     * @param array                   $defaultConfig
     * @param PaymentFactoryInterface $corePaymentFactory
     */
    public function __construct(array $defaultConfig = array(), PaymentFactoryInterface $corePaymentFactory = null)
    {
        $this->corePaymentFactory = $corePaymentFactory ?: new CorePaymentFactory();
        $this->defaultConfig = $defaultConfig;
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $config = array())
    {
        return $this->corePaymentFactory->create($this->createConfig($config));
    }

    /**
     * {@inheritDoc}
     */
    public function createConfig(array $config = array())
    {
        $config = ArrayObject::ensureArrayObject($config);
        $config->defaults($this->defaultConfig);
        $config->defaults($this->corePaymentFactory->createConfig());

        $config->defaults(array(
            'payum.factory_name' => 'payreceipt',
            'payum.factory_title' => 'Pay receipt',

            'payum.action.capture' => new CaptureAction(),
            'payum.action.notify' => new NotifyAction(),
            'payum.action.status' => new StatusAction(),
            'payum.action.fill_order_details' => new FillOrderDetailsAction(),
        ));

        if (false == $config['payum.api']) {
            $config['payum.default_options'] = array(
                'mapping' => null,
                'gateway' => ['url' => '/you/should/configure/gateway/section/in/config/yml'],
            );

            $config->defaults($config['payum.default_options']);

            $config['payum.api'] = function (ArrayObject $config) {
                $config->validateNotEmpty($config['payum.required_options']);

                $payreceiptConfig = array(
                    'mapping' => $config['mapping'],
                    'gateway' => $config['gateway'],
                );

                return new Api($payreceiptConfig);
            };
        }

        return (array) $config;
    }
}
