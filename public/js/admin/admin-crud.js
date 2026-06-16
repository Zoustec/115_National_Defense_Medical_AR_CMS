/**
 * Complete Admin CRUD Module
 * Includes: drag-and-drop reordering, delete functionality, notifications, and utilities
 */
window.AdminCrudModule = (function() {
    'use strict';

    const AdminCommon = {
        /**
         * Initialize Toastr with default options
         */
        initToastr: function() {
            if (typeof toastr !== 'undefined') {
                toastr.options = {
                    "closeButton": true,
                    "progressBar": true,
                    "positionClass": "toast-top-right",
                    "timeOut": "3000",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                };
            }
        },

        /**
         * Auto-hide success alerts
         */
        autoHideAlerts: function(delay = 5000) {
            setTimeout(function() {
                $('.alert-success').fadeOut('slow');
            }, delay);
        },

        /**
         * Show notification
         */
        notify: function(type, message) {
            if (typeof toastr !== 'undefined') {
                toastr[type](message);
            } else {
                console.log(`${type.toUpperCase()}: ${message}`);
            }
        },

        /**
         * Generic delete confirmation using SweetAlert2
         */
        confirmDelete: function(title, text, confirmText, cancelText, onConfirm) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: title,
                    text: text,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: confirmText,
                    cancelButtonText: cancelText,
                    reverseButtons: true,
                    customClass: {
                        confirmButton: 'btn btn-danger mx-2',
                        cancelButton: 'btn btn-secondary mx-2'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    // Support both old and new SweetAlert2 API
                    const isConfirmed = result.isConfirmed === true || result.value === true;

                    if (isConfirmed && typeof onConfirm === 'function') {
                        onConfirm();
                    }
                });
            } else if (confirm(text) && typeof onConfirm === 'function') {
                onConfirm();
            }
        },

        /**
         * Initialize common admin features
         */
        init: function() {
            this.initToastr();
            this.autoHideAlerts();
        }
    };

    const SortableTable = {
        findTbody: function(tableId) {
            return document.getElementById(tableId)
                || document.querySelector(`table tbody#${tableId}`)
                || document.querySelector(`#${tableId}`);
        },

        createSortable: function(tbody, options) {
            if (typeof Sortable === 'undefined') {
                console.error('SortableJS library not found');
                return;
            }
            Sortable.create(tbody, {
                handle: `.${options.handleClass}`,
                animation: 150,
                onEnd: function() {
                    SortableTable.updateOrder(options, tbody);
                }
            });
        },

        init: function(options) {
            const tbody = this.findTbody(options.tableId);

            if (!tbody) {
                console.error(`Element with ID "${options.tableId}" not found`);
                return;
            }

            this.createSortable(tbody, options);
        },

        updateOrder: function(options, tbody) {
            const rows = tbody.querySelectorAll(options.rowSelector);
            const newOrder = [];

            rows.forEach(function(row, index) {
                const id = row.dataset.id;
                if (id) {
                    newOrder.push({
                        id: parseInt(id),
                        order: index + 1
                    });
                }
            });

            if (newOrder.length === 0) {
                console.warn('No sortable items found');
                return;
            }

            // Prepare data for request
            const requestData = {
                _token: options.csrfToken
            };

            // Add extra data first (e.g., designable_id for designs)
            if (options.extraData) {
                Object.assign(requestData, options.extraData);
            }

            // Then add the main data array
            requestData[options.dataKey] = newOrder;

            // Send AJAX request
            $.ajax({
                url: options.updateUrl,
                method: 'POST',
                data: requestData,
                success: function(response) {
                    if (options.onSuccess) {
                        options.onSuccess(response);
                    }
                },
                error: function(xhr) {
                    console.error('Update order failed:', xhr.status);
                    if (options.onError) {
                        options.onError(xhr);
                    }
                }
            });
        }
    };

    const CrudModule = {
        /**
         * Initialize sortable functionality
         */
        initializeSortable: function(options) {
            const defaults = {
                tableId: null,
                handleClass: 'drag-handle',
                updateUrl: null,
                csrfToken: null,
                rowSelector: 'tr[data-id]',
                dataKey: 'items',
                extraData: null, // additional data to send with request
                successMessage: 'Order updated successfully',
                errorMessage: 'Failed to update order'
            };

            const settings = { ...defaults, ...options };

            if (!settings.tableId || !settings.updateUrl) {
                console.error('CrudModule: tableId and updateUrl are required');
                return;
            }

            const sortableOptions = {
                tableId: settings.tableId,
                handleClass: settings.handleClass,
                updateUrl: settings.updateUrl,
                csrfToken: settings.csrfToken,
                rowSelector: settings.rowSelector,
                dataKey: settings.dataKey,
                extraData: settings.extraData,
                onSuccess: function(response) {
                    AdminCommon.notify('success', settings.successMessage);
                    if (settings.onSuccess) {
                        settings.onSuccess(response);
                    }
                },
                onError: function(xhr) {
                    AdminCommon.notify('error', settings.errorMessage);
                    if (settings.onError) {
                        settings.onError(xhr);
                    } else {
                        location.reload();
                    }
                }
            };

            SortableTable.init(sortableOptions);
        },

        /**
         * Initialize delete functionality
         */
        initializeDelete: function(options) {
            const defaults = {
                deleteUrl: null,
                csrfToken: null,
                confirmTitle: null,
                confirmText: null,
                confirmButton: null,
                cancelButton: null,
                successMessage: null,
                errorMessage: null
            };

            const settings = { ...defaults, ...options };

            if (!settings.deleteUrl) {
                console.error('CrudModule: deleteUrl is required for delete functionality');
                return;
            }

            const handleDeleteSuccess = function(response, settings) {
                const message = response.message || settings.successMessage;
                AdminCommon.notify('success', message);

                if (settings.onSuccess) {
                    settings.onSuccess(response);
                } else {
                    window.location.reload();
                }
            };

            const handleDeleteError = function(xhr, settings) {
                const message = settings.errorMessage;
                AdminCommon.notify('error', message);
                if (settings.onError) {
                    settings.onError(xhr);
                }
            };

            const performDelete = function(deleteUrl, settings) {
                $.ajax({
                    url: deleteUrl,
                    method: 'POST',
                    data: {
                        _token: settings.csrfToken,
                        _method: 'DELETE'
                    },
                    success: function(response) {
                        handleDeleteSuccess(response, settings);
                    },
                    error: function(xhr) {
                        handleDeleteError(xhr, settings);
                    }
                });
            };

            return function(itemId) {
                const deleteUrl = settings.deleteUrl.replace(':id', itemId);
                const title = settings.confirmTitle;
                const text = settings.confirmText;
                const confirmText = settings.confirmButton;
                const cancelText = settings.cancelButton;

                AdminCommon.confirmDelete(title, text, confirmText, cancelText, function() {
                    performDelete(deleteUrl, settings);
                });
            };
        },

        /**
         * Initialize complete CRUD module for a resource
         */
        initialize: function(config) {
            const module = {};

            // Initialize sortable if configured
            if (config.sortable) {
                this.initializeSortable(config.sortable);
                module.sortable = config.sortable;
            }

            // Initialize delete if configured
            if (config.delete) {
                module.confirmDelete = this.initializeDelete(config.delete);
            }

            return module;
        }
    };

    let modules = {};

    /**
     * Initialize CRUD functionality for a specific resource
     */
    function initializeResource(resourceType, config) {
        if (!config?.reorderUrl && !config?.deleteUrl) {
            console.warn(`${resourceType} configuration is missing required URLs (need at least reorderUrl or deleteUrl)`);
            return null;
        }

        // Map resource type to table ID
        const tableIdMap = {
            'team-types': 'sortable-team-types',
            'categories': 'sortable-categories',
            'teams': 'sortable-teams',
            'products': 'sortable-products',
            'shippingFee': 'sortable-shipping-fee'
        };

        const tableId = tableIdMap[resourceType] || `sortable-${resourceType}`;
        const dataKey = resourceType;

        const crudConfig = {
            name: resourceType
        };

        // Only add sortable if reorderUrl is provided
        if (config.reorderUrl) {
            crudConfig.sortable = {
                tableId: tableId,
                updateUrl: config.reorderUrl,
                csrfToken: config.csrfToken,
                dataKey: dataKey,
                successMessage: config.messages?.orderUpdatedSuccess || `${resourceType} order updated successfully`,
                errorMessage: config.messages?.orderUpdateFailed || `Failed to update ${resourceType} order`
            };
        }

        // Only add delete if deleteUrl is provided
        if (config.deleteUrl) {
            crudConfig.delete = {
                deleteUrl: config.deleteUrl,
                csrfToken: config.csrfToken,
                confirmTitle: config.messages?.deleteTitle,
                confirmText: config.messages?.deleteText,
                confirmButton: config.messages?.deleteConfirm,
                cancelButton: config.messages?.deleteCancel,
                successMessage: config.messages?.deleteSuccess,
                errorMessage: config.messages?.deleteError,
                onSuccess : config.onSuccess
            };
        }

        const crudModule = CrudModule.initialize(crudConfig);
        return crudModule;
    }
    function initializeResourceForDelete(resourceType, config) {
        if (!config?.deleteUrl) {
            console.warn(`${resourceType} configuration is missing required URLs`);
            return null;
        }

        return CrudModule.initialize({
            delete: {
                deleteUrl: config.deleteUrl,
                csrfToken: config.csrfToken,
                confirmTitle: config.messages?.deleteTitle,
                confirmText: config.messages?.deleteText,
                confirmButton: config.messages?.deleteConfirm,
                cancelButton: config.messages?.deleteCancel,
                successMessage: config.messages?.deleteSuccess,
                errorMessage: config.messages?.deleteError
            }
        });
    }

    /**
     * Create a window module proxy that delegates confirmDelete to the initialized module.
     */
    function createDeleteProxy(moduleKey, errorMsg) {
        return {
            confirmDelete: function(id) {
                if (modules[moduleKey]?.confirmDelete) {
                    modules[moduleKey].confirmDelete(id);
                } else {
                    console.error(errorMsg);
                }
            }
        };
    }

    /**
     * Initialize a resource module if its window config is defined.
     */
    function registerModule(configKey, moduleKey, windowKey, initFn, resourceName, errorMsg) {
        if (typeof window[configKey] === 'undefined') {
            return;
        }
        modules[moduleKey] = initFn(resourceName, window[configKey]);
        window[windowKey] = createDeleteProxy(moduleKey, errorMsg);
    }

    /**
     * Auto-detect and initialize based on available configurations
     */
    function autoInit() {
        // Prevent double initialization
        if (window.adminCrudInitialized) {
            return;
        }

        // Initialize AdminCommon first
        AdminCommon.init();

        registerModule('teamTypesConfig',    'teamTypes',    'TeamTypesModule',    initializeResource,          'team-types',       'Team Types delete functionality not initialized');
        registerModule('usersConfig',        'users',        'UsersModule',        initializeResourceForDelete, 'users',            'Users delete functionality not initialized');
        registerModule('companiesConfig',    'companies',    'CompaniesModule',    initializeResourceForDelete, 'companies',        'Companies delete functionality not initialized');
        registerModule('categoriesConfig',   'categories',   'CategoriesModule',   initializeResource,          'categories',       'Categories delete functionality not initialized');
        registerModule('teamsConfig',        'teams',        'TeamsModule',        initializeResource,          'teams',            'Teams delete functionality not initialized');
        registerModule('productsConfig',     'products',     'ProductsModule',     initializeResource,          'products',         'Products delete functionality not initialized');
        registerModule('shippingFeeConfig',  'shippingFee',  'shippingFeeModule',  initializeResource,          'shippingFee',      'ShippingFee delete functionality not initialized');
        registerModule('newsConfig',         'news',         'NewsModule',         initializeResource,          'news',             '新聞刪除功能未初始化');
        registerModule('achievementsConfig', 'achievements', 'AchievementModule',  initializeResource,          'achievements',     '成就刪除功能未初始化');
        registerModule('faqTypesConfig',     'faqTypes',     'FaqTypeModule',      initializeResourceForDelete, 'faqTypes',         'FaqType delete functionality not initialized');
        registerModule('companySizesConfig', 'companySizes', 'CompanySizeModule',  initializeResourceForDelete, 'companySizes',     'CompanySize delete functionality not initialized');
        registerModule('paymentMethodsConfig','paymentMethods','PaymentMethodModule',initializeResourceForDelete,'paymentMethods',  'PaymentMethod delete functionality not initialized');
        registerModule('provincesConfig',    'provinces',    'ProvinceModule',     initializeResourceForDelete, 'provinces',        'Province delete functionality not initialized');
        registerModule('regionsConfig',      'regions',      'RegionModule',       initializeResourceForDelete, 'regions',          'Region delete functionality not initialized');
        registerModule('workplacesConfig',   'workplaces',   'WorkplaceModule',    initializeResourceForDelete, 'workplaces',       'Workplace delete functionality not initialized');
        registerModule('jobCategoriesConfig','jobCategories','JobCategoryModule',  initializeResourceForDelete, 'jobCategories',    'JobCategory delete functionality not initialized');
        registerModule('jobTypesConfig',     'jobTypes',     'JobTypeModule',      initializeResourceForDelete, 'jobTypes',         'JobType delete functionality not initialized');
        registerModule('degreeLevelsConfig', 'degreeLevels', 'DegreeLevelsModule', initializeResource,          'degree_levels',    'DegreeLevels delete functionality not initialized');
        registerModule('benefitsConfig',     'benefits',     'BenefitModule',      initializeResource,          'benefits',         'Benefit delete functionality not initialized');
        registerModule('benefitTypesConfig', 'benefitTypes', 'BenefitTypeModule',  initializeResourceForDelete, 'benefit_types',    'BenefitTypes delete functionality not initialized');
        registerModule('workingTimesConfig', 'workingTimes', 'WorkingTimeModule',  initializeResource,          'working_times',    'WorkingTime delete functionality not initialized');
        registerModule('universitiesConfig',  'universities',  'UniversityModule',    initializeResource,          'universities',     'University delete functionality not initialized');
        registerModule('majorsConfig',        'majors',        'MajorModule',         initializeResource,          'majors',           'Major delete functionality not initialized');
        registerModule('recruitmentStepsConfig', 'recruitmentSteps', 'RecruitmentStepModule', initializeResource, 'recruitment_steps', 'RecruitmentStep delete functionality not initialized');
        registerModule('skillCategoriesConfig', 'skillCategories', 'SkillCategoryModule', initializeResourceForDelete, 'skillCategories', 'SkillCategory delete functionality not initialized');
        registerModule('skillJobTitlesConfig', 'skillJobTitles', 'SkillJobTitleModule', initializeResourceForDelete, 'skillJobTitles', 'SkillJobTitle delete functionality not initialized');
        registerModule('skillsConfig', 'skills', 'SkillModule', initializeResourceForDelete, 'skills', 'Skill delete functionality not initialized');
        registerModule('desiredJobIndustriesConfig', 'desiredJobIndustries', 'DesiredJobIndustryModule', initializeResourceForDelete, 'desiredJobIndustries', 'DesiredJobIndustry delete functionality not initialized');
        registerModule('desiredJobCategoriesConfig', 'desiredJobCategories', 'DesiredJobCategoryModule', initializeResourceForDelete, 'desiredJobCategories', 'DesiredJobCategory delete functionality not initialized');
        registerModule('desiredJobsConfig', 'desiredJobs', 'DesiredJobModule', initializeResourceForDelete, 'desiredJobs', 'DesiredJob delete functionality not initialized');
        registerModule('educationQualificationsConfig', 'educationQualifications', 'EducationQualificationModule', initializeResourceForDelete, 'educationQualifications', 'EducationQualification delete functionality not initialized');
        registerModule('industryCategoriesConfig', 'industryCategories', 'IndustryCategoryModule', initializeResourceForDelete, 'industryCategories', 'IndustryCategory delete functionality not initialized');
        registerModule('faqsConfig',             'faqs',             'FaqModule',              initializeResourceForDelete, 'faqs',             'Faq delete functionality not initialized');

        window.adminCrudInitialized = true;
    }

    return {
        AdminCommon: AdminCommon,
        SortableTable: SortableTable,
        CrudModule: CrudModule,
        initialize: CrudModule.initialize.bind(CrudModule),
        autoInit: autoInit
    };
})();

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        AdminCrudModule.autoInit();
    }, 100);
});

// For backward compatibility with jQuery - prevent double initialization
$(document).ready(function() {
    if (!window.adminCrudInitialized) {
        setTimeout(function() {
            AdminCrudModule.autoInit();
        }, 200);
    }
});
