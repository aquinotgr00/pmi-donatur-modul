<?php

namespace BajakLautMalaka\PmiDonatur;

use Illuminate\Database\Eloquent\Model;

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
     * @return hasMany
     */
    public function getCampaign(): hasMany
    {
        return $this->hasMany('\BajakLautMalaka\PmiDonatur\Campaign','type_id','id');
    }
}
