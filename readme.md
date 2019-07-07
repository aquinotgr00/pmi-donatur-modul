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


## Testing
Run the tests with:

``` bash
vendor/bin/phpunit
```