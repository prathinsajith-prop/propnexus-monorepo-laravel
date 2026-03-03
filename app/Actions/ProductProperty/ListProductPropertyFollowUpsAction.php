<?php

declare(strict_types=1);

namespace App\Actions\ProductProperty;

use App\Enums\FollowUpType;
use App\Models\BixoSchedulesFollowUp;
use Litepie\Actions\ActionResult;
use Litepie\Actions\BaseAction;

/**
 * ListProductPropertyFollowUpsAction
 *
 * Retrieve all follow-ups for a given product property.
 */
class ListProductPropertyFollowUpsAction extends BaseAction
{
    protected function rules(): array
    {
        return [
            'property_id' => 'required|integer',
            'limit' => 'sometimes|nullable|integer|min:1',
        ];
    }

    public function handle(): ActionResult
    {
        try {
            $query = BixoSchedulesFollowUp::with(['property', 'createdBy'])
                ->where('property_id', $this->data['property_id'])
                ->orderBy('start_date', 'asc');

            if (! empty($this->data['limit'])) {
                $query->limit((int) $this->data['limit']);
            }

            $followUps = $query->get()
                ->map(fn($item) => $this->formatFollowUp($item))
                ->values()
                ->all();

            return ActionResult::success([
                'data' => $followUps,
                'meta' => ['total' => count($followUps)],
            ], 'Follow-ups retrieved successfully');
        } catch (\Exception $e) {
            return ActionResult::failure('Failed to retrieve follow-ups: ' . $e->getMessage());
        }
    }

    /**
     * Format a follow-up record for the API response.
     *
     * @return array<string, mixed>
     */
    private function formatFollowUp(BixoSchedulesFollowUp $item): array
    {
        $details = $item->details ? json_decode($item->details, true) : [];

        return [
            'eid' => $item->eid,
            'property_id' => $item->property->eid,
            'followup_title' => $item->title,
            'followup_date' => $item->start_date?->toISOString(),
            'followup_date_formatted' => $item->start_date?->format('d M Y H:i'),
            'followup_date_day' => $item->start_date?->format('j'),
            'followup_date_month' => $item->start_date ? strtoupper($item->start_date->format('M')) : null,
            'followup_type' => $item->type,
            'followup_type_label' => FollowUpType::tryFrom($item->type)?->label(),
            'description' => $item->description,
            'send_reminder' => $details['send_reminder'] ?? false,
            'status' => $item->status?->value,
            'created_by' => $item->created_by,
            'created_by_name' => $item->createdBy?->name,
            'created_at' => $item->created_at?->toISOString(),
            'updated_at' => $item->updated_at?->toISOString(),
        ];
    }
}
