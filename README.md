# Env-Parser

A powerful PHP parser for env files.

## Features
- Easy to use , zero configurations
- Convert .env file into associative array
- Type casting for numbers , booleans and NULL
- Support variables with optional default values 
- Support adding comments to your .env file
- Parsed data are saved in $_ENV and $_SERVER
- Support reading parsed data using `getenv(...)`

## Installation

``` 
composer require agashe/env-parser
```

## Documentation

To start using env-parser in your project , you declare a new instance of class `EnvParser` , then call `parse` method which accepts only one parameter , the path to your .env file :

```
<?php

require "vendor/autoload.php";

use EnvParser\Parser;

$parser = new Parser();

$data = $parser->parse('/path/to/my/env/file');

var_dump($data);
```

### Type Casting

By default env-parser casts on all parsed data , so for the following file :

```
TEST_BOOL=true
TEST_NULL=null
TEST_FLOAT=1.234
```

The parser will return :

```
array(3) {
  ["TEST_BOOL"]=>
  bool(true)
  ["TEST_NULL"]=>
  NULL
  ["TEST_FLOAT"]=>
  float(1.234)
}
```

### $_ENV and $_SERVER

In addition to the returned result , env-parser will save the extracted data into $_ENV and $_SERVER , so you have wide range of options to access your data :

```
$data = $parser->parse('/path/to/my/env/file');

echo $data['DATABASE_NAME']; // app_db
echo $_ENV['DATABASE_NAME']; // app_db
echo $_SERVER['DATABASE_NAME']; // app_db
```

### Using `getenv` method

PHP provides 2 methods to work with env variables `putenv()` and `getenv()` , env-parser use `putenv()` to save your .env file extracted data , so you can access it easily using `getenv()` :

```
$data = $parser->parse('/path/to/my/env/file');

echo getenv('DATABASE_NAME'); // app_db
```

### Comments

Feel free to add all the comments you want to your .env file , env-parser will skip all of the comments and will return the only the data , so assuming you have the following .env file :

```
# MY DataBase Connection
DB_HOST = http://localhost # My database host
DB_NAME = "app_db" # My database name
```

It will be parsed into :

```
array(2) {
  ["DB_HOST"]=>
  string(16) "http://localhost"
  ["DB_NAME"]=>
  string(6) "app_db"
}
```

### Variables

Env-parser support variables inside .env files , so you can use previous defined keys as variables with other keys :

```
# Default email domain
EMAIL_DOMAIN = our-emails-domain.com

# Admin's default username
ADMIN_USERNAME = super-admin

# Admin's default email
ADMIN_EMAIL = ${ADMIN_USERNAME}@${EMAIL_DOMAIN}
```

The above file will be parsed to :

```
array(3) {
  ["EMAIL_DOMAIN"]=>
  string(21) "our-emails-domain.com"
  ["ADMIN_USERNAME"]=>
  string(11) "super-admin"
  ["ADMIN_EMAIL"]=>
  string(33) "super-admin@our-emails-domain.com"
}
```
You can also add default values for your variables , in case of missing data :

```
# Default email domain
EMAIL_DOMAIN = our-emails-domain.com

# Admin's default email
ADMIN_EMAIL = ${ADMIN_USERNAME:=admin}@${EMAIL_DOMAIN}
```
env-parser will use the default data you provided :

```
array(2) {
  ["EMAIL_DOMAIN"]=>
  string(21) "our-emails-domain.com"
  ["ADMIN_EMAIL"]=>
  string(27) "admin@our-emails-domain.com"
}
```

In case no default value was provided the parser will return empty string for that variable , but the rest of the value will be parsed normally.

## Examples

```
# .env file

# check basic functionality 
TEST_NO_QUOTES=Parser
TEST_SPACES    =     "1.0"
    TEST_QUOTES='test'

# check boolean values
TEST_TRUE=true
TEST_FALSE=false

# check nullable values
TEST_NULL =null
TEST_EMPTY =

# check numeric values
TEST_FLOAT= 1500.223
TEST_INT= 123

# check comments
TEST_COMMENT_1 = test#test comment
TEST_COMMENT_2 = "test#test"
TEST_COMMENT_3 = 'some text'        # test  # comment

# check variables
TEST_VAR="${TEST_QUOTES}/${TEST_NO_QUOTES}"
TEST_DEFAULT_VAL="${UNKNOWN:=test} or some text like ${TEST_NO_QUOTES}"
```
This .env file will be parsed into :

```
array(14) {
  ["TEST_NO_QUOTES"]=>
  string(6) "Parser"
  ["TEST_SPACES"]=>
  string(3) "1.0"
  ["TEST_QUOTES"]=>
  string(4) "test"
  ["TEST_TRUE"]=>
  bool(true)
  ["TEST_FALSE"]=>
  bool(false)
  ["TEST_NULL"]=>
  NULL
  ["TEST_EMPTY"]=>
  string(0) ""
  ["TEST_FLOAT"]=>
  float(1500.223)
  ["TEST_INT"]=>
  int(123)
  ["TEST_COMMENT_1"]=>
  string(4) "test"
  ["TEST_COMMENT_2"]=>
  string(9) "test#test"
  ["TEST_COMMENT_3"]=>
  string(9) "some text"
  ["TEST_VAR"]=>
  string(11) "test/Parser"
  ["TEST_DEFAULT_VAL"]=>
  string(29) "test or some text like Parser"
}
```

## License
(Env-Parser) released under the terms of the MIT license.