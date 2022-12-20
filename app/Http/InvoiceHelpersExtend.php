<?php

namespace App\Http;


use LaravelDaily\Invoices\Invoice;
use LaravelDaily\Invoices\Traits\InvoiceHelpers;


class InvoiceHelpersExtend
{
    use Invoice {
        Invoice::getCustomData as getInvoiceNumber;
        Invoice::setCustomData as setInvoiceNumber;
    }

    public function getCustomData()
    {
        return $this->getInvoiceNumber();
    }

    public function setCustomData($value)
    {
        return $this->setInvoiceNumber($value);
    }
}
