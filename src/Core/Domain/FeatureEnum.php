<?php

declare(strict_types=1);

namespace App\Core\Domain;

final class FeatureEnum
{
    /* Modules */
    public const GALLERY = 'GALLERY';
    public const PARTNER = 'PARTNER';
    public const DOCUMENT = 'DOCUMENT';
    public const CONTACT = 'CONTACT';
    public const SEARCH = 'SEARCH';
    public const ARTICLE = 'ARTICLE';

    /* Comments */
    public const COMMENT_ARTICLE = 'COMMENT_ARTICLE';
    public const COMMENT_ALBUM = 'COMMENT_ALBUM';

    /* Utils */
    public const CAPTCHA = 'CAPTCHA';
    public const ANALYTICS = 'ANALYTICS';

    /* User management */
    public const REGISTER = 'REGISTER';
    public const LOST_PASSWORD = 'LOST_PASSWORD';
    public const PROFILE_UPDATE = 'PROFILE_UPDATE';
    public const PROFILE_DETAILS = 'PROFILE_DETAILS';

    public static function getModules(): array
    {
        return [
            self::GALLERY,
            self::PARTNER,
            self::DOCUMENT,
            self::CONTACT,
            self::SEARCH,
            self::ARTICLE,
            self::COMMENT_ARTICLE,
            self::COMMENT_ALBUM,
            self::CAPTCHA,
            self::ANALYTICS,
            self::REGISTER,
            self::LOST_PASSWORD,
            self::PROFILE_UPDATE,
            self::PROFILE_DETAILS,
        ];
    }
}
