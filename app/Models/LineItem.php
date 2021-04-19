<?php

namespace App\Models;

use BlueFeather\EloquentFileMaker\Database\Eloquent\FMModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LineItem extends FMModel
{
    use HasFactory;

    protected $layout = 'invoicedata';
    protected $keyType = 'string';

    protected $fieldMapping = [
        'INVOICE ID MATCH FIELD' => 'invoice_id',
        'Unit Price' => 'unit_price',
    ];

}
