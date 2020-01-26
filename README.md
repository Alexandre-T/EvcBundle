# EvcBundle : Installation

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Applications that use Symfony Flex
----------------------------------

Open a command console, enter your project directory and execute:

```console
$ composer require alexandret/evc-bundle
```

Open env (or env.dist) file and search the new created lines to configure variable
```
###> alexandret/evc-bundle ###
EVC_API = ''
EVC_USERNAME = ''
EVC_PASSWORD = ''
###< alexandret/evc-bundle ###
```

Look at the configuration section for more explanation

Applications that don't use Symfony Flex
----------------------------------------

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require alexandret/evc-bundle
```

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    Alexandre\EvcBundle\AlexandreEvcBundle::class => ['all' => true],
];
```

Open your `env` file and add the value of necessary configuration variables

### Step 3: Create a alexandre_evc.yaml file

Create a `alexandre_evc.yaml` file under the config/packages subdirectory.
Then copy and paste these lines:

```yaml
#config/packages/alexandre_evc.yaml
alexandre_evc:
  api_id: '%env(EVC_API)%'
  username: '%env(EVC_USERNAME)%'
  password: '%env(EVC_PASSWORD)%'
```

Do NOT replace env by your password. You have to configure your `.env` file as described in the below paragraph.

Configuration
-------------

EvcBundle needs data information. You shall complete them in your env file.

* EVC_API: The api key provided by evc support
* EVC_USERNAME: Your evc.de account number
* EVC_PASSWORD: Your API password. This is **NOT** your evc.de account password. 
It's a separate password that you get from the EVC office.

Here is a sample:
```dotenv
###> alexandret/evc-bundle ###
EVC_API = sample_key
EVC_USERNAME = my_name
EVC_PASSWORD = my_password
###< alexandret/evc-bundle ###
```

How to mock your requests to the API?
-------------------------------------
You want to test your application with mocked customer and avoid to send data to the real evc.de API?
By default, our bundle is created to use a requester service that embed Unirest/Request. In your
`config/package/dev` repository, add new lines at the end of the `service.yaml` file. (Do not hesitate to create
a new `service.yaml` file if there is no file yet.

```yaml
# config/packages/dev/service.yaml
# config/packages/test/service.yaml
services:
    alexandre_evc_request:    
        class: Alexandre\EvcBundle\Service\EmulationService
        arguments:
            $api: '%env(EVC_API)%'
            $username: '%env(EVC_USERNAME)%'
            $password: '%env(EVC_PASSWORD)%'
```
Instead of calling the `RequesterService`, dev environment will use an `EmulationService`.

There is four declared customer.
 * `11111` is the identifier of a customer that does not exists. Use it when you want to test your application with a non-existent user
 * `22222` customer exists, but he is not a personal user. Use it when you want to test your application with a non-personal customer
 * `33333` customer exists and he is a personal user with 42 credits.
 * `44444` is a personal customer too. He has 42 credits too.
 * `55555` Each time you call the 55555 customer, Emulation service will throw a `NetworkException`to test your application as if evc.de wasn't reachable.
 * `66666` Each time you call the 66666 customer, Emulation service will throw a `CredentialException` to test your application when your configuration is wrong.
 * `77777` Each time you call the 77777 customer, Emulation service will throw a `LogicException`. We do not think it is useful, but if you want to test.
 
Exceptions
----------
 * `NetworkException`: Network exceptions are thrown if evc.de is not reachable.
 * `CredentialException`: Credential exceptions are thrown when you do a misconfiguration on your evc.de credentials.
 * `LogicException`: Logical exceptions are thrown when evc.de is returning a not expected response. It could happen if there is a bug on this bundle, or if the api changes.
 * `EvcException`: The three previous exceptions inherits the `EvcException`.
