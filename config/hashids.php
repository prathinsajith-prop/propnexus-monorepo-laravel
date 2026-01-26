<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Custom Alphabet
    |--------------------------------------------------------------------------
    |
    | Sqids supports personalizing your IDs by accepting a custom alphabet.
    | The default alphabet contains all lowercase and uppercase letters
    | and numbers. You can customize this to match your needs.
    |
    */

    'alphabet' => env('HASHIDS_ALPHABET', 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'),

    /*
    |--------------------------------------------------------------------------
    | Minimum ID Length
    |--------------------------------------------------------------------------
    |
    | By default, IDs are going to be the shortest possible. You can also
    | set the minimum ID length to obfuscate how large the number behind
    | the ID is and make IDs more uniform.
    |
    */

    'length' => env('HASHIDS_LENGTH', 0),

    /*
    |--------------------------------------------------------------------------
    | Blocklist
    |--------------------------------------------------------------------------
    |
    | Sqids can prevent specific words from appearing anywhere in the
    | auto-generated IDs. You can provide a custom blocklist or use
    | the default one which contains common profanity in multiple languages.
    | Set to empty array [] to disable blocklist entirely.
    |
    */

    'blocklist' => env('HASHIDS_BLOCKLIST') ? explode(',', env('HASHIDS_BLOCKLIST')) : null,

    /*
    |--------------------------------------------------------------------------
    | Legacy Salt Support (Deprecated)
    |--------------------------------------------------------------------------
    |
    | For backward compatibility with Hashids. In Sqids, this is incorporated
    | into the alphabet. This setting will be removed in future versions.
    |
    */

    'salt' => env('HASHIDS_SALT', env('APP_KEY', '')),

];
