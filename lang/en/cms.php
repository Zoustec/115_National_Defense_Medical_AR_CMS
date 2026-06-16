<?php

return [
    // Module titles
    'categories'      => 'Food Categories',
    'category'        => 'Category',
    'items'           => 'Food Items',
    'item'            => 'Item',
    'recommend_items' => 'Replacement Items',
    'recommend_item'  => 'Replacement Item',
    'learning_units'  => 'Learning Units',
    'learning_unit'   => 'Learning Unit',

    // Generic actions
    'create_title' => 'Create :resource',
    'edit_title'   => 'Edit :resource',
    'list_title'   => ':resource List',

    // Field labels (shared)
    'code'           => 'Code',
    'name'           => 'Name',
    'description'    => 'Description',
    'status'         => 'Status',
    'image'          => 'Image',
    'image_short'    => 'Image',
    'display_order'  => 'Display Order',
    'model'          => 'Model / Option Key',
    'category_id'    => 'Category',
    'unit'           => 'Unit (portion count)',
    'unit_text'      => 'Unit Text',
    'weight'         => 'Weight',
    'column'         => 'Column Group',
    'applicable_objects' => 'Audience Tags (comma-separated)',
    'dietary_recommendation_title' => 'Dietary Recommendation Title',
    'dietary_recommendations'      => 'Dietary Recommendations',
    'clinical_note_title' => 'Clinical Note Title',
    'clinical_notes'      => 'Clinical Notes',
    'is_locked'      => 'Locked',
    'is_active'      => 'Active',
    'username'       => 'Username',
    'email'          => 'Email',

    // Column enum
    'column_staple' => '1 — Staple Food',
    'column_main'   => '2 — Main Course',
    'column_fruit'  => '3 — Fruit',

    // Tabs & helpers
    'tab_basic'         => 'Basic',
    'tab_items'         => 'Items',
    'tab_replacements'  => 'Replacements',
    'items_help'        => 'Tick "Include" to add the item to this unit. "Default" marks items shown on the default plate.',
    'replacements_help' => 'Tick "Enable" to attach the replacement to this unit. Column determines which swap group it belongs to.',
    'include'           => 'Include',
    'default'           => 'Default',
    'enable'            => 'Enable',
    'show_selected_only' => 'Show selected only',
    'filter_all_categories' => 'All categories',

    // Search & filter helpers
    'search_placeholder' => 'Search by name...',
    'no_results'         => 'No matching items.',
    'clear_filters'      => 'Clear',

    // Notifications
    'created_successfully' => ':resource created successfully.',
    'updated_successfully' => ':resource updated successfully.',
    'deleted_successfully' => ':resource deleted successfully.',

    // Quick toggle confirmations
    'confirm_enable_title'  => 'Enable this item?',
    'confirm_enable_text'   => 'Are you sure you want to enable this item?',
    'confirm_disable_title' => 'Disable this item?',
    'confirm_disable_text'  => 'Are you sure you want to disable this item? It will be hidden from students / teachers.',

    // Validation
    'duplicate' => 'This :attribute already exists.',
    'duplicate_cancelled' => 'The data has not been saved; this change has been cancelled.',
    'name_required' => 'The name field is required.',
    'code_required' => 'The code field is required.',
];
