<?php

namespace App\Models;

use BlueFeather\EloquentFileMaker\Database\Eloquent\FMModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends FMModel
{
    use HasFactory;

    protected $connection = 'fm-invoices';
    protected $layout = 'customers';

    protected $keyType = 'string';

    protected $fieldMapping = [
        'CUSTOMER ID MATCH FIELD' => 'id',
        'Address 1' => 'address_1',
        'Address 2' => 'address_2',
        'Postal Code' => 'postal_code',
        'Office Email' => 'office_email',
        'Home Email' => 'home_email'
    ];

    public function outstandingInvoices(){
        return $this->hasMany(Invoice::class)->where('date_payment', "=")->where('customer_id', "=" . $this->id);
    }
}
