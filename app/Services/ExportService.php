<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\ExportServiceInterface;
use App\Models\LearningUnit;
use App\Models\LearningUnitRecommendItem;
use App\Models\User;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportService implements ExportServiceInterface
{
    public function exportLearningUnits(array $filters): StreamedResponse
    {
        $filename = __('cms.list_title', ['resource' => __('cms.learning_units')]) . '-' . now()->format('Y-m-d') . '.csv';
        $headers = [
            __('exports.col_no'),
            __('exports.col_code'),
            __('exports.col_lu_name'),
            __('exports.col_lu_description'),
            __('exports.col_lu_applicable_objects'),
            __('exports.col_lu_dietary_title'),
            __('exports.col_lu_dietary_recommendations'),
            __('exports.col_lu_clinical_title'),
            __('exports.col_lu_clinical_notes'),
            __('exports.col_lu_status'),
            __('exports.col_created_at'),
            __('exports.col_updated_at'),
        ];

        $query = LearningUnit::query()
            ->orderBy('sort_order')->orderBy('id');

        return $this->stream($filename, $headers, function () use ($query) {
            $no = 0;
            foreach ($query->cursor() as $unit) {
                yield [
                    ++$no,
                    $unit->code ?? '',
                    $unit->name ?? '',
                    $this->stripHtml($unit->description),
                    is_array($unit->applicable_objects) ? implode(', ', $unit->applicable_objects) : '',
                    $unit->dietary_recommendation_title ?? '',
                    $this->stripHtml($unit->dietary_recommendations),
                    $unit->clinical_note_title ?? '',
                    $this->stripHtml($unit->clinical_notes),
                    $unit->status ? __('exports.status_active') : __('exports.status_inactive'),
                    optional($unit->created_at)->format('Y-m-d H:i:s') ?? '',
                    optional($unit->updated_at)->format('Y-m-d H:i:s') ?? '',
                ];
            }
        });
    }

    public function exportLearningUnit(LearningUnit $unit): StreamedResponse
    {
        $unit->load([
            'items.category',
            'recommendItems.recommendItem.category',
        ]);

        $slug = Str::slug($unit->code ?: ('unit-' . $unit->id));
        $filename = 'learning-unit-' . $slug . '-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($unit) {
            // Build the whole CSV in memory as UTF-8, then convert to UTF-16 LE
            // with BOM. This is the format Microsoft Excel / WPS / Numbers all
            // recognise as CSV with comma delimiter regardless of locale —
            // unlike UTF-8 + sep= hint which WPS on macOS ignores.
            $buffer = fopen('php://temp', 'r+');

            $this->writeLearningUnitSection($buffer, $unit);
            $this->putRow($buffer, []);
            $this->writeItemsSection($buffer, $unit);
            $this->putRow($buffer, []);
            $this->writeRecommendItemsSection($buffer, $unit);

            rewind($buffer);
            $utf8 = stream_get_contents($buffer);
            fclose($buffer);

            // Excel expects CRLF line endings in UTF-16 CSVs.
            $utf8 = str_replace("\n", "\r\n", $utf8);
            $utf16 = mb_convert_encoding($utf8, 'UTF-16LE', 'UTF-8');

            echo "\xFF\xFE" . $utf16;
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-16LE',
        ]);
    }

    /**
     * Write one CSV row (comma-delimited, RFC 4180 quoting).
     *
     * @param  resource  $out
     */
    private function putRow($out, array $row): void
    {
        $cells = array_map(static function ($value): string {
            $value = (string) $value;
            if (preg_match('/[,\r\n"]/', $value)) {
                return '"' . str_replace('"', '""', $value) . '"';
            }

            return $value;
        }, $row);

        fwrite($out, implode(',', $cells) . "\n");
    }

    /**
     * @param  resource  $out
     */
    private function writeLearningUnitSection($out, LearningUnit $unit): void
    {
        $this->putRow($out, ['# ' . __('exports.section_learning_unit')]);
        $this->putRow($out, [
            __('exports.col_code'),
            __('exports.col_lu_name'),
            __('exports.col_lu_status'),
            __('exports.col_lu_applicable_objects'),
            __('exports.col_lu_description'),
            __('exports.col_lu_dietary_title'),
            __('exports.col_lu_dietary_recommendations'),
            __('exports.col_lu_clinical_title'),
            __('exports.col_lu_clinical_notes'),
        ]);
        $this->putRow($out, [
            $unit->code ?? '',
            $unit->name ?? '',
            $unit->status ? __('exports.status_active') : __('exports.status_inactive'),
            is_array($unit->applicable_objects) ? implode(', ', $unit->applicable_objects) : '',
            $this->stripHtml($unit->description),
            $unit->dietary_recommendation_title ?? '',
            $this->stripHtml($unit->dietary_recommendations),
            $unit->clinical_note_title ?? '',
            $this->stripHtml($unit->clinical_notes),
        ]);
    }

    /**
     * @param  resource  $out
     */
    private function writeItemsSection($out, LearningUnit $unit): void
    {
        $this->putRow($out, ['# ' . __('exports.section_items')]);

        if ($unit->items->isEmpty()) {
            $this->putRow($out, [__('exports.no_items')]);

            return;
        }

        $grouped = $unit->items->groupBy(fn ($item) => optional($item->category)->id ?? 0)
            ->sortKeys();

        foreach ($grouped as $items) {
            $category = $items->first()->category;
            $categoryLabel = $category
                ? sprintf('[%s] %s', $category->code, $category->name)
                : __('exports.category_unknown');

            $this->putRow($out, ['## ' . __('exports.category_block', ['category' => $categoryLabel])]);
            $this->putRow($out, [
                __('exports.col_item_model'),
                __('exports.col_item_name'),
                __('exports.col_item_description'),
                __('exports.col_item_unit'),
                __('exports.col_item_is_default'),
                __('exports.col_item_status'),
            ]);
            foreach ($items as $item) {
                $this->putRow($out, [
                    $item->model ?? '',
                    $item->name ?? '',
                    $this->stripHtml($item->description),
                    $item->unit ?? 1,
                    $item->pivot->is_default ? __('common.yes') : __('common.no'),
                    $item->status ? __('exports.status_active') : __('exports.status_inactive'),
                ]);
            }
            $this->putRow($out, []);
        }
    }

    /**
     * @param  resource  $out
     */
    private function writeRecommendItemsSection($out, LearningUnit $unit): void
    {
        $this->putRow($out, ['# ' . __('exports.section_recommend_items')]);

        if ($unit->recommendItems->isEmpty()) {
            $this->putRow($out, [__('exports.no_recommend_items')]);

            return;
        }

        $grouped = $unit->recommendItems->groupBy(fn ($link) => optional(optional($link->recommendItem)->category)->id ?? 0)
            ->sortKeys();

        foreach ($grouped as $links) {
            $category = optional($links->first()->recommendItem)->category;
            $categoryLabel = $category
                ? sprintf('[%s] %s', $category->code, $category->name)
                : __('exports.category_unknown');

            $this->putRow($out, ['## ' . __('exports.category_block', ['category' => $categoryLabel])]);
            $this->putRow($out, [
                __('exports.col_recommend_name'),
                __('exports.col_recommend_description'),
                __('exports.col_recommend_column'),
                __('exports.col_recommend_weight'),
                __('exports.col_recommend_unit_text'),
            ]);
            foreach ($links as $link) {
                $recommend = $link->recommendItem;
                $this->putRow($out, [
                    optional($recommend)->name ?? '',
                    $this->stripHtml(optional($recommend)->description),
                    $this->columnLabel($link->column),
                    $link->weight ?? '',
                    $link->unit_text ?? '',
                ]);
            }
            $this->putRow($out, []);
        }
    }

    public function exportUsers(array $filters): StreamedResponse
    {
        $filename = $this->usersFilename($filters['role'] ?? null);
        $headers = [
            __('exports.col_no'),
            __('exports.col_user_identifier'),
            __('exports.col_emp_id'),
            __('exports.col_user_name'),
            __('exports.col_user_email'),
            __('exports.col_role'),
            __('exports.col_user_unit_label'),
            __('exports.col_user_job_title'),
            __('exports.col_account_status'),
            __('exports.col_last_login'),
            __('exports.col_created_at'),
        ];

        $query = User::query()
            ->when(isset($filters['status']) && $filters['status'] !== '', fn ($q) => $q->where('is_active', (int) $filters['status']))
            ->when(isset($filters['role']) && $filters['role'] !== '', fn ($q) => $q->where('role', (int) $filters['role']))
            ->orderBy('created_at');

        return $this->stream($filename, $headers, function () use ($query) {
            $no = 0;
            foreach ($query->cursor() as $u) {
                yield [
                    ++$no,
                    $u->hash_id ?? $u->id,
                    $u->emp_id ?? '',
                    $u->cname ?? $u->username ?? '',
                    $u->email ?? '',
                    $this->roleLabel($u->role),
                    $u->unit_label ?? '',
                    $u->job_title ?? '',
                    $u->is_active ? __('exports.status_active_user') : __('exports.status_suspended'),
                    optional($u->last_login_at)->format('Y-m-d H:i:s') ?? '',
                    optional($u->created_at)->format('Y-m-d H:i:s') ?? '',
                ];
            }
        });
    }

    private function stream(string $filename, array $headers, \Closure $rowsGenerator): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rowsGenerator) {
            $out = fopen('php://output', 'w');
            // UTF-8 BOM so Excel renders CJK correctly
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, $headers);
            foreach ($rowsGenerator() as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Localised export file name, chosen by the role filter. The role value
     * comes from the request ('0' student, '1' teacher, empty = all roles).
     */
    private function usersFilename(int|string|null $role): string
    {
        $key = match ((string) $role) {
            (string) User::ROLE_STUDENT => 'exports.filename_users_student',
            (string) User::ROLE_TEACHER => 'exports.filename_users_teacher',
            default => 'exports.filename_users_all',
        };

        return __($key, ['date' => now()->format('Ymd')]);
    }

    private function roleLabel(?int $role): string
    {
        return match ($role) {
            User::ROLE_TEACHER => __('exports.role_teacher'),
            User::ROLE_STUDENT => __('exports.role_student'),
            default => '',
        };
    }

    private function columnLabel(?int $column): string
    {
        return match ($column) {
            LearningUnitRecommendItem::COLUMN_STAPLE => __('cms.column_staple'),
            LearningUnitRecommendItem::COLUMN_MAIN => __('cms.column_main'),
            LearningUnitRecommendItem::COLUMN_FRUIT => __('cms.column_fruit'),
            default => (string) ($column ?? ''),
        };
    }

    private function stripHtml(?string $value): string
    {
        if (! $value) {
            return '';
        }

        return trim(preg_replace('/\s+/u', ' ', strip_tags($value)));
    }
}
