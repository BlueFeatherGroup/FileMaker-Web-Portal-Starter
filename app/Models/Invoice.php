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

    // always add a where clause to finding invoices so that we only get invoices for the currently logged-in user
    protected static function booted()
    {
        static::addGlobalScope(new BelongsToUserClientScope());
        parent::booted();
    }

    public function lineItems(){
        return $this->hasMany(LineItem::class);
    }


}
