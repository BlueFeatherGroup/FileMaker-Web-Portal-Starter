<?php

namespace App\Models;

use BlueFeather\EloquentFileMaker\Database\Eloquent\FMModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LineItem extends FMModel
{
    use HasFactory;

    protected $connection = 'fm-invoices';
    protected $layout = 'invoicedata';
    protected $keyType = 'string';
    protected $fieldMapping = [
        'INVOICE ID MATCH FIELD' => 'invoice_id',
        'Item' => 'description',
        'Qty' => 'qty',
        'Unit Price' => 'unit_price',
        'Amount' => 'extended_price'
    ];

}
