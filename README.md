
# MySQL

This is a PHP class I wrote a while ago.  
It extends PHP's `mysqli` class with a method that
allows for automatic parameter binding.  
It also defaults to UTF-8 and persistant connections to the database.

#### Benefits

First of all the SQL injection safeness that comes with
statements in PHP. Then `mysql::statement` handles everything,
from statement creation/preparation over
parameter and result binding to statement execution resulting
in a wonderfully packed result set you can iterate over without hassle.
Not to forget: a *lot* less SLOC.

## Usage

Writing to the database:

```php
<?php
  
  require 'mysql.php';
  
  // Creating a new connection:
  $db = new mysql( 'localhost', 'root', 'password' )
  
  // Running a query:
  $result = $db->statement(
    'INSERT INTO CountryLanguage VALUES (?, ?, ?, ?)',
    [ 'DEU', 'German', 'D', 11.2 ]
  );
  
?>
```

Since the above is a write op, the `$result` will be an `array` like this:

```php
<?php
  [
    'affectedRows' => 1,
    'insertId'     => 76
  ]
?>
```

Reading from the database:

```php
<?php
  
  $result = $db->statement(
    'SELECT * FROM wp_users WHERE id=?',
    [ 1 ]
  );
  
?>
```

Will yield an `array` wherein each result row is represented as an `object`:

```php
<?php
  [
    0 => {
      ID                  =>  1
      user_login          => 'Admin'
      user_pass           => '$P$B8et03XpvL0jKTQAPj9OzMNqAt/v2m.'
      user_nicename       => 'admin'
      user_email          => 'some@email.com'
      user_url            =>  NULL
      user_registered     => '2012-07-12 12:03:35'
      user_activation_key => 'BAa2sbNYNREyIVogyjoj'
      user_status         =>  0
      display_name        => 'Admin'
    }
  ]
?>
```

## API

### mysql::__construct( *host*, *user*, *pass*, *db*, *options* )

> *string* [__host = NULL__]  
> *string* [__user = NULL__]  
> *string* [__pass = NULL__]  
> *string* [__db = NULL__]  
> *array* [__options = array()__]

### mysql::statement( *sql*, *params* )

> *string* [__sql__]  
> *array* [__params = array()__]
