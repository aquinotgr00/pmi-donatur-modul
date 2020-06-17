<?php

namespace BajakLautMalaka\PmiDonatur;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CampaignType extends Model
{
    /**
     * fillable data campaign type
     *
     * @var array
     */
    protected $fillable = ['name','description'];
    /**
     * get all campaign by type
     *
     * @return HasMany
     */
    public function campaigns(): HasMany
    {
        return $this->hasMany('\BajakLautMalaka\PmiDonatur\Campaign','type_id','id');
    }
}
