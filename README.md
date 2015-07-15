# PayReceipt payment gateway for [payum](http://payum.org/)

## Instalation (with symfony2 payum bundle)
add to your composer json
```json
{
    "require": {
        "payum/payum-bundle": "0.14.*",
        "sergeym/payum-pay-receipt": "dev-master"
    }
}
```

Add PayReceiptPaymentFactory to payum:
```php
<?php

// src/Acme/PaymentBundle/AcmePaymentBundle.php

namespace Acme\PaymentBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Sergeym\Payum\PayReceipt\Bridge\Symfony\PayReceiptPaymentFactory;

class AcmePaymentBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('payum');
        $extension->addPaymentFactory(new PayReceiptPaymentFactory());
    }
}
```

