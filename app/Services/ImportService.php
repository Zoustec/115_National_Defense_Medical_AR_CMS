<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\ImportServiceInterface;
use App\Models\Category;
use App\Models\Item;
use App\Models\LearningUnit;
use App\Models\LearningUnitItem;
use App\Models\LearningUnitRecommendItem;
use App\Models\RecommendItem;
use App\Support\LearningUnitTemplate;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImportService implements ImportServiceInterface
{
    /** Section markers written/parsed in the CSV (prefixed with "# "). */
    private const MARK_UNIT = 'unit';

    private const MARK_ITEMS = 'items';

    private const MARK_RECOMMENDS = 'recommends';

    /**
     * Field length limits, kept in sync with App\Http\Requests\Admin\LearningUnitRequest.
     */
    private const MAX_CODE = 50;

    private const MAX_NAME = 255;

    private const MAX_DESCRIPTION = 5000;

    private const MAX_APPLICABLE_OBJECTS = 1000;

    private const MAX_TITLE = 255;

    private const MAX_LONG_TEXT = 20000;

    private const MAX_UNIT_TEXT = 100;

    private const MAX_WEIGHT = 99999.99;

    public function learningUnitTemplate(): StreamedResponse
    {
        $filename = '學習單元模板.csv';

        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel

            $this->putRow($out, ['# ' . __('imports.section_unit')]);
            $this->putRow($out, [
                __('imports.col_code'),
                __('imports.col_name'),
                __('imports.col_sort_order'),
                __('imports.col_applicable_objects'),
                __('imports.col_description'),
                __('imports.col_dietary_title'),
                __('imports.col_dietary_recommendations'),
                __('imports.col_clinical_title'),
                __('imports.col_clinical_notes'),
            ]);
            $this->putRow($out, ['LU001', 'Sample Unit', '0', 'tagA, tagB', '', '', '', '', '']);
            $this->putRow($out, []);

            $this->putRow($out, ['# ' . __('imports.section_items')]);
            $this->putRow($out, [
                __('imports.col_item_model'),
                __('imports.col_item_name'),
                __('imports.col_item_category_code'),
                __('imports.col_item_unit'),
            ]);
            // The default item / recommend set is always applied automatically.
            // Rows below are ADDED on top — duplicates of the default are ignored,
            // new entries are created. Category codes must be real (e.g.
            // StapleFood / MainCourse / Fruit / SideDish1 / SideDish2).
            $this->putRow($out, ['rice', '乾飯', 'StapleFood', '4']);
            $this->putRow($out, ['my_grain', '糙米飯', 'StapleFood', '4']);
            $this->putRow($out, ['my_dish', '清蒸鱈魚', 'MainCourse', '2']);
            $this->putRow($out, []);

            $this->putRow($out, ['# ' . __('imports.section_recommends')]);
            $this->putRow($out, [
                __('imports.col_recommend_name'),
                __('imports.col_recommend_category_code'),
                __('imports.col_recommend_weight'),
                __('imports.col_recommend_unit_text'),
            ]);
            $this->putRow($out, ['糙米飯', 'StapleFood', '80', '公克']);
            $this->putRow($out, ['鮭魚', 'MainCourse', '120', '公克']);
            $this->putRow($out, ['奇異果', 'Fruit', '100', '公克']);

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function importLearningUnit(UploadedFile $file): array
    {
        $rows = $this->readRows($file);
        $sections = $this->splitSections($rows);

        if (! isset($sections[self::MARK_UNIT]) || count($sections[self::MARK_UNIT]) < 3) {
            return $this->failure('validation', __('imports.error_no_unit_section', ['section' => __('imports.section_unit')]));
        }

        // Section layout: [0] marker row, [1] header row, [2] first data row.
        $unitData = $this->parseUnitRow($sections[self::MARK_UNIT][2] ?? []);

        // Required fields: both the code and the name must be present. Report
        // every missing field at once so the user does not have to fix and
        // re-upload one error at a time.
        $missing = [];
        if ($unitData['code'] === '') {
            $missing[] = __('imports.error_missing_code');
        }
        if ($unitData['name'] === '') {
            $missing[] = __('imports.error_missing_name');
        }
        if ($missing !== []) {
            return $this->failure('validation', implode(' ', $missing));
        }

        // Length / uniqueness checks for the unit itself.
        if (mb_strlen($unitData['code']) > self::MAX_CODE) {
            return $this->failure('validation', __('imports.error_code_too_long', ['max' => self::MAX_CODE]));
        }
        if (mb_strlen($unitData['name']) > self::MAX_NAME) {
            return $this->failure('validation', __('imports.error_name_too_long', ['max' => self::MAX_NAME]));
        }
        if ($this->nameTaken($unitData['name'], $unitData['code'])) {
            return $this->failure('validation', __('imports.error_name_duplicate', ['name' => $unitData['name']]));
        }
        foreach ($this->unitFieldLimits($unitData['attributes']) as [$value, $max, $label]) {
            if ($value !== null && mb_strlen($value) > $max) {
                return $this->failure('validation', __('imports.error_field_too_long', ['field' => $label, 'max' => $max]));
            }
        }

        // A duplicate code is rejected outright — the existing unit is never
        // overwritten. Import only ever creates a brand-new unit.
        if (LearningUnit::where('code', $unitData['code'])->exists()) {
            return $this->failure('duplicate_code', __('imports.error_code_duplicate', ['code' => $unitData['code']]));
        }

        return DB::transaction(function () use ($unitData, $sections) {
            $unit = LearningUnit::create($unitData['attributes'] + ['code' => $unitData['code']]);

            // Every imported unit starts from the same default composition as the
            // seeder / create form; the CSV rows are applied on top as additions.
            $this->seedDefaultComposition($unit->id);

            $this->importItems($unit->id, $sections[self::MARK_ITEMS] ?? []);
            $this->importRecommends($unit->id, $sections[self::MARK_RECOMMENDS] ?? []);

            return [
                'status' => 'success',
                'unit_code' => $unit->code,
                // Total links on the unit (default composition + any added rows).
                'items' => LearningUnitItem::where('learning_unit_id', $unit->id)->count(),
                'recommends' => LearningUnitRecommendItem::where('learning_unit_id', $unit->id)->count(),
                'message' => null,
            ];
        });
    }

    /**
     * Shape a non-success import result. $status is one of 'validation' or
     * 'duplicate_code'; the controller maps both to a single red toast.
     *
     * @return array{status: string, unit_code: null, items: int, recommends: int, message: string}
     */
    private function failure(string $status, string $message): array
    {
        return [
            'status' => $status,
            'unit_code' => null,
            'items' => 0,
            'recommends' => 0,
            'message' => $message,
        ];
    }

    /**
     * Attach the default item / recommend-item composition to a freshly created
     * unit — the same set the seeder and create form use. Imported CSV rows are
     * then layered on top (see importItems / importRecommends), which skip any
     * row that duplicates one of these defaults.
     */
    private function seedDefaultComposition(int $unitId): void
    {
        foreach (LearningUnitTemplate::ITEM_IDS as $itemId) {
            LearningUnitItem::create([
                'learning_unit_id' => $unitId,
                'item_id' => $itemId,
                'is_default' => in_array($itemId, LearningUnitTemplate::DEFAULT_ITEM_IDS, true),
            ]);
        }

        foreach (LearningUnitTemplate::recommendLinks() as $link) {
            LearningUnitRecommendItem::create($link + ['learning_unit_id' => $unitId]);
        }
    }

    /**
     * Layer the CSV item rows on top of the unit's default composition.
     * The default set is already attached; a row is added only when it is valid
     * AND its item is not already linked (deduped by item id). Invalid or
     * duplicate rows are silently skipped — the unit keeps its valid default.
     *
     * @param  array<int, array<int, string>>  $section
     * @return int  number of extra item links added beyond the default
     */
    private function importItems(int $unitId, array $section): int
    {
        // Item ids already linked to this unit (the seeded default set).
        $linked = LearningUnitItem::where('learning_unit_id', $unitId)
            ->pluck('item_id')
            ->all();
        $linked = array_flip($linked);

        $count = 0;
        // Row 0 is the section marker, row 1 is the header; data starts at row 2.
        foreach (array_slice($section, 2) as $row) {
            if ($this->isBlankRow($row)) {
                continue;
            }

            $model = trim((string) ($row[0] ?? ''));
            $name = trim((string) ($row[1] ?? ''));
            $categoryCode = trim((string) ($row[2] ?? ''));
            $unit = trim((string) ($row[3] ?? ''));

            if ($model === '') {
                continue;
            }

            $item = Item::where('model', $model)->first();

            if ($item === null) {
                $category = $this->resolveCategory($categoryCode);
                if ($category === null) {
                    continue;
                }

                $item = Item::create([
                    'model' => $model,
                    'category_id' => $category->id,
                    'name' => $name !== '' ? $name : $model,
                    'unit' => $unit !== '' ? max(1, (int) $unit) : 1,
                    'display_order' => 0,
                    'status' => true,
                ]);
            }

            // Duplicate of a default (or an earlier row) — skip, don't double-link.
            if (isset($linked[$item->id])) {
                continue;
            }

            LearningUnitItem::create([
                'learning_unit_id' => $unitId,
                'item_id' => $item->id,
                // Imported item assignments always default to the default plate (on).
                'is_default' => true,
            ]);
            $linked[$item->id] = true;
            $count++;
        }

        return $count;
    }

    /**
     * Layer the CSV recommend rows on top of the unit's default composition.
     * A row is added only when it is valid AND not a duplicate of an existing
     * link (deduped by recommend item, i.e. name + category). Invalid or
     * duplicate rows are silently skipped.
     *
     * @param  array<int, array<int, string>>  $section
     * @return int  number of extra recommend links added beyond the default
     */
    private function importRecommends(int $unitId, array $section): int
    {
        // Recommend item ids already linked to this unit (the seeded default set).
        $linked = LearningUnitRecommendItem::where('learning_unit_id', $unitId)
            ->pluck('recommend_item_id')
            ->all();
        $linked = array_flip($linked);

        $count = 0;
        foreach (array_slice($section, 2) as $row) {
            if ($this->isBlankRow($row)) {
                continue;
            }

            $name = trim((string) ($row[0] ?? ''));
            $categoryCode = trim((string) ($row[1] ?? ''));
            $weightRaw = trim((string) ($row[2] ?? ''));
            $unitText = trim((string) ($row[3] ?? ''));

            if ($name === '') {
                continue;
            }

            if ($weightRaw !== '' && (! is_numeric($weightRaw) || (float) $weightRaw < 0 || (float) $weightRaw > self::MAX_WEIGHT)) {
                continue;
            }

            if (mb_strlen($unitText) > self::MAX_UNIT_TEXT) {
                continue;
            }

            $category = $this->resolveCategory($categoryCode);
            if ($category === null) {
                continue;
            }

            // Dedup by recommend item (name + category): reuse if it exists,
            // otherwise it's a brand-new recommend item created here.
            $recommend = RecommendItem::where('name', $name)
                ->where('category_id', $category->id)
                ->first();

            if ($recommend === null) {
                $recommend = RecommendItem::create([
                    'category_id' => $category->id,
                    'name' => $name,
                    'status' => true,
                ]);
            }

            // Already linked (matches a default or an earlier row) — skip.
            if (isset($linked[$recommend->id])) {
                continue;
            }

            LearningUnitRecommendItem::create([
                'learning_unit_id' => $unitId,
                'recommend_item_id' => $recommend->id,
                // Replacement group follows the recommend item's own category.
                'column' => $category->id,
                'weight' => $weightRaw !== '' ? (float) $weightRaw : null,
                'unit_text' => $unitText !== '' ? $unitText : null,
            ]);
            $linked[$recommend->id] = true;
            $count++;
        }

        return $count;
    }

    /**
     * Whether the given name is already used by another active learning unit
     * (one that does not share the supplied code).
     */
    private function nameTaken(string $name, string $code): bool
    {
        return LearningUnit::where('name', $name)
            ->where('code', '!=', $code)
            ->whereNull('deleted_at')
            ->exists();
    }

    /**
     * Build the [value, max, label] tuples for the unit text fields that have a
     * length limit on the create/edit screen.
     *
     * @param  array<string, mixed>  $attributes
     * @return array<int, array{0: ?string, 1: int, 2: string}>
     */
    private function unitFieldLimits(array $attributes): array
    {
        $applicable = $attributes['applicable_objects'] ?? [];
        $applicableText = is_array($applicable) ? implode(', ', $applicable) : (string) $applicable;

        return [
            [$applicableText !== '' ? $applicableText : null, self::MAX_APPLICABLE_OBJECTS, __('imports.col_applicable_objects')],
            [$attributes['description'] ?? null, self::MAX_DESCRIPTION, __('imports.col_description')],
            [$attributes['dietary_recommendation_title'] ?? null, self::MAX_TITLE, __('imports.col_dietary_title')],
            [$attributes['dietary_recommendations'] ?? null, self::MAX_LONG_TEXT, __('imports.col_dietary_recommendations')],
            [$attributes['clinical_note_title'] ?? null, self::MAX_TITLE, __('imports.col_clinical_title')],
            [$attributes['clinical_notes'] ?? null, self::MAX_LONG_TEXT, __('imports.col_clinical_notes')],
        ];
    }

    private function resolveCategory(string $code): ?Category
    {
        if ($code === '') {
            return null;
        }

        return Category::where('code', $code)->first();
    }

    /**
     * @param  array<int, string>  $row
     * @return array{code: string, name: string, attributes: array<string, mixed>}
     */
    private function parseUnitRow(array $row): array
    {
        $code = trim((string) ($row[0] ?? ''));
        $name = trim((string) ($row[1] ?? ''));

        return [
            'code' => $code,
            'name' => $name,
            'attributes' => [
                'name' => $name,
                // status & is_locked are never imported — newly imported units
                // always default to off; toggle them afterward in the CMS.
                'status' => false,
                'is_locked' => false,
                'sort_order' => trim((string) ($row[2] ?? '')) !== '' ? (int) $row[2] : 0,
                'applicable_objects' => $this->parseTags($row[3] ?? null),
                'description' => $this->nullable($row[4] ?? null),
                'dietary_recommendation_title' => $this->nullable($row[5] ?? null),
                'dietary_recommendations' => $this->nullable($row[6] ?? null),
                'clinical_note_title' => $this->nullable($row[7] ?? null),
                'clinical_notes' => $this->nullable($row[8] ?? null),
            ],
        ];
    }

    /**
     * Split the flat row list into named sections keyed by their "# marker".
     *
     * @param  array<int, array<int, string>>  $rows
     * @return array<string, array<int, array<int, string>>>
     */
    private function splitSections(array $rows): array
    {
        $sections = [];
        $current = null;

        foreach ($rows as $row) {
            $first = trim((string) ($row[0] ?? ''));

            if (str_starts_with($first, '#')) {
                $marker = strtolower(trim(ltrim($first, '#')));
                $current = $this->canonicalMarker($marker);
                if ($current !== null) {
                    $sections[$current] = [$row];
                }
                continue;
            }

            if ($current !== null) {
                $sections[$current][] = $row;
            }
        }

        return $sections;
    }

    /**
     * Map a raw marker label (localized or English) to a canonical section key.
     */
    private function canonicalMarker(string $marker): ?string
    {
        $map = [
            self::MARK_UNIT => self::MARK_UNIT,
            self::MARK_ITEMS => self::MARK_ITEMS,
            self::MARK_RECOMMENDS => self::MARK_RECOMMENDS,
            strtolower(__('imports.section_unit')) => self::MARK_UNIT,
            strtolower(__('imports.section_items')) => self::MARK_ITEMS,
            strtolower(__('imports.section_recommends')) => self::MARK_RECOMMENDS,
        ];

        return $map[$marker] ?? null;
    }

    /**
     * Read a CSV file (UTF-8 or UTF-16 with BOM) into a list of rows.
     *
     * @return array<int, array<int, string>>
     */
    private function readRows(UploadedFile $file): array
    {
        $content = (string) file_get_contents($file->getRealPath());
        $content = $this->normalizeEncoding($content);

        $rows = [];
        $handle = fopen('php://temp', 'r+');
        fwrite($handle, $content);
        rewind($handle);

        while (($row = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
            $rows[] = array_map(static fn ($v) => (string) $v, $row);
        }
        fclose($handle);

        return $rows;
    }

    private function normalizeEncoding(string $content): string
    {
        // Strip / convert common BOMs so fgetcsv sees clean UTF-8.
        if (str_starts_with($content, "\xFF\xFE") || str_starts_with($content, "\xFE\xFF")) {
            $content = mb_convert_encoding($content, 'UTF-8', 'UTF-16');
        } elseif (str_starts_with($content, "\xEF\xBB\xBF")) {
            $content = substr($content, 3);
        } elseif (! mb_check_encoding($content, 'UTF-8')) {
            // No BOM and not valid UTF-8: Excel on Windows (Taiwan) saves CSV as
            // Big5 (CP950). Convert it so Traditional Chinese names survive the
            // parse instead of turning into "???". Try Big5 first, then fall back
            // to detection across the common legacy encodings.
            $detected = mb_detect_encoding($content, ['UTF-8', 'BIG-5', 'GB18030', 'CP950'], true) ?: 'BIG-5';
            $content = mb_convert_encoding($content, 'UTF-8', $detected);
        }

        return str_replace("\r\n", "\n", $content);
    }

    /**
     * @param  array<int, string>  $row
     */
    private function isBlankRow(array $row): bool
    {
        foreach ($row as $cell) {
            if (trim((string) $cell) !== '') {
                return false;
            }
        }

        return true;
    }

    private function nullable(mixed $value): ?string
    {
        $v = trim((string) $value);

        return $v === '' ? null : $v;
    }

    /**
     * @return array<int, string>
     */
    private function parseTags(mixed $raw): array
    {
        $raw = trim((string) $raw);
        if ($raw === '') {
            return [];
        }

        return collect(explode(',', $raw))
            ->map(static fn ($t) => trim((string) $t))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @param  resource  $out
     * @param  array<int, mixed>  $row
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
}
