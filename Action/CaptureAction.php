<?php

namespace Sergeym\Payum\PayReceipt\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\Reply\HttpPostRedirect;
use Payum\Core\Request\Capture;
use Sergeym\Payum\PayReceipt\Api;

class CaptureAction implements ActionInterface, ApiAwareInterface
{
    /**
     * @var Api
     */
    protected $api;

    /**
     * {@inheritDoc}
     */
    public function setApi($api)
    {
        if (false === $api instanceof Api) {
            throw new UnsupportedApiException('Not supported.');
        }

        $this->api = $api;
    }

    /**
     * {@inheritDoc}
     */
    public function execute($request)
    {
        /* @var $request Capture */
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var \Payum\Core\Bridge\Spl\ArrayObject $details */
        $details = ArrayObject::ensureArrayObject($request->getModel());

        throw new HttpPostRedirect($this->api->getPaymentPageUrl(), $details->toUnsafeArray());
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Capture &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
