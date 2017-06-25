# Scaffolding for Laravel 5 migrations

This package provides a command make:migration@ that will do more routine work for you, 
trying to guess a table name by migration name, creating needed relations by column 
names, exempting you from writing boilerplate code like.

```php
$table->integer('category_id')->unsigned();
$table->foreign('category_id')->references('id')->on('categories');
```

Instead you should pass second argument with columns delimited with a comma.

## Examples

Command:

```
php artisan make:migration@ create_users_table id,name,password,role_id
```

Output:

```php
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('password');
            $table->integer('role_id')->unsigned();
            $table->foreign('role_id')->references('id')->on('roles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropTable();
        });
    }
}
```
