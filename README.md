# laravel-feature-flag

Internal feature flag client for Unleash & Flipt

## Config

```php
<?php

return [
    'default' => env('FEATURE_FLAG_CONNECTION', 'disabled-toggler'),

    'providers' => [
        'flipt' => [
            'namespace' => env('FLIPT_NAMESPACE', 'default'),
            'host' => env('FLIPT_HOST'),
            'token' => env('FLIPT_TOKEN'),
        ],
        'unleash' => [
            'name' => env('UNLEASH_NAME', 'default'),
            'url' => env('UNLEASH_URL'),
            'id' => env('UNLEASH_ID'),
            'token' => env('UNLEASH_TOKEN'),
        ],
        'disabled-toggler' => [
            'optimistic' => env('DISABLED_TOGGLER_OPTIMISTIC', true)
        ]
    ],

    'disable' => env('FEATURE_FLAG_DISABLE', false),
];

```

## Env

```
# Feature Flag
FEATURE_FLAG_CONNECTION=unleash
FEATURE_FLAG_DISABLE=false

# Unleash Client
UNLEASH_NAME=default
UNLEASH_URL=http://localhost:4242/api/
UNLEASH_ID=EXAMPLE_ID
UNLEASH_TOKEN=EXAMPLE_TOKEN

# Disabled Toggler Client
DISABLED_TOGGLER_OPTIMISTIC=true

# Flipt Client
FLIPT_NAMESPACE=default
FLIPT_HOST=http://localhost:8080
FLIPT_TOKEN=

```

## Install

`composer require yomafleet/payment-provider`

## Usage

Implements the UserContract in User model

```php
<?php

namespace App\Models;

use Yomafleet\FeatureFlag\UserContract;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements UserContract
{
    /**
     * Get ID of the user.
     *
     * @return string|integer
     */
    public function id(): string|int
    {
        return $this->id;
    }

    /**
     * Get all roles of the user.
     *
     * @return array
     */
    public function roles(): array
    {
        return $this->roles->toArray();
    }

    /**
     * Check if the user has given role.
     *
     * @param string $name
     * @return boolean
     */
    public function hasRole(string $name): bool
    {
        return $this->hasRole($name);
    }
}
```

And check the feature flag with Facade

```php
<?php

use Yomafleet\FeatureFlag\Facade as Flag;

//...
//...
//...
$isEnabled = Flag::enabled('someflag-check');
```

Can use blade directive as well

```php
<?php

@feature('something')
    <div>Something</div>
@else
    <div>Nothing</div>
@endfeature
```

Also, can use in route

```php
<?php

use Illuminate\Support\Facades\Route;

Route::get('example', [\Example\Controller::class, 'index'])->feature('something:admin')
```
