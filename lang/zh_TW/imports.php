<?php

return [
    'learning_unit_section' => '匯入學習單元',
    'learning_unit_description' => '上傳依照範本格式填寫的 CSV 檔案，可一次匯入一個學習單元（含項目與替換食材）。',
    'import_button' => '匯入 CSV',
    'download_template' => '下載範本',
    'choose_file' => '選擇檔案',
    'no_file' => '未選擇檔案',
    'file_label' => 'CSV 檔案',
    'submit' => '開始匯入',

    // Section markers (must match the template — these are parsed)
    'section_unit' => '基本資料',
    'section_items' => '項目',
    'section_recommends' => '替換食材',

    // Unit columns
    'col_code' => '代碼',
    'col_name' => '名稱',
    'col_sort_order' => '顯示順序',
    'col_applicable_objects' => '適用對象 (逗號分隔)',
    'col_description' => '描述',
    'col_dietary_title' => '飲食建議標題',
    'col_dietary_recommendations' => '飲食建議內容',
    'col_clinical_title' => '臨床小提醒標題',
    'col_clinical_notes' => '臨床小提醒內容',

    // Items columns
    'col_item_model' => '項目代碼 (model)',
    'col_item_name' => '項目名稱 (不存在時自動建立用)',
    'col_item_category_code' => '項目分類代碼 (不存在時自動建立用)',
    'col_item_unit' => '份數',

    // Recommends columns
    'col_recommend_name' => '替換食材名稱',
    'col_recommend_category_code' => '替換食材分類代碼 (須為本檔項目所用的分類)',
    'col_recommend_weight' => '重量',
    'col_recommend_unit_text' => '單位文字',

    // Results
    'success_created' => '已新增學習單元「:code」（項目:items筆，替換食材:recommends筆）。',
    'success_updated' => '已更新學習單元「:code」（項目:items筆，替換食材:recommends筆）。',
    'completed_with_errors' => '匯入完成，但有:count筆問題需要確認。',

    // Errors
    'error_empty_file' => '檔案內容為空，或格式不正確。',
    'error_no_unit_section' => '找不到「# :section」區段，請使用提供的範本。',
    'error_missing_code' => '代碼欄位為必填。',
    'error_missing_name' => '名稱欄位為必填。',
    'error_item_missing_model' => '第:line列項目缺少代碼 (model)，已略過。',
    'error_item_missing_category' => '項目「:model」不存在且未提供分類代碼，無法自動建立，已略過。',
    'error_recommend_missing_name' => '第:line列替換食材缺少名稱，已略過。',
    'error_recommend_missing_category' => '替換食材「:name」未提供有效分類代碼，無法匯入，已略過。',
    'error_recommend_category_not_in_items' => '第:line列替換食材的分類「:category」不在本檔項目所使用的分類範圍內，已略過。',
    'error_invalid_category' => '分類代碼「:code」不存在，已略過。',
    'error_unexpected' => '匯入時發生錯誤：:message',

    // Field validation errors (kept in sync with the create/edit screen)
    'error_code_too_long' => '學習單元代碼長度不可超過:max個字元。',
    'error_name_too_long' => '學習單元名稱長度不可超過:max個字元。',
    'error_code_duplicate' => '代碼「:code」的學習單元已存在，已取消匯入，原有資料未變更。',
    'error_name_duplicate' => '學習單元名稱「:name」已被使用。',
    'error_field_too_long' => '「:field」欄位長度不可超過:max個字元。',
    'error_recommend_weight_invalid' => '第:line列替換食材的重量不正確（須介於 0 至 99999.99），已略過。',
    'error_recommend_unit_text_too_long' => '第:line列替換食材的單位文字長度不可超過:max個字元，已略過。',
];
