<?php

return [
    // Success/Error Messages
    'created_successfully' => 'Attribute created successfully.',
    'updated_successfully' => 'Attribute updated successfully.',
    'deleted_successfully' => 'Attribute deleted successfully.',
    'order_updated_successfully' => 'Attribute order updated successfully.',
    'failed_to_update_order' => 'Failed to update attribute order.',
    'swap_success' => 'Attributes swapped successfully.',
    'swap_failed' => 'Failed to swap attributes.',

    // Page titles
    'create_attribute' => 'Create Attribute',
    'edit_attribute' => 'Edit Attribute',
    'attributes_list' => 'Attributes List',
    'attribute_details' => 'Attribute Details',

    // Form labels
    'attribute_name' => 'Attribute Name',
    'created_at' => 'Created At',
    'updated_at' => 'Last Updated',
    'type' => 'Type',
    'attributes' => 'Attributes',
    'values' => 'Values',
    'option_type' => 'Feminine Hygiene Products',
    'captain' => 'Captain',
    'label_captain' => 'Captain\'s Mark given to this attribute',
    'label_user_show' => 'Show to Buyer',
    'user_show_description' => 'Set whether to display this attribute on the buyer\'s order screen. If turned off, it can only be viewed and edited on admin, supplier, and logistic screens.',
    'note' => 'Note',

    // Type options
    'user_input' => 'User Input',
    'multi_choice' => 'Multi Choice',
    'color_picker' => 'Color Picker',
    'select_type' => 'Select Type',

    // Placeholders
    'enter_attribute_name' => 'Enter attribute name',
    'enter_name' => 'Enter attribute name',
    'enter_values_hint' => 'Type and press Enter to add',
    'type_and_press_enter' => 'Type and press Enter',
    'enter_note' => 'Enter note',

    // Description texts
    'user_input_description' => 'This attribute will allow users to enter custom values when placing orders.',
    'values_hint' => 'Type a value and press Enter to add. Press Backspace to remove.',
    'select_colors' => 'Select Colors',
    'choose_color' => 'Choose your color',
    'add_color' => 'Add Color',
    'add_custom_color' => 'Add Custom Color',
    'add_custom_value' => 'Add Custom Value',
    'select_multiple_colors' => 'You can select multiple colors',
    'select_values' => 'Select Values',
    'select_multiple_values' => 'You can select multiple values',
    'color_picker_hint' => 'Click the color box to choose a color, then click Add Color button to add it to the list.',
    'color_already_added' => 'This color has already been added.',
    'select_attribute' => 'Select Attribute',
    'select_attribute_placeholder' => 'Choose an attribute...',
    'no_values_added' => 'No values added yet',
    'enter_choice_value' => 'Enter choice value',
    'enter_value_first' => 'Please enter a value first',
    'value_already_exists' => 'This value already exists',
    'color_already_exists' => 'This color already exists',
    'add_at_least_one_value' => 'Please add at least one value',
    'select_attribute_first' => 'Please select an attribute first',
    'value_added_successfully' => 'Value added successfully to attribute',
    'not_found' => 'Attribute not found',
    'value' => 'Value',
    'value_required' => 'Value is required',
    'value_must_be_string' => 'Value must be a text string',
    'value_max' => 'Value cannot exceed 255 characters',

    // Buttons - specific to attributes
    'add_attribute' => 'Add Attribute',

    // Table headers
    'name' => 'Name',

    // Messages
    'no_attributes_found' => 'No attributes found',
    'create_first_attribute' => 'Create your first attribute',
    'try_adjusting_search_criteria' => 'Try adjusting your search criteria',
    'adjust_search_criteria' => 'Try adjusting your search criteria',
    'user_input_type' => 'User input field',
    'created_successfully' => 'Attribute created successfully.',
    'updated_successfully' => 'Attribute updated successfully.',
    'deleted_successfully' => 'Attribute deleted successfully.',

    // Validation Messages
    'name_required' => 'Attribute name is required.',
    'name_max' => 'Attribute name cannot exceed 255 characters.',
    'english_name_required' => 'English name is required.',
    'english_name_string' => 'English name must be a valid string.',
    'english_name_max' => 'English name cannot exceed 255 characters.',
    'english_name_unique' => 'This English name already exists.',
    'type_required' => 'Type is required.',
    'type_invalid' => 'Selected type is invalid.',
    'values_required' => 'Values are required for Multi Choice type.',
    'colors_required' => 'At least one color is required for Color Picker type.',
    'colors_min' => 'Please add at least one color.',
    'color_value_required' => 'Color value is required.',
    'color_invalid_format' => 'Color must be in valid HEX format (e.g., #FF0000).',
    'name_unique' => 'This attribute name already exists.',

    // Option Types
    'size' => 'Size',

    // Swap Attributes
    'swap_attributes' => 'Swap Attributes',
    'swap_attributes_description' => 'Select two attributes you want to swap',
    'first_attribute' => 'First Attribute',
    'second_attribute' => 'Second Attribute',
    'select_first_attribute' => 'Select first attribute',
    'select_second_attribute' => 'Select second attribute',
    'swap' => 'Swap',
    'first_attribute_required' => 'First attribute is required.',
    'first_attribute_integer' => 'First attribute must be an integer.',
    'first_attribute_exists' => 'First attribute does not exist.',
    'second_attribute_required' => 'Second attribute is required.',
    'second_attribute_integer' => 'Second attribute must be an integer.',
    'second_attribute_exists' => 'Second attribute does not exist.',
    'attributes_must_be_different' => 'The two attributes must be different.',
];
