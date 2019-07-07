<?php

namespace BajakLautMalaka\PmiDonatur;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campaign extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'admin_id', 'type_id', 'title','image_file_name',
        'image', 'description', 'amount_goal',
        'start_campaign', 'finish_campaign','fundraising',
        'publish'
    ];
    
    protected $appends  = ['amount_donation','ranges_donation'];
    
    /**
     * Global Scope - sort by latest
     */
    protected static function boot()
    {
        parent::boot();
        
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->latest();
        });
    }
    
    /**
     * get amount donations
     *
     * @return void
     */
    public function getAmountDonationAttribute()
    {
        return $this->getDonations()->count();
    }
    /**
     * get relation
     *
     * @return void
     */
    public function getDonations()
    {
        if (class_exists('\BajakLautMalaka\PmiDonatur\Donation')) {
            return $this->hasMany('\BajakLautMalaka\PmiDonatur\Donation', 'campaign_id', 'id');
        }
        return null;
    }
    /**
     * campaign has type
     *
     * @return belongsTo
     */
    public function getType()
    {
        return $this->belongsTo('\BajakLautMalaka\PmiDonatur\CampaignType', 'type_id', 'id');
    }
    /**
     * get campaign published
     *
     * @param boolean $status
     * @param integer $paginate
     * @return void
     */
    public static function getByPublished(bool $status, int $paginate)
    {
        return static::where('publish', $status)
            ->with('getType')
            ->with('getDonations')
            ->paginate($paginate);
    }
    /**
     * update finish campaign
     *
     * @param object $data
     * @param integer $id
     * @return void
     */
    public static function updateFinishCampaign(object $data, int $id)
    {
        if (isset($data->finish_campaign)) {
            $campaign = static::where('id', $id)->first();
            if (!is_null($campaign)) {
                $campaign->finish_campaign = $data->finish_campaign;
                $campaign->update();
                return $campaign;
            }
        }
        return false;
    }
    /**
     * get by keyword
     *
     * @param string $keyword
     * @param integer $paginate
     * @return void
     */
    public static function getByKeyword(string $keyword,int $paginate)
    {
        return static::where('title', 'like', '%' . $keyword . '%')
            ->orWhere('description', 'like', '%' . $keyword . '%')
            ->with('getType')
            ->with('getDonations')
            ->paginate($paginate);
    }
    /**
     * update status published
     *
     * @param integer $id
     * @return void
     */
    public static function updatePublish(int $id)
    {
        $campaign = static::where('id', $id)->first();
        $campaign->publish = !($campaign->publish);
        $campaign->update();
        return $campaign;
    }
    /**
     * get ranges Donation attribute
     *
     * @return void
     */
    public function getRangesDonationAttribute()
    {
        $start      = (is_null($this->start_campaign))? '' : date_format(date_create($this->start_campaign), "j F Y h:m");
        $finish     = (is_null($this->finish_campaign))? '' : date_format(date_create($this->finish_campaign), "j F Y h:m");
        $ranges     = $start.' - '.$finish;
        return $ranges;
    }
    
    public function admin(): BelongsTo
    {
        return $this->belongsTo('\BajakLautMalaka\PmiAdmin\Admin');
    }
}
