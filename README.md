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

