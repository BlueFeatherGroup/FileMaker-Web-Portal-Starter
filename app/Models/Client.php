<?php

namespace App\Models;

use BlueFeather\EloquentFileMaker\Database\Eloquent\FMModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends FMModel
{
    use HasFactory;

    protected $connection = 'fm-invoices';
    protected $layout = 'clients';

    protected $keyType = 'string';

    public function outInvoices(){
        return $this->hasMany(Invoice::class)->where('remainingBalance_c', ">0");
    }
}
