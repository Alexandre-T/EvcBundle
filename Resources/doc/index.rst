EvcBundle : Installation
========================

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Applications that use Symfony Flex
----------------------------------

Open a command console, enter your project directory and execute:

.. code:: console
    $ composer require alexandret/evc-bundle

Open env (or env.dist) file and search the new created lines to configure variable

.. code:: dotenv
    ###> alexandret/evc-bundle ###
    EVC_API = ''
    EVC_USERNAME = ''
    EVC_PASSWORD = ''
    ###< alexandret/evc-bundle ###

Look at the configuration section for more explanation

Applications that don't use Symfony Flex
----------------------------------------

Step1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

.. code:: console
    $ composer require alexandret/evc-bundle

Step2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

.. code:: php
    // config/bundles.php

    return [
        // ...
        Alexandre\EvcBundle\AlexandreEvcBundle::class => ['all' => true],
    ];

Open your `env` file and add the value of necessary configuration variables

### Step 3: Create a alexandre_evc.yaml file

Create a `alexandre_evc.yaml` file under the config/packages subdirectory.
Then copy and paste these lines:

.. code::yaml
    #config/packages/alexandre_evc.yaml
    alexandre_evc:
      api_id: '%env(EVC_API)%'
      username: '%env(EVC_USERNAME)%'
      password: '%env(EVC_PASSWORD)%'

Do NOT replace env by your password. You have to configure your `.env` file as described in the below paragraph.

Configuration
-------------

EvcBundle needs data information. You shall complete them in your env file.

- EVC_API: The api key provided by evc support
- EVC_USERNAME: Your evc.de account number
- EVC_PASSWORD: Your API password. This is **NOT** your evc.de account password.
It's a separate password that you get from the EVC office.

Here is a sample:
.. code::dotenv
    ###> alexandret/evc-bundle ###
    EVC_API = sample_key
    EVC_USERNAME = my_name
    EVC_PASSWORD = my_password
    ###< alexandret/evc-bundle ###
