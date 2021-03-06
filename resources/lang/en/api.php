<?php

return [
    'common' => [
        'search' => 'Search',
        'status' => 'Status',
        'send' => 'Send',
        'create_success' => 'Create successfully !',
        'update_success' => 'Update successfully !',
        'close' => 'close',
        'save' => 'save',
        'filter_btn' => 'Filter',
        'filter_reset' => 'Reset',
        'list_title' => 'List',
        'add_btn' => 'Add',
        'edit_btn' => 'Edit',
        'delete_btn' => 'Delete',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'save_btn' => 'Save',
        'cancel_btn' => 'Cancel',
        'all' => 'All',
        'create' => 'Create',
        'update' => 'Update',
        'total' => 'Total',
        'action' => 'Action',
        'export_excel' => 'Export excel',
        'show_hide' => 'Show/Hide',
        'result' => 'Result',
        'page' => 'Page',
        'id' => 'Id',
        'show' => 'Show',
        'hide' => 'Hide',
        'next' => 'Next',
        'previous' => 'Previous',
        'success' => 'Success',
        'failed' => 'Failed',
        'deleted' => 'Deleted',
        'ok' => 'OK',
        'cancel' => 'Cancel',
        'clear' => 'Clear',
        'process_error' => 'Process Error',
        'published' => 'Published',
        'unpublished' => 'Unpublished',
        'all_category' => 'All Categories',
        'not_found' => 'Not Found!',
        'id_not_exists' => 'Data not exists',
        'start_date_format' => 'The start date must be date format',
        'end_date_format' => 'The end date must be date format',
        'per_page_number' => 'The per page must be number',
        'id_required' => 'The id required',
        'id_array' => 'The id must be array',
        'you_not_permission' => 'You are not permission'
    ],
    'login' => [
        'login_fail' => 'Email or password is incorect!',
        'login_success' => 'Login success!',
        'login_password' => 'Please enter password',
        'login_email' => 'Please enter email',
        'email_not_verified' => 'Email still not verified',
        'account_not_exists' => 'Email not registered account',
        'user_invalid' => 'The user is blacklisted or self-locked',
        'logout_success' => 'Successfully logged out',
        'otp_invalid' => 'OTP invalid',
        'otp_expire' => 'OTP expire',
        'resend_otp_success' => 'Resend OTP success',
    ],
    'reset_password' => [
        'not_found' => 'This email has not been registered!',
        'sent_success' => 'We have e-mailed your password reset link!',
        'sent_failed' => 'Error when sending mail reset password',
        'token_invalid' => 'This password reset token is invalid',
        'token_expire' => 'This password reset token is expired',
        'reset_password_success' => 'Reset password successfully',
        'invalid_user' => 'Invalid user reset password',
        'invalid_email' => 'Email is not the same as registered one',
        'email_not_confirm' => 'Email has not been confirmed. Please check your mail for confirmation.',
    ],
    'verify_email' => [
        'sent_success' => 'We have e-mailed your password reset link!',
        'token_invalid' => 'This token is invalid',
        'token_expire' => 'This token is expired',
    ],
    'user' => [
        'email_required' => 'Please enter email',
        'email_email' => 'Incorrect email format',
        'password_required' => 'Please enter password',
        'password_min' => 'Password must not be less than 8 characters',
        'password_confirmation_required' => 'Please confirm password',
        'password_confirmation_same' => 'Incorrect password confirm',
        'password_incorect' => 'Enter the password incorrectly',
        'email' => 'Email',
        'phone' => 'Phone',
        'username' => 'Username',
        'password' => 'Password',
        'confirm_password' => 'Confirm password',
        'phone_min' => 'Phone number must not be less than 8 characters',
        'phone_max' => 'Phone number must not exceed 15 characters',
        'phone_regex' => 'Incorrect phone number format',
        'email_required' => 'Please enter email',
        'email_unique' => 'This email registered',
        'user_name_required' => 'Nick name is required',
        'gender_required' => 'Gender is required',
        'address_id_required' => 'Region is required',
        'birth_date_required' => 'Birthday is required',
        'birth_date_invalid' => 'Incorrect birhday format',
        'age_invalid' => 'You must be at least 18 years old to register for an account',
        'first_name_required' => 'First name is required',
        'last_name_required' => 'Last name is required',
        'account_updated' => 'account updated',
        'latitude_required' => 'The latitude field is required when store address is present.',
        'longitude_required' => 'The longitude field is required when store address is present.',
        'phone_required' => 'Please input phone number',
        'latitude_number' => 'The latitude must be number',
        'longitude_number' => 'The longitude must be number',
        'avatar_mimes' => 'The avatar must be a file of type: jpeg, png, jpg, gif, svg.',
        'avatar_max' => 'The avatar may not be greater than 5120 kilobytes.',
        'input_refferal_code_exists' => 'The selected input refferal code is invalid.',
    ],
    'validation' => [
        'services_id_required' => 'Service id required',
        'services_not_exists' => 'Service id not exists',
        'user_owner_invalid' => 'User of owner is invalid',
        'user_owner_required' => 'User of owner required',
        'room_data_has_been_deleted' => 'Rooms data has been deleted',
        'progress_not_yet_finish' => 'The progress not yet finish.',
        'services_is_selling' => 'The services is selling.',
        'user_is_pending_withdraw' => 'User is pending withdraw.',
        'user_id_required' => 'The user id required',
        'user_id_not_exits' => 'the User id not exists',
        'user_id_array' => 'The user id must be an array',
        'type_required' => 'The type required',
        'type_invalid' => 'The type invalid',
        'room_key_required' => 'The room key required',
        'room_key_exists' => 'The room key not exists',
        'step_required' => 'The step required',
        'step_numeric' => 'The step must a number',
        'step_in' => 'The step invalid',
        'price_required' => 'The price required',
        'name_owner_required' => 'The name owner required',
        'name_customer_required' => 'The name customer required',
        'name_services_required' => 'The name service required',
        'consulting_id_required' => 'The consulting id required',
        'consulting_id_exists' => 'The consulting id not exists',
        'text_required' => 'The text required',
        'room_key_required' => 'The room key required',
        'room_key_required' => 'The room key required',
        'file_name_mimetypes' => 'The file name must be format .pdf',
    ],
    'payment' => [
        'amount_required' => 'Amount is required',
        'amount_invalid' => 'Amount is invalid',
        'point_not_enough' => 'Point not enough',
        'valid_point' => 'Valid use point',
        'error_create_intent' => 'Error when create payment (minimum ??50)',
        'failed' => 'Payment failed',
        'success' => 'Payment successfully',
        'used_coupon' => 'Coupon already used',
        'create_intent_failed' => 'Error when create payment intent',
        'error_card' => 'Error when create payment method when input card number',
    ],
    'service' => [
        'price_gte' => 'Price is free (??0) OR must be greater than or equal 50',
        'error_create' => 'Error when create service',
        'create_success' => 'Create service successfully.',
        'error_like' => 'Error when like service',
        'not_found' => 'Service Id not found',
        'error_comment' => 'Error when comment service',
        'category_id_required' => 'Category is required',
        'region_id_required' => 'Region is required',
        'image_required' => 'The images field is required.',
        'image_max' => 'The images may not be greater than 5120 kilobytes.',
        'image_invalid' => 'The images must be a file of type: jpeg, png, jpg, gif, svg.',
        'service_title_required' => 'The title is required',
        'service_title_max' => 'The title maximum 32 characters.',
        'service_detail_required' => 'The service detail is required',
        'service_detail_max' => 'The service detail maximum 4000 characters.',
        'price_required' => 'The service price is required',
        'time_required' => 'The time require is required',
        'time_required_numeric' => 'The time require must be number',
        'service_id_array' => 'The service id must be an array',
        'price_max' => 'The service price is too expensive',
        'service_id_array_not_duplicate' => 'The array of services id exists same id',
        'service_not_exists' => 'The services not exists',
        'blocked_or_not_exists' => 'The service has been blocked or do not exist',
        'service_is_active' => 'The service is active',
        'service_removed' => 'The service has been removed',
        'service_removed_by_user_owner' => 'The service removed by user owner',
    ],
    'review' => [
        'review_success' => 'Review successfully',
        'review_failed' => 'Error occur when reivew',
        'cancel_success' => 'Cancel review successfully',
        'cancel_failed' => 'Error occur when cancel',
    ],
    'store' => [
        'id_required' => 'Store id required',
        'not_found' => 'Store Id not found',
        'error_like' => 'Error when like store',
        'store_blocked' => 'Store blocked',
        'unauthorized_like' => 'Unauthorized like self-store',
        'store_not_exists' => 'Store id is not exist.',
        'latitude_required' => 'The latitude required',
        'longitude_required' => 'The longitude required',
        'upload_file_s3_error' => 'Upload file to S3 has error',
        'get_image_map_google_cloud_has_error' => 'Get image map google cloud has error',
    ],
    'identity_card' => [
        'image_required' => 'The images field is required.',
        'image_max' => 'The images may not be greater than 5120 kilobytes.',
        'image_invalid' => 'The images must be a file of type: jpeg, png, jpg, gif, svg.',
        'identity_card_not_exits' => 'The identity card not exists data',
        'identity_card_not_process' => 'Status identity card changed',
    ],
    'request-consulting' => [
        'canceled_request' => 'The request consulting already canceled or not existing.',
        'consulting_id_required' => 'The consulting id is required',
        'consulting_not_exists' => 'The consulting is not exist.',
        'service_exists_in_progress' => 'The service is exists in progress consulting',
        'reason_required' => 'The reason is required',
        'cannot_cancel_request' => 'The progress has been paid',
    ],
    'contact' => [
        'contents_required' => 'Contents is required',
        'answer_required' => 'The answer is required',
        'email_not_exists' => 'Email not exists',
    ],
    'verify_phone' => [
        'code_required' => 'Code is required',
        'sent_success' => 'Sent verify code success. Please check your phone to get verification code.',
        'code_invalid' => 'Verification code is invalid.',
        'code_expired' => 'Verification code is expired.',
        'verify_success' => 'Verify phone successfully.',
        'verify_failed' => 'Error on server.',
        'exist_phone_verified' => 'Phone number already used by the user on the system. Please use another phone number',
        'reset_phone_success' => 'Reset phone successfully',
        'is_not_phone_verify' => 'Your phone number is not verify',
    ],
    'bonus' => [
        'referrent_success' => 'Referrent bonuses updated',
        'indecate_success' => 'Bonus indecated success',
        'check_all_boolean' => 'Check all must be boolean',
    ],
    'leave_group' => [
        'leave_success' => 'leave group success',
        'reason_required' => 'Please input reason leave.',
    ],
    'bank_account' => [
        'create_success' => 'Bank account created',
        'update_success' => 'Bank account updated',
        'category_id_required' => 'Category is required',
        'category_not_exists' => 'Category is not exists',
        'account_number_required' => 'Account number is required',
        'account_number_max' => 'Account number maximum 50 characters',
        'account_owner_required' => 'Account Owner is required',
        'account_owner_max' => 'Account number maximum 255 characters',
        'bank_name_required' => 'Bank name is required',
        'bank_name_max' => 'Bank name maximum 255 characters',
        'branch_name_required' => 'Branch name is required',
        'branch_name_max' => 'Branch name maximum 255 characters',
        'account_not_exists' => 'Bank account is not exists',
    ],
    'coupon' => [
        'invalid_code' => 'Coupon code invalid',
        'valid_code' => 'Valid coupon code',
        'coupon_required' => 'Coupon code is required',
        'coupon_discount_required' => 'Coupon discount is required',
        'coupon_discount_numeric' => 'The coupon discount must be a number.',
        'coupon_discount_between' => 'The coupon discount between 0 and 1.',
        'start_date_required' => 'Start date is required',
        'start_date_date' => 'Start date is invalid date format',
        'expire_date_required' => 'Expire date is required',
        'expire_date_date' => 'Expire date is invalid date format',
        'expire_date_after' => 'Expire date must be after start date',
    ],
    'store_image' => [
        'create_success' => 'Store images already created',
        'update_success' => 'Store images already updated',
        'delete_success' => 'Store images already deleted',
        'id_required' => 'The store image id is required',
        'id_exists' => 'The store image not found',
        'caption_required' => 'The caption is required',
        'caption_max' => 'The caption maximum 255 characters',
    ],
    'store_article' => [
        'create_success' => 'Store article already created',
        'update_success' => 'Store article already updated',
        'delete_success' => 'Store article already deleted',
        'title_required' => 'Title is required',
        'title_max' => 'Title maximum 255 characters',
        'contents_required' => 'Content is required',
        'file_name_required' => 'Please choose image',
        'file_name_image' => 'Please choose valid image file',
        'file_name_mimes' => 'The file must be a file of type: jpeg,png,jpg,gif,svg.',
        'file_name_max' => 'The file may not be greater than 5120 kilobytes.',
    ],
    'store_intro' => [
        'create_success' => 'Store introduce already created',
        'update_success' => 'Store introduce already updated',
        'delete_success' => 'Store introduce already deleted',
        'title_required' => 'Title is required',
        'title_max' => 'Title maximum 255 characters',
        'contents_required' => 'Content is required',
        'file_name_required' => 'Please choose image',
        'file_name_image' => 'Please choose valid image file',
        'file_name_mimes' => 'The file must be a file of type: jpeg,png,jpg,gif,svg.',
        'file_name_max' => 'The file may not be greater than 5120 kilobytes.',
    ],
    'comment' => [
        'message_required' => 'Please enter message',
        'message_max' => 'Message maximum 1000 character',
    ],
    'withdraw' => [
        'amount_required' => 'The amount field is required.',
        'amount_numeric' => 'The amount must be a number.',
        'amount_min' => 'The amount must be at least 50.',
        'status_required' => 'The status field is required.',
        'status_in' => 'The selected status is invalid.',
        'reason_rejected_required_if' => 'The reason rejected field is required when status is REJECTED.',
        'update_status_success' => 'The status of request withdraw has updated',
        'create_room_chat_error' => 'The error occurred creating room chat on firebase',
        'data_not_same' => 'Data in Firebase not same data in database',
    ],
    'search' => [
        'text_required' => 'The text search is required',
    ],
    'service_review' => [
        'value_required' => 'The value is required',
        'value_numeric' => 'The value must be number',
        'message_required' => 'Please input message',
    ],
    'advertising' => [
        'link_path_required' => 'The link path required',
        'block_id_required' => 'The block id required',
        'block_id_not_exists' => 'The block id not exists',
        'media_id_required' => 'The advertising media id required',
        'media_id_array' => 'The advertising media id must be an array',
        'media_id_not_exists' => 'The advertising media id not exists',
    ],
    'region' => [
        'id_not_exists' => 'The region id not exists',
    ],
    'refferal_bonus' => [
        'data_not_exists' => 'Refferal bonus data not exists',
    ],
    'category' => [
        'parent_id_required' => 'The parent id required',
        'parent_id_number' => 'The parent id must a type integer',
        'parent_id_not_exists' => 'The parent id not exists',
        'name_required' => 'The category name required',
        'name_max' => 'The category name maximum 255 characters',
        'prarent_id_not_same_id' => 'The parent id not allow same id',
        'has_child_data' => 'The category has child data.',
        'has_services' => 'The category id exists in service',
    ],

    'exception' => 'EXCEPTION FAILED',
    'services_not_of_user' => 'Services not of user',
    'services_not_exists' => 'Services not exists',
    'code_invalid' => 'Code invalid',
    'user_not_yet_have_point' => 'User not yet have point',
    'user_not_enough_point_payment' => 'User not enough point payment',
    'request_consulting_not_exists' => 'Request consulting not exists',
    'data_not_exists' => 'Data not exists',
    'bank_account_exists' => 'Bank account exists.',
    'invalid_image' => 'Image id is invalid',
];
