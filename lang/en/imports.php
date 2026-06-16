<?php

return [
    'learning_unit_section' => 'Import Learning Unit',
    'learning_unit_description' => 'Upload a CSV file following the template format to import a single learning unit (with its items and recommend items).',
    'import_button' => 'Import CSV',
    'download_template' => 'Download Template',
    'choose_file' => 'Choose file',
    'no_file' => 'No file chosen',
    'file_label' => 'CSV File',
    'submit' => 'Start Import',

    // Section markers (must match the template — these are parsed)
    'section_unit' => 'Learning Unit',
    'section_items' => 'Items',
    'section_recommends' => 'Recommend Items',

    // Unit columns
    'col_code' => 'Code',
    'col_name' => 'Name',
    'col_sort_order' => 'Sort Order',
    'col_applicable_objects' => 'Applicable Objects (comma-separated)',
    'col_description' => 'Description',
    'col_dietary_title' => 'Dietary Recommendation Title',
    'col_dietary_recommendations' => 'Dietary Recommendations',
    'col_clinical_title' => 'Clinical Note Title',
    'col_clinical_notes' => 'Clinical Notes',

    // Items columns
    'col_item_model' => 'Item Code (model)',
    'col_item_name' => 'Item Name (used when auto-creating)',
    'col_item_category_code' => 'Item Category Code (used when auto-creating)',
    'col_item_unit' => 'Unit',

    // Recommends columns
    'col_recommend_name' => 'Recommend Item Name',
    'col_recommend_category_code' => 'Recommend Category Code (must be a category used by this file\'s items)',
    'col_recommend_weight' => 'Weight',
    'col_recommend_unit_text' => 'Unit Text',

    // Results
    'success_created' => 'Created learning unit ":code" (:items items, :recommends recommend items).',
    'success_updated' => 'Updated learning unit ":code" (:items items, :recommends recommend items).',
    'completed_with_errors' => 'Import finished with :count issue(s) to review.',

    // Errors
    'error_empty_file' => 'The file is empty or not in the expected format.',
    'error_no_unit_section' => 'Could not find the "# :section" block. Please use the provided template.',
    'error_missing_code' => 'The code field is required.',
    'error_missing_name' => 'The name field is required.',
    'error_item_missing_model' => 'Item on row :line has no model code; skipped.',
    'error_item_missing_category' => 'Item ":model" does not exist and no category code was provided to auto-create it; skipped.',
    'error_recommend_missing_name' => 'Recommend item on row :line has no name; skipped.',
    'error_recommend_missing_category' => 'Recommend item ":name" has no valid category code; skipped.',
    'error_recommend_category_not_in_items' => 'Recommend item on row :line uses category ":category" which is not among the categories used by this file\'s items; skipped.',
    'error_invalid_category' => 'Category code ":code" does not exist; skipped.',
    'error_unexpected' => 'An error occurred during import: :message',

    // Field validation errors (kept in sync with the create/edit screen)
    'error_code_too_long' => 'The learning unit code may not be greater than :max characters.',
    'error_name_too_long' => 'The learning unit name may not be greater than :max characters.',
    'error_code_duplicate' => 'A learning unit with code ":code" already exists. Import was cancelled and the existing unit was not changed.',
    'error_name_duplicate' => 'The learning unit name ":name" is already in use.',
    'error_field_too_long' => 'The ":field" field may not be greater than :max characters.',
    'error_recommend_weight_invalid' => 'Recommend item on row :line has an invalid weight (must be between 0 and 99999.99); skipped.',
    'error_recommend_unit_text_too_long' => 'Recommend item on row :line has a unit text longer than :max characters; skipped.',
];
