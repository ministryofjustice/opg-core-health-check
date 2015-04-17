# OPG Health Check ZF2 Module

This module provides zend services related to checking the health of the other opg core applications, specifically [opg-core-back-end], [opg-core-front-end] and [opg-core-auth-membrane]

## Installation

1. Install this module as a dependency on your zf2 project via Composer. If you don't have composer then follow the steps
to download it from their site: [https://getcomposer.org/download/](https://getcomposer.org/download/)
2. register module with your application

Your ```composer.json``` should look something like this
```
{
    "repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:ministryofjustice/opg-core-health-check.git"
        }
    ],
    "require": {
        "ministryofjustice/opg-core-health-check": "1.0.*",
    }
}
```

Your ```config/application.config.php``` should look something like this

```
$modules = array(
    ...
    'HealthCheck',
);
```

## Usage

**Example controller action:** uses the environmental variables checker service and returns error 500 and json error if there are any missing.

```PHP
    public function checkEnvVarsAreSetAction()
    {
        try {
            $this->getServiceLocator()->get('EnvironmentVariablesChecker')->check();
        } catch (HttpException $e) {
            // Environment variable(s) missing:
            $this->getResponse()->setStatusCode($e->getStatusCode());

            return new JsonModel(array('error' => $e->getMessage()));
        }

        return $this->getResponse();
    }
```

## Testing

1. Clone it.
2. Install dependencies via composer. If you don't have composer then follow the steps
to download it from their site: [https://getcomposer.org/download/](https://getcomposer.org/download/)
3. run PHPUnit.

```
git clone git@github.com:ministryofjustice/opg-core-health-check.git
cd opg-core-health-check.git
composer install
php ./vendor/bin/phpunit
```

## Contributing

1. Clone it.
2. Create your feature branch: `git checkout -b my-new-feature`
3. Commit your changes: `git commit -am 'Add some feature'`
4. Push to the branch: `git push origin my-new-feature`
5. Submit a pull request :D

## History

[2015-04-16](https://github.com/ministryofjustice/opg-core-health-check/commit/f4f135e33ab7fe3ca546f21584729f425657619a): Repository repurposed as a zf2 module from original empty zf2 skeleton app

## Credits

* [Richard Saunders]

ministryofjustice

TODO: Write license

[opg-core-back-end]: https://github.com/ministryofjustice/opg-core-back-end
[opg-core-front-end]: https://github.com/ministryofjustice/opg-core-front-end
[opg-core-auth-membrane]: https://github.com/ministryofjustice/opg-core-auth-membrane
[Richard Saunders]: https://github.com/rs-saunders
