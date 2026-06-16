<?php

return [
    'title' => 'Export Data',

    // Export file names (:date is YYYYMMDD). Picked by the selected role filter.
    'filename_users_student' => 'Student_User_List_:date.csv',
    'filename_users_teacher' => 'Teacher_User_List_:date.csv',
    'filename_users_all' => 'User_List_:date.csv',

    'learning_units_section' => 'Export Learning Units',
    'learning_units_description' => 'Export complete data of all learning units in the system (descriptions, dietary recommendations, clinical notes, etc.).',
    'export_single_tooltip' => 'Export this learning unit (with items and recommend items)',

    'section_learning_unit' => 'Learning Unit',
    'section_items' => 'Items',
    'section_recommend_items' => 'Recommend Items',
    'category_block' => 'Category: :category',
    'category_unknown' => '(Uncategorized)',
    'no_items' => '(No items in this unit)',
    'no_recommend_items' => '(No recommend items in this unit)',

    'col_item_model' => 'Model Code',
    'col_item_name' => 'Item Name',
    'col_item_description' => 'Item Description',
    'col_item_unit' => 'Unit',
    'col_item_is_default' => 'Is Default',
    'col_item_status' => 'Item Status',

    'col_recommend_name' => 'Recommend Item Name',
    'col_recommend_description' => 'Recommend Item Description',
    'col_recommend_column' => 'Replacement Column',
    'col_recommend_weight' => 'Weight',
    'col_recommend_unit_text' => 'Unit Text',


    'users_section' => 'Export User Accounts',
    'users_description' => 'Export complete user account data (synchronized with SSO).',

    'date_from' => 'Date From',
    'date_to' => 'Date To',
    'role' => 'Role',
    'export_csv' => 'Export CSV',

    'role_student' => 'Student',
    'role_teacher' => 'Teacher',

    // Common columns
    'col_no' => 'No.',
    'col_code' => 'Code',
    'col_created_at' => 'Created At',
    'col_updated_at' => 'Updated At',

    // Learning Unit columns
    'col_lu_name' => 'Learning Unit Name',
    'col_lu_description' => 'Description',
    'col_lu_applicable_objects' => 'Applicable Objects',
    'col_lu_dietary_title' => 'Dietary Recommendation Title',
    'col_lu_dietary_recommendations' => 'Dietary Recommendations',
    'col_lu_clinical_title' => 'Clinical Note Title',
    'col_lu_clinical_notes' => 'Clinical Notes',
    'col_lu_status' => 'Status',
    'col_lu_is_locked' => 'Locked',
    'col_lu_sort_order' => 'Sort Order',

    // User columns
    'col_user_identifier' => 'User Identifier',
    'col_emp_id' => 'Student / Employee ID',
    'col_user_name' => 'Name',
    'col_user_email' => 'Email',
    'col_role' => 'Role',
    'col_user_unit_label' => 'Unit',
    'col_user_job_title' => 'Job Title',
    'col_account_status' => 'Account Status',
    'col_last_login' => 'Last Login',

    'status_active' => 'Active',
    'status_inactive' => 'Inactive',
    'status_active_user' => 'Active',
    'status_suspended' => 'Suspended',
];
