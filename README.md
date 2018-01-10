<h1 align="center">Sylius Adyen Plugin</h1>

## Support

Do you want us to customize this plugin for your specific needs? Write us an email on mikolaj.krol@bitbag.pl :computer:

## Installation
    
Add plugin dependencies to your AppKernel.php file:
```php
public function registerBundles()
{
    return array_merge(parent::registerBundles(), [
        ...
        
        new \BitBag\SyliusAdyenPlugin\BitBagSyliusAdyenPlugin(),
    ]);
}
```

Import routing in your `app/config/routing.yml` file:

```yaml

# app/config/routing.yml
...

bitbag_sylius_adyen_plugin:
    resource: "@BitBagSyliusAdyenPlugin/Resources/config/routing.yml"
```

## Settings

### Signature for notifications

- https://docs.adyen.com/developers/ecommerce-integration/hmac-signature-calculation/signature-for-notifications

### Settings for notifications

- Sign in to the [Customer Area](https://ca-test.adyen.com/) and navigate to Settings > Server Communication.
- For Standard notification click Edit & Test.
- Required settings for transport
    - Set URL for notifications: https://{your_domain}/payment/adyen/notify
    - Set method: HTTP POST
- Expand Additional Settings.
- Click Generate New HMAC Key and copy the key to use it for your server configuration.
- Click Save Configuration. The generated HMAC key is now in effect and is used to sign all newly generated notifications.

### How to get HMAC keys for a skin

- https://docs.adyen.com/developers/user-management/how-to-get-hmac-keys-for-a-skin

### How to get code for a skin

- Sign in to the [Customer Area](https://ca-test.adyen.com/) using your company-level account.
- On the left navigation sidebar, click Skins.
- Select an existing skin from the List and copy the contents of the skin code column

### How to get Merchant account for a skin

- Sign in to the [Customer Area](https://ca-test.adyen.com/) using your company-level account.
- On the left navigation sidebar, click Skins.
- Select an existing skin from the List and copy the contents of the Valid accounts column

### Test card numbers

- https://docs.adyen.com/developers/test-cards/test-card-numbers

## Usage

### Running plugin tests

  - PHPSpec

    ```bash
    $ bin/phpspec run
    ```

  - Behat (non-JS scenarios)

    ```bash
    $ bin/behat --tags="~@javascript"
    ```

  - Behat (JS scenarios)
 
    1. Download [Chromedriver](https://sites.google.com/a/chromium.org/chromedriver/)
    
    2. Run Selenium server with previously downloaded Chromedriver:
    
        ```bash
        $ bin/selenium-server-standalone -Dwebdriver.chrome.driver=chromedriver
        ```
    3. Run test application's webserver on `localhost:8080`:
    
        ```bash
        $ (cd tests/Application && bin/console server:run 127.0.0.1:8080 -d web -e test)
        ```
    
    4. Run Behat:
    
        ```bash
        $ bin/behat --tags="@javascript"
        ```

### Opening Sylius with your plugin

- Using `test` environment:

    ```bash
    $ (cd tests/Application && bin/console sylius:fixtures:load -e test)
    $ (cd tests/Application && bin/console server:run -d web -e test)
    ```
    
- Using `dev` environment:

    ```bash
    $ (cd tests/Application && bin/console sylius:fixtures:load -e dev)
    $ (cd tests/Application && bin/console server:run -d web -e dev)
    ```
