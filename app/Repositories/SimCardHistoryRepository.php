<?php


namespace App\Repositories;

use Illuminate\Support\Facades\Auth;
use App\Models\SimCardHistory;
use App\Models\SimStatus;

class SimCardHistoryRepository
{

    public function createHistory(
        int $simCardId,
        int $newStatusId,
        ?int $previousStatusId = null,
        array $options = []
    ) {
        // Підготовка даних про зміни
        $changesData = $this->prepareChangesData(
            $newStatusId,
            $previousStatusId,
            $options['additional_changes'] ?? []
        );

        return SimCardHistory::create([
            'sim_card_id' => $simCardId,
            'equipment_id' => $options['equipment_id'] ?? null,
            'operation_date_time' => now(),
            'operation' => $options['operation'] ?? 'status_change',
            'replace_to_acc_id' => $options['replace_to_acc_id'] ?? null,
            'user_id' => Auth::id(),
            'status_id' => $newStatusId,
            'previous_sim_status_id' => $previousStatusId,
            'changes_data' => $changesData,
            'change_source' => $options['change_source'] ?? 'manual',
            'change_reason' => $options['change_reason'] ?? null,
            'notes' => $options['notes'] ?? null,
        ]);
    }

    /**
     * Підготувати дані про зміни для JSON
     */
    private function prepareChangesData(
        int $newStatusId,
        ?int $previousStatusId,
        array $additionalChanges = []
    ) {
        $changes = [];

        if ($previousStatusId !== $newStatusId) {
            // Один запит замість двох
            $statuses = SimStatus::whereIn('id', array_filter([
                $previousStatusId,
                $newStatusId
            ]))->get()->keyBy('id');

            $oldStatus = $statuses->get($previousStatusId);
            $newStatus = $statuses->get($newStatusId);

            $changes['status'] = [
                'old' => $oldStatus?->name ?? 'Немає',
                'new' => $newStatus?->name ?? 'Невідомо',
                'old_id' => $previousStatusId,
                'new_id' => $newStatusId,
                'old_color' => $oldStatus?->color,
                'new_color' => $newStatus?->color
            ];
        }

        // Додаємо інші зміни
        foreach ($additionalChanges as $field => $change) {
            if (isset($change['old']) && isset($change['new']) && $change['old'] !== $change['new']) {
                $changes[$field] = [
                    'old' => $change['old'],
                    'new' => $change['new']
                ];
            }
        }

        return $changes;
    }

    /**
     * Отримати історію SIM-карти
     */
    public function getHistory(int $simCardId, int $limit = 50)
    {
        return SimCardHistory::with(['status', 'previousStatus', 'user', 'equipment'])
            ->where('sim_card_id', $simCardId)
            ->orderBy('operation_date_time', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Отримати останню зміну
     */
    public function getLastChange(int $simCardId)
    {
        return SimCardHistory::with(['status', 'previousStatus'])
            ->where('sim_card_id', $simCardId)
            ->latest('operation_date_time')
            ->first();
    }

    /**
     * Отримати статистику змін за період
     */
    public function getStatistics($startDate, $endDate)
    {
        return SimCardHistory::whereBetween('operation_date_time', [$startDate, $endDate])
            ->selectRaw('
                status_id,
                change_source,
                COUNT(*) as total_changes
            ')
            ->groupBy('status_id', 'change_source')
            ->with('status')
            ->get();
    }
}
