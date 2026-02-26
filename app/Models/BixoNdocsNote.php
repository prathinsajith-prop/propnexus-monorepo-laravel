<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\NoteType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Litepie\Database\Traits\Searchable;
use Litepie\Hashids\Traits\Hashids;

/**
 * BixoNdocsNote Model
 *
 * Polymorphic note that can be attached to any subject (e.g. product property).
 *
 * @property int $id
 * @property string|null $note Note content
 * @property string|null $attachments JSON-encoded attachment list
 * @property int|null $subject_id Polymorphic subject ID
 * @property string|null $subject_type Polymorphic subject class
 * @property NoteType|null $type Note type
 * @property int|null $user_id Author user ID
 */
class BixoNdocsNote extends Model
{
    use Hashids;
    use Searchable;
    use SoftDeletes;

    protected $table = 'bixo_ndocs_notes';

    protected $guarded = [];

    protected $appends = ['eid'];

    protected $hidden = ['id'];

    protected function casts(): array
    {
        return [
            'type' => NoteType::class,
        ];
    }

    /**
     * The product property this note belongs to (when subject_type is BixoProductProperties).
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(BixoProductProperties::class, 'subject_id');
    }
}
