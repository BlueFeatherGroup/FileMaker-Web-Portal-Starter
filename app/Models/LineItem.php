<?php

namespace App\Models;

use BlueFeather\EloquentFileMaker\Database\Eloquent\FMModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LineItem extends FMModel
{
    use HasFactory;

    protected $layout = 'lineitems';
    protected $keyType = 'string';

}
