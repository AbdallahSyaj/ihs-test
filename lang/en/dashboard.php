<?php

return [

    // ==========================================================
    // Shared / common vocabulary — reused across two or more
    // modules. Always check here before adding a module key.
    // ==========================================================
    'shared' => [

        // Generic action buttons
        'create_new' => 'Create New',
        'save_updates' => 'Save Updates',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'view' => 'View',
        'view_details' => 'View Details',
        'view_archive' => 'View Archive',
        'archive' => 'Archive',
        'update_info' => 'Update Info',
        'main_info' => 'Main Info',
        'settings' => 'Setting',
        'select_deselect_all' => 'Select/Deselect All',
        'delete_selected' => 'Delete Selected',
        'active_selected' => 'Active Selected',
        'inactive_selected' => 'Inactive Selected',
        'restore_selected' => 'Restore Selected Rows',
        'restore_this_record' => 'Restore this Record',
        'set_active' => 'Set Active',
        'set_inactive' => 'Set Inactive',

        'oops_title' => 'Oops',
        'logout' => 'Logout',
        'undefined' => 'Undefined',
        'copyright_text' => 'Copyright. All rights reserved.',

        // Flash message titles (SweetAlert)
        'success_title' => 'Good job!',
        'error_title' => 'Warning!',

        // Flash messages — unified across all CRUD controllers
        'added_successfully' => 'Record has been added successfully.',
        'updated_successfully' => 'Record has been updated successfully.',
        'deleted_successfully' => 'Record has been deleted successfully.',
        'restored_successfully' => 'Record has been restored successfully.',
        'process_successful' => 'Process has been completed successfully.',
        'record_not_found' => 'Record not found.',
        'select_at_least_one' => 'Please select at least one row.',
        'confirm_delete_message' => 'Are you sure to delete? All associated data will be deleted.',

        // Field labels / table column headers
        'title_en' => 'Title EN',
        'title_ar' => 'Title AR',
        'description_en' => 'Description EN',
        'description_ar' => 'Description AR',
        'status' => 'Status',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'choose_status' => '--- Select Status ---',
        'date_time' => 'Date/Time',
        'control' => 'Control',
        'select' => 'Select',
        'id' => 'ID',
        'type' => 'Type',
        'image' => 'Image',
        'video' => 'Video',
        'name' => 'Name',
        'first_name' => 'First Name',
        'last_name' => 'Last Name',
        'email' => 'Email',
        'phone' => 'Phone',
        'password' => 'Password',
        'confirm_password' => 'Confirm Password',
        'addition_time' => 'Addition Time',
        'addition_date' => 'Addition Date',
        'added_since' => 'Added Since',
        'date_label' => 'Date',
        'time_label' => 'Time',
        'since_label' => 'Since',

        // ':platform URL' — reused for every social link field on the
        // Contact Us page instead of one key per platform
        'platform_url' => ':platform URL',

        // Sidebar / breadcrumb / page-title / dashboard-card display
        // names — single source of truth for every module's name
        'modules' => [
            'personal' => 'Personal',
            'home' => 'Home',
            'dashboard' => 'Dashboard',
            'pages' => 'Pages',
            'about_us' => 'About Us',
            'contact_us' => 'Contact Us',
            'privacy_policy' => 'Privacy Policy',
            'terms_and_conditions' => 'Terms & Conditions',
            'users' => 'Users',
            'website_layouts' => 'Website Layouts',
            'sliders' => 'Sliders',
            'categories' => 'Categories',
            'products' => 'Products',
            'requests' => 'Requests',
            'contact_us_requests' => 'Contact Us Requests',
            'website' => 'Website',
            'admins' => 'Admins',
            'support_tickets' => 'Support Tickets',
        ],
    ],

    // ==========================================================
    // Auth / login page
    // ==========================================================
    'auth' => [
        'welcome_title' => 'Welcome to the Admin Portal',
        'welcome_subtitle' => 'Access your dashboard to manage certifications, checks, and organizational data.',
        'feature_secure_access' => 'Secure admin access to all system features',
        'feature_analytics' => 'Real-time analytics and reporting',
        'feature_checks' => 'Complete check management system',
        'feature_user_admin' => 'User and organization administration',
        'copyright' => '© 2025 Admin Dashboard. All rights reserved.',
        'sign_in_title' => 'Sign in to your account',
        'sign_in_subtitle' => 'Enter your credentials to access your dashboard',
        'correct_errors' => 'Please correct the following errors',
        'email_placeholder' => 'Enter your email',
        'password_placeholder' => 'Enter your password',
        'remember_me' => 'Remember me',
        'forgot_password' => 'Forgot password?',
        'login_button' => 'Login',
        'need_help' => 'Need help?',
        'contact_support' => 'Contact Support',
        'invalid_credentials' => 'Email or password is incorrect.',
        'too_many_attempts' => 'Too many attempts. Please wait a moment and try again.',
        'two_factor_unavailable' => 'Two-factor authentication is unavailable for this account. Contact another administrator.',
        'two_factor_expired' => 'Your verification session has expired. Please sign in again.',
    ],

    // ==========================================================
    // Dashboard home page
    // ==========================================================
    'dashboard_home' => [
        'overview' => 'Dashboard Overview & Quick Access',
        'visit' => 'Visit',
    ],

    // ==========================================================
    // Sliders
    // ==========================================================
    'sliders' => [
        'select_type' => '--- Select Type ---',
    ],

    // ==========================================================
    // About Us
    // ==========================================================
    'about_us' => [
        'image_label' => 'About Us Image',
    ],

    // ==========================================================
    // Contact Us
    // ==========================================================
    'contact_us' => [
        'whatsapp' => 'Whatsapp',
        'address_ar' => 'Address AR',
        'address_en' => 'Address EN',
        'hours' => 'Business Hours',
        'page_title_en' => 'Page Title (English)',
        'page_intro_en' => 'Introductory Description (English)',
    ],

    // ==========================================================
    // Contact Us Requests
    // ==========================================================
    'contact_us_requests' => [
        'message_details' => 'Message Details',
        'message' => 'Message',
        'received' => 'Received',
        'confirm_delete_message' => 'Are you sure you want to delete this message?',
        'no_messages' => 'There are no messages for you currently.',
    ],

    // ==========================================================
    // Support Tickets
    // ==========================================================
    'support_tickets' => [
        'error_description' => 'Error Description',
        'function_name' => 'Function Name',
    ],

    // ==========================================================
    // Admins
    // ==========================================================
    'admins' => [
        'cannot_archive_last_active' => 'Cannot archive the last active admin account.',
        'cannot_deactivate_last_active' => 'Cannot deactivate the last active admin account.',
    ],

    // ==========================================================
    // DataTables UI strings (search box, pagination, entry count)
    // ==========================================================
    'datatable' => [
        'search' => 'Search:',
        'info' => 'Showing _START_ to _END_ of _TOTAL_ entries',
        'info_empty' => 'Showing 0 to 0 of 0 entries',
        'info_filtered' => '(filtered from _MAX_ total entries)',
        'zero_records' => 'No matching records found',
        'first' => 'First',
        'last' => 'Last',
        'next' => 'Next',
        'previous' => 'Previous',
    ],

    // ==========================================================
    // Admin two-factor authentication
    // ==========================================================
    'two_factor' => [
        'page_title' => 'Two-factor authentication',
        'challenge_title' => 'Verify your sign-in',
        'challenge_subtitle' => 'Enter the 6-digit code from your authenticator app.',
        'code_label' => 'Authentication code',
        'code_placeholder' => '6-digit code',
        'verify_button' => 'Verify',
        'recovery_label' => 'Recovery code',
        'recovery_placeholder' => 'Enter a recovery code',
        'recovery_button' => 'Use recovery code',
        'invalid_code' => 'The verification code is invalid.',
        'enroll_title' => 'Set up two-factor authentication',
        'enroll_subtitle' => 'Add this account to Google Authenticator, Microsoft Authenticator, or a compatible app, then enter the current code.',
        'enroll_account' => 'Account: :email',
        'manual_key_label' => 'Manual setup key',
        'enroll_code_label' => 'Confirm with a code',
        'enroll_button' => 'Enable two-factor authentication',
        'recovery_title' => 'Save your recovery codes',
        'recovery_once' => 'These codes are shown once. Store them somewhere safe. Each code can be used one time if you lose your authenticator.',
        'continue_button' => 'Continue to dashboard',
        'settings_title' => 'Two-factor authentication',
        'status_label' => 'Status',
        'status_enabled' => 'Enabled',
        'status_disabled' => 'Not enabled',
        'next_login_enrolls' => 'You will be asked to set up an authenticator the next time you sign in.',
        'regenerate_title' => 'Regenerate recovery codes',
        'regenerate_help' => 'Generating new codes immediately invalidates the previous set. Confirm your password to continue.',
        'regenerate_button' => 'Regenerate recovery codes',
        'disable_title' => 'Disable two-factor authentication',
        'disable_help' => 'Confirm your password to turn off two-factor authentication for this account.',
        'disable_button' => 'Disable two-factor authentication',
        'disable_confirm' => 'Disable two-factor authentication for your account?',
        'disabled' => 'Two-factor authentication has been disabled.',
        'password_incorrect' => 'The password is incorrect.',
        'recovery_codes_unavailable' => 'Recovery codes are no longer available. Generate a new set if you still need them.',
        'last_recovery_code_used' => 'You used your last recovery code. Generate a new set from the security settings.',
        'reset_title' => 'Reset two-factor authentication',
        'reset_help' => 'This removes the authenticator and recovery codes for :email. They must enroll again at the next sign-in.',
        'reset_email_label' => 'Type the administrator email to confirm',
        'reset_button' => 'Reset 2FA',
        'reset_confirm' => 'Reset two-factor authentication for this administrator?',
        'reset_confirmation_mismatch' => 'The confirmation email does not match this administrator.',
        'reset_successful' => 'Two-factor authentication was reset. The administrator must enroll again at the next sign-in.',
        'use_self_service' => 'Use your own security settings to disable two-factor authentication.',
        'menu_label' => 'Security',
    ],

    // ==========================================================
    // Categories admin module
    // ==========================================================
    'categories' => [
        'slug' => 'Slug',
        'slug_help' => 'Optional on create. Leave blank to generate from the name. Use lowercase letters, numbers, and hyphens only.',
        'restore_name_conflict' => 'Cannot restore this category because another active category already uses this name.',
        'restore_slug_conflict' => 'Cannot restore this category because another active category already uses this slug.',
        'delete_has_products' => 'Cannot archive this category while products are still assigned to it. Reassign those products to another category first.',
    ],

    'products' => [
        'slug' => 'Slug',
        'slug_help' => 'Optional on create. Leave blank to generate from the name. Use lowercase letters, numbers, and hyphens only.',
        'model' => 'Model',
        'category' => 'Category',
        'base_price' => 'Base price (USD)',
        'badge' => 'Badge',
        'short_description' => 'Short description',
        'long_description' => 'Long description',
        'compatibility' => 'Compatibility',
        'listing_image' => 'Listing image',
        'gallery_images' => 'Gallery images',
        'gallery_order' => 'Gallery order',
        'specifications' => 'Specifications',
        'spec_label' => 'Label',
        'spec_value' => 'Value',
        'accessories' => 'Accessories',
        'accessory_qty' => 'Qty',
        'options' => 'Options',
        'option_name' => 'Option name',
        'option_value' => 'Value label',
        'additional_price' => 'Additional price (USD)',
        'main_listing_image' => 'Main listing image',
        'remove_image' => 'Remove',
        'add_row' => 'Add row',
        'add_option' => 'Add option group',
        'add_option_value' => 'Add value',
        'search_placeholder' => 'Search name, slug, or model',
        'filter_category' => 'All categories',
        'filter_status' => 'All statuses',
        'restore_slug_conflict' => 'Cannot restore this product because another active product already uses this slug.',
    ],

];
