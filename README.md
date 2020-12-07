# STO


## REFERENCE

Database : [admin/data/sql/default.sql](admin/data/sql/default.sql)

> Password Encryption Using :

```

password_hash(
    sha1( string $plain_text )
);

```

`Plain Password` / `Hashed Password` will be converted into encrypted password 
when user in database called by script using `password_needs_rehash` functions.

### NOTE BYPASS

```php

// dont update user online
hook_add('set_supervisor_online', 'return_false');
hook_add('set_student_online', 'return_false');

// manipulate cookie student data
hook_add('cookie_student_data', function () {
    return base64_encode(create_json_auth_user(1, STUDENT));
});

// make pretty
hook_add('json_success_options', function () {
    return JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES;
});

```
