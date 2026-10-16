<?php

return [

    // ==========================================================
    // Generic rule messages — used whenever no per-field override
    // exists below. ":attribute" is replaced with the friendly
    // name from the 'attributes' array at the bottom of this file.
    // ==========================================================
    'required' => 'The :attribute field is required.',
    'email' => 'The :attribute field must be a valid email address.',
    'unique' => 'This :attribute is already in use.',
    'string' => 'The :attribute field must be text.',
    'max' => [
        'numeric' => 'The :attribute field must not be greater than :max.',
        'file' => 'The :attribute field must not be greater than :max kilobytes.',
        'string' => 'The :attribute field must not be greater than :max characters.',
        'array' => 'The :attribute field must not have more than :max items.',
    ],
    'min' => [
        'numeric' => 'The :attribute field must be at least :min.',
        'file' => 'The :attribute field must be at least :min kilobytes.',
        'string' => 'The :attribute field must be at least :min characters.',
        'array' => 'The :attribute field must have at least :min items.',
    ],
    'confirmed' => 'The :attribute confirmation does not match.',
    'same' => 'The :attribute and :other must match.',
    'numeric' => 'The :attribute field must be a number.',
    'integer' => 'The :attribute field must be a whole number.',
    'in' => 'The selected :attribute is invalid.',
    'url' => 'The :attribute field must be a valid URL.',
    'image' => 'The :attribute field must be an image.',
    'mimes' => 'The :attribute field must be a file of type: :values.',
    'nullable' => '',

    // Composite password rule (Illuminate\Validation\Rules\Password)
    'password' => [
        'letters' => 'The :attribute must contain at least one letter.',
        'mixed' => 'The :attribute must contain at least one uppercase and one lowercase letter.',
        'numbers' => 'The :attribute must contain at least one number.',
        'symbols' => 'The :attribute must contain at least one symbol.',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.',
    ],

    // ==========================================================
    // Custom, field+rule specific overrides — only where the
    // generic message above isn't clear enough on its own
    // (mainly the numeric-coded enum fields: status, type).
    // ==========================================================
    'custom' => [
        'status' => [
            'in' => 'Status must be either Active or Inactive.',
        ],
        'type' => [
            'in' => 'Type must be either Image or Video.',
        ],
    ],

    // ==========================================================
    // Human-readable field names substituted into ":attribute"
    // above. One entry per field name used across the project's
    // FormRequests — reused automatically by every form that
    // has a field with this name (no per-form duplication).
    // ==========================================================
    'attributes' => [
        'name' => 'name',
        'first_name' => 'first name',
        'last_name' => 'last name',
        'email' => 'email',
        'phone' => 'phone number',
        'password' => 'password',
        'password_confirmation' => 'password confirmation',
        'title_ar' => 'Title AR',
        'title_en' => 'Title EN',
        'description_ar' => 'Description AR',
        'description_en' => 'Description EN',
        'status' => 'status',
        'type' => 'type',
        'image' => 'image',
        'video' => 'video',
        'address_ar' => 'Address AR',
        'address_en' => 'Address EN',
        'whatsapp' => 'Whatsapp',
        'facebook' => 'Facebook URL',
        'twitter' => 'Twitter URL',
        'instagram' => 'Instagram URL',
        'linkedin' => 'Linkedin URL',
        'youtube' => 'Youtube URL',
        'snapchat' => 'Snapchat URL',
        'tiktok' => 'Tiktok URL',
        'telegram' => 'Telegram URL',
    ],

];
