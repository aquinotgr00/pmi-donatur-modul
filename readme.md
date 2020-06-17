# pmi-donatur

This package provides API services for 
* create campaigns
* get list of campaigns
* donating money and/or goods
* donator registration
* upload proof of donation

## Getting Started

### Prerequisites
* PMI Admin
```sh
composer require bajaklautmalaka/pmi-admin
```
* Midtrans Payment Gateway

## Install
To include the private Bitbucket repository via Composer you need to add this lines into your composer.json:

```json

    "repositories": [
      {
        "type": "vcs",
        "url" : "git@bitbucket.org:bajak_laut_malaka/pmi-donatur.git"
      },
    ]
```

Add the package with:

```bash
composer require bajaklautmalaka/pmi-donatur
```


## Usage
Write a few lines about the usage of this package.


## Models

### User (App\User.php)

We need to update the user model with a trait. Your model should now look like this:

```
use BajakLautMalaka\PmiDonatur\Traits\DonatorUserTrait;
 
class User extends Model implements AuthenticatableContract, CanResetPasswordContract {
 
    use Authenticatable, CanResetPassword, DonatorUserTrait;
 
    // Your other stuff
    ...
 
}
```

**Note: It's important that the use statement is added to your User model**

---


## Testing
Run the tests with:

``` bash
vendor/bin/phpunit
```