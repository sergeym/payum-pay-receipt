<?php

namespace Sergeym\Payum\PayReceipt\Bridge\Symfony;

use Payum\Bundle\PayumBundle\DependencyInjection\Factory\Payment\AbstractPaymentFactory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\Routing\Router;

class PayReceiptPaymentFactory extends AbstractPaymentFactory implements PrependExtensionInterface
{
    private $container;

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'pay_receipt';
    }

    /**
     * {@inheritDoc}
     */
    public function addConfiguration(ArrayNodeDefinition $builder)
    {
        parent::addConfiguration($builder);

        $builder->children()
                ->arrayNode('gateway')
                    ->isRequired()
                    ->children()
                        ->scalarNode('url')->isRequired()->end()
                        /*->arrayNode('route')
                            ->children()
                                ->scalarNode('name')->isRequired()->end()
                                ->arrayNode('params')->useAttributeAsKey('param')->prototype('variable')->end()->end()
                            ->end()
                        ->end()*/
                    ->end()
                ->end()
                ->arrayNode('mapping')
                    ->isRequired()
                    ->useAttributeAsKey('name')
                    ->prototype('scalar')
                ->end()
            ->end();
    }

    /**
     * {@inheritDoc}
     */
    protected function getPayumPaymentFactoryClass()
    {
        return 'Sergeym\Payum\PayReceipt\PaymentFactory';
    }

    /**
     * {@inheritDoc}
     */
    protected function getComposerPackage()
    {
        return 'sergeym/payum-payreceipt';
    }

    /**
     * Allow an extension to prepend the extension configurations.
     *
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        $this->container = $container;
    }
}
