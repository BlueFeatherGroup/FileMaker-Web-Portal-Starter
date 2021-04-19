<?php


namespace App\Scopes;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class BelongsToUserClientScope implements Scope
{

    public function apply(Builder $builder, Model $model)
    {
        // apply a where clause to make sure the object belongs to the user's client by searching the client_id column
        $clientId = Auth::user()->client_id;
        if ($clientId) {
            $builder->where('client_id', "=" . $clientId);
        }
    }
}
