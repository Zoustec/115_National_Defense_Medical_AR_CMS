<?php

return [
    'title' => '匯出資料',

    // Export file names (:date is YYYYMMDD). Picked by the selected role filter.
    'filename_users_student' => '學生使用者列表_:date.csv',
    'filename_users_teacher' => '教師使用者列表_:date.csv',
    'filename_users_all' => '使用者列表_:date.csv',

    'learning_units_section' => '匯出學習單元',
    'learning_units_description' => '匯出系統中所有學習單元的完整資料（含描述、飲食建議、臨床小提醒等）。',
    'export_single_tooltip' => '匯出此學習單元（含項目與替換食材）',

    'section_learning_unit' => '學習單元資料',
    'section_items' => '項目清單',
    'section_recommend_items' => '替換食材清單',
    'category_block' => '分類：:category',
    'category_unknown' => '（未分類）',
    'no_items' => '（此單元尚未加入任何項目）',
    'no_recommend_items' => '（此單元尚未加入任何替換食材）',

    'col_item_model' => '模型代碼',
    'col_item_name' => '項目名稱',
    'col_item_description' => '項目描述',
    'col_item_unit' => '份數',
    'col_item_is_default' => '是否為預設',
    'col_item_status' => '項目狀態',

    'col_recommend_name' => '替換食材名稱',
    'col_recommend_description' => '替換食材描述',
    'col_recommend_column' => '替換群組',
    'col_recommend_weight' => '重量',
    'col_recommend_unit_text' => '單位文字',


    'users_section' => '匯出使用者帳戶',
    'users_description' => '匯出使用者帳戶完整資料（與 SSO 同步）。',

    'date_from' => '起始日期',
    'date_to' => '結束日期',
    'role' => '身份',
    'export_csv' => '匯出 CSV',

    'role_student' => '學生',
    'role_teacher' => '教師',

    // Common columns
    'col_no' => '編號',
    'col_code' => '代碼',
    'col_created_at' => '建立時間',
    'col_updated_at' => '更新時間',

    // Learning Unit columns
    'col_lu_name' => '學習單元名稱',
    'col_lu_description' => '描述',
    'col_lu_applicable_objects' => '適用對象',
    'col_lu_dietary_title' => '飲食建議標題',
    'col_lu_dietary_recommendations' => '飲食建議內容',
    'col_lu_clinical_title' => '臨床小提醒標題',
    'col_lu_clinical_notes' => '臨床小提醒內容',
    'col_lu_status' => '狀態',
    'col_lu_is_locked' => '是否鎖定',
    'col_lu_sort_order' => '顯示順序',

    // User columns
    'col_user_identifier' => '使用者識別碼',
    'col_emp_id' => '學號 / 員工編號',
    'col_user_name' => '姓名',
    'col_user_email' => '電子信箱',
    'col_role' => '身份',
    'col_user_unit_label' => '單位',
    'col_user_job_title' => '職稱',
    'col_account_status' => '帳戶狀態',
    'col_last_login' => '最後登入時間',

    'status_active' => '啟用',
    'status_inactive' => '停用',
    'status_active_user' => '使用中',
    'status_suspended' => '已停權',
];
