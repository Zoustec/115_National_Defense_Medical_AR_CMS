@include('admin._row_actions', [
    'editUrl'   => route('admin.categories.edit', $category),
    'deleteUrl' => route('admin.categories.destroy', $category),
])
