<?php

return [
    'lang' => 'en',

    'core' => [
        'nav' => [
            'home' => 'Home',
            'pictures' => 'Pictures',
            'partners' => 'Partners',
            'documents' => 'Documents',
            'contact' => 'Contact',
            'administration' => 'Administration',
            'search' => [
                'keys' => 'Keys',
                'submit' => 'Search',
            ],
            'userspace' => 'Userspace',
            'profile' => 'Profile',
            'logout' => 'Logout',
            'lang' => [
                'fr' => 'Français',
                'en' => 'English',
            ],
        ],
        'footer' => [
            'contact' => 'Contact',
            'terms' => 'Terms and conditions',
        ],
    ],

    'error' => [
        404 => [
            'title' => 'Page not found',
            'message' => 'The page you requested does not exists.',
        ],
        500 => [
            'title' => 'Internal error',
            'message' => 'An internal problem occurred. Please contact an administrator in order to get more details.',
        ],
    ],

    'home' => [
        'articles' => [
            'title' => 'Articles',
            'posted_by' => 'Posted by',
            'more' => 'More',
        ],
        'albums' => [
            'title' => 'Albums',
        ],
        'comments' => [
            'title' => 'Comments',
            'by' => 'Commented by',
            'on' => 'on',
        ],
        'partners' => [
            'title' => 'Partners',
        ],
    ],

    'article' => [
        'default' => 'Articles',
        'no_content' => 'There is no article yet.',
        'created_at' => 'Posted by',
        'views' => 'Viewers',
        'author' => 'Author',
        'comments' => 'Comments',
        'more' => 'More',
    ],

    'comment' => [
        'write' => [
            'title' => 'Post a comment',
            'message' => 'Message',
            'submit' => 'Send',
            'login' => [
                'title' => 'User authentication required',
                'message' => 'You cannot post a comment while you are not logged in.',
                'login_now' => 'Log in now',
            ],
        ],
        'read' => [
            'title' => 'Comments',
        ],
    ],

    'documents' => [
        'title' => 'Download files',
        'row' => [
            'type' => 'Type',
            'filename' => 'Name',
            'author' => 'Author',
            'date' => 'Created at',
            'download' => 'Download',
        ],
    ],

    'pictures' => [
        'category' => [
            'title' => 'Categories',
            'default' => 'Gallery',
            'no_content' => 'There is no album in this page yet.',
        ],
        'album' => [
            'title' => 'Albums',
            'title_alone' => 'Album',
            'created_at' => 'Posted by',
            'views' => 'Viewers',
            'author' => 'Author',
        ],
    ],

    'login' => [
        'title' => 'Log in',
        'username' => 'Email / Username',
        'password' => 'Password',
        'submit' => 'Log in',
        'register' => 'Register now',
        'forgot_password' => 'Forgot password?',
        'errors' => [
            'no_account' => 'Your account does not exists.',
            'password_does_not_match' => 'Your password does not match the account founded.',
            'missing_fields' => 'Some fields is missing.',
        ],
    ],

    'pages' => [
        'sub_pages' => 'Sub pages',
        'no_content' => 'No content for the moment.',
    ],

    'partners' => [
        'title' => 'Partners',
        'no_content' => 'There is no partner yet.',
    ],

    'profile' => [
        'title' => 'Update my personal information',
        'confirm' => [
            'title' => 'Confirmation',
            'message' => 'Your profile has been updated.',
        ],
        'errors' => [
            'title' => 'Error(s)',
            'password_does_not_match' => 'Your passwords does not match.',
            'password_too_short' => 'Your password is too short.',
            'email_invalid' => 'Your email is not valid.',
            'email_already' => 'The email already exists.',
            'avatar_invalid' => 'Your profile picture is not valid.',
            'newsletter_invalid' => 'Newsletter invalid.',
        ],
        'username' => 'Username',
        'fullname' => 'Firstname / lastname',
        'gender' => 'Gender',
        'men' => 'Men',
        'women' => 'Women',
        'birthday' => 'Birthday',
        'email' => 'Email',
        'new_password' => 'New Password',
        'repeat_password' => 'Repeat your new password',
        'avatar' => 'Profile picture',
        'newsletter' => 'Accept the newsletter',
        'submit' => 'Confirm',
        'details' => [
            'title' => 'User profile',
            'birthday' => 'Birthday',
            'username' => 'Username',
            'gender' => 'Gender',
            'men' => 'Men',
            'women' => 'Women',
        ],
    ],

    'register' => [
        'title' => 'Register',
        'errors' => [
            'title' => 'Error(s) :',
            'password_does_not_match' => 'Your passwords does not match.',
            'password_too_short' => 'Your password is too short',
            'email_invalid_or_already_exists' => 'Your email is not valid or already exists.',
            'captcha_invalid' => 'Captcha invalid.',
            'username_already_exists' => 'Your username already exists.',
            'gender_missing' => 'Gender is missing.',
            'username_missing' => 'Username is missing.',
            'first_name_missing' => 'Firstname is missing.',
            'last_name_missing' => 'Lastname is missing.',
            'email_missing' => 'Email is missing.',
            'birth_day_missing' => 'Your birth day is missing.',
            'birth_month_missing' => 'Your birth month is missing.',
            'birth_year_missing' => 'Your birth year is missing.',
            'password_missing' => 'Your password is missing.',
            'password_repeat_missing' => 'Your repeated password is missing.',
            'username_length' => 'Username too short or too long.',
            'first_name_length' => 'First name too short or too long.',
            'last_name_length' => 'Last name too short or too long.',
            'birth_day_range' => 'Your birth day is not valid.',
            'birth_month_range' => 'Your birth month is not valid.',
            'birth_year_range' => 'Your birth year is not valid.',
            'internal_error' => 'An internal error occurred, please contact an administrator.',
        ],
        'username' => 'Username',
        'first_name' => 'First name',
        'last_name' => 'Last name',
        'gender' => 'Gender',
        'men' => 'Men',
        'women' => 'Women',
        'birthday' => 'Birthday',
        'day' => 'Day',
        'month' => 'Month',
        'year' => 'Year',
        'email' => 'Email',
        'password' => 'Password',
        'repeat_password' => 'Repeat the password',
        'newsletter' => 'Accept the newsletter',
        'submit' => 'Register now',
        'all_fields_mandatory' => 'All fields are required.',
    ],

    'contact' => [
        'title' => 'Contact us',
        'to' => 'Target',
        'first_name' => 'Firstname',
        'last_name' => 'Lastname',
        'subject' => 'Subject',
        'email' => 'Email',
        'message' => 'Your message',
        'submit' => 'Send',
        'post' => [
            'message_sent' => 'Your message has been sent.',
            'confirm' => 'Confirmation',
            'error' => [
                'title' => 'Error(s) :',
                'contact_not_found' => 'Please select a target.',
                'captcha_invalid' => 'Please confirm the security.',
                'last_name_empty' => 'Your last name is empty.',
                'last_name_invalid' => 'Your last name is invalid.',
                'first_name_empty' => 'Your first name is empty.',
                'first_name_invalid' => 'Your first name is invalid.',
                'email_empty' => 'Your email is empty.',
                'email_invalid' => 'Your email is invalid.',
                'subject_empty' => 'The subject is empty.',
                'subject_invalid' => 'The subject is invalid.',
                'message_empty' => 'The message is empty.',
                'message_invalid' => 'The message is invalid.',
            ],
        ],
    ],

    'search' => [
        'title' => 'Search',
        'keys' => 'Keys',
        'error_keys' => 'You must give some keys in order to search.',
        'pages' => 'Page(s)',
        'no_pages' => 'No page has been found.',
        'articles' => 'Article(s)',
        'no_articles' => 'No article has been found.',
        'documents' => 'Document(s)',
        'no_documents' => 'No document has been found.',
        'albums' => 'Album(s)',
        'no_albums' => 'No album has been found.',
    ],
];
