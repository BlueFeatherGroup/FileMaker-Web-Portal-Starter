<?php

namespace App\Models;

use App\Scopes\BelongsToUserClientScope;
use BlueFeather\EloquentFileMaker\Database\Eloquent\FMModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends FMModel
{
    use HasFactory;

    protected $connection = 'fm-invoices';
    protected $layout = 'invoices';
    protected $keyType = 'string';
    protected $fieldMapping = [
        'CUSTOMER ID MATCH FIELD' => 'customer_id',
        'Invoice ID' => 'id',
        'Date' => 'date',
        'Date Payment' => 'paid_on',
        'Summary' => 'summary',
        'Subtotal' => 'subtotal',
        'Tax' => 'tax',
        'Tax Rate' => 'tax_rate',
        'Total' => 'total'
    ];

    public function lineItems(){
        return $this->hasMany(LineItem::class);
    }


}
