<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\FollowUpStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Litepie\Database\Traits\Searchable;
use Litepie\Hashids\Traits\Hashids;

/**
 * BixoSchedulesFollowUp Model
 *
 * Follow-up scheduling associated with a product property.
 *
 * @property int $id
 * @property string|null $title Follow-up title
 * @property string|null $type Follow-up type (call, meeting, viewing, offer, other)
 * @property \Illuminate\Support\Carbon|null $start_date Scheduled date/time
 * @property string|null $description Notes/description
 * @property string|null $details JSON details (e.g. send_reminder flag)
 * @property int|null $property_id Related product property ID
 * @property int|null $created_by User who created the follow-up
 */
class BixoSchedulesFollowUp extends Model
{
    use Hashids;
    use Searchable;
    use SoftDeletes;

    protected $table = 'bixo_schedules_follow_ups';

    protected $guarded = [];

    protected $appends = ['eid'];

    protected $hidden = ['id'];

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'notified_at' => 'datetime',
            'status' => FollowUpStatus::class,
        ];
    }

    /**
     * Get the searchable fields (override Searchable trait).
     *
     * @return array<int, string>
     */
    public function getSearchFields(): array
    {
        return [
            'title',
            'description',
            'type',
            'status',
            'priority',
        ];
    }

    /**
     * Get filterable fields for query string filtering (override Searchable trait).
     *
     * @return array<int, string>
     */
    protected function getFilterableFields(): array
    {
        return [
            'title',
            'description',
            'type',
            'status',
            'priority',
            'property_id',
            'created_by',
            'assigned_to',
            'start_date',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * The product property this follow-up belongs to.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(BixoProductProperties::class, 'property_id');
    }

    /**
     * The user who created this follow-up.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
