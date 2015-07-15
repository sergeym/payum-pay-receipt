<?php

namespace Sergeym\Payum\PayReceipt;

class Api
{
    /**
     * @var array
     */
    private $config;

    /**
     * Constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getPaymentPageUrl()
    {
        return $this->config['gateway']['url'];
    }

    public function getMapping()
    {
        return $this->config['mapping'];
    }

}
