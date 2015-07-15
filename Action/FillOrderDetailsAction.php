<?php

namespace Sergeym\Payum\PayReceipt\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\Request\FillOrderDetails;
use Payum\Core\Bridge\Spl\ArrayObject;
use Sergeym\Payum\PayReceipt\Api;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;

class FillOrderDetailsAction implements ActionInterface, ApiAwareInterface
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
        if (false == $api instanceof Api) {
            throw new UnsupportedApiException('Not supported.');
        }

        $this->api = $api;
    }

    /**
     * {@inheritDoc}
     *
     * @param FillOrderDetails $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $order = $request->getOrder();

        $details = ArrayObject::ensureArrayObject($order->getDetails());

        $mapping = $this->api->getMapping();
        $default = [];

        foreach($mapping as $_key => $_value) {
            $details[$_key] = $this->getObjectValue($request, $_value);
            $default[$_key] = '';
        }

        $details->defaults($default);
        $order->setDetails($details);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return $request instanceof FillOrderDetails;
    }

    /**
     * Convert getter chain strings into final result
     * @param $object
     * @param $_value
     * @return string
     */
    private function getObjectValue($object, $_value)
    {
        if ($_value) {
            $_valueList = explode(' ', $_value);
            if (count($_valueList)>1) {
                $result = '';
                foreach($_valueList as $_v) {
                    $str = $this->getObjectValue($object, $_v).' ';
                    $result .= $str;
                }
                return trim($result);
            }

            $decorators = explode('~',$_value);

            if (count($decorators)==3) {
                $_value = $decorators[1];
            }

            if ($_value[0]=='$') {

                $_value = substr($_value,1);
                $chain = explode('.', $_value);

                if (is_array($object)) {
                    $_method = $chain[0];
                    $_resultObject = isset($object[$_method]) ? $object[$_method] : '';
                } elseif (is_object($object)) {
                    $_method = 'get' . ucfirst($chain[0]);
                    if (method_exists($object, $_method)) {
                        $_resultObject = $object->$_method();
                    } else {
                        $_resultObject = $_value;
                    }
                } else {
                    $_resultObject = '';
                }

                if (count($chain)>1) {
                    $_nextVal = '$'.join('.', array_slice($chain, 1));
                    $_resultObject = $this->getObjectValue($_resultObject, $_nextVal);
                }

                if (count($decorators)==3 && $_resultObject) {
                    $_resultObject = $decorators[0].$_resultObject.$decorators[2];
                }

            } else {
                $_resultObject = '';
            }

            return $_resultObject;

        } else {
            return '';
        }
    }
}
