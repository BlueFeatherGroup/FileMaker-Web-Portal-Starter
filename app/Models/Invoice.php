<?php

namespace App\Models;

use App\Scopes\BelongsToUserClientScope;
use BlueFeather\EloquentFileMaker\Database\Eloquent\FMModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends FMModel
{
    use HasFactory;

    protected $connection = 'fm-invoices';

    protected $keyType = 'string';

    protected $fieldMapping = [
        'Date Payment' => 'date_payment',
        'Invoice ID' => 'id',
        'Tax Rate' => 'tax_rate',
        'CUSTOMER ID MATCH FIELD' => 'customer_id',
    ];

    public function lineItems(){
        return $this->hasMany(LineItem::class);
    }


}
