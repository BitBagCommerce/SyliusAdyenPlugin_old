> :warning: **BEWARE!**
> This repository has been deprecated and will not be maintained or evolved by the BitBag Team. You can still use it with compatible Sylius versions, but at your own risk, as no bugs will be fixed on it.

> Here you can find the new version of the plugin: [SyliusAdyenPlugin](https://github.com/BitBagCommerce/SyliusAdyenPlugin)

# Adyen Payments Plugin for Sylius
----
[![](https://img.shields.io/packagist/l/bitbag/adyen-plugin.svg) ](https://packagist.org/packages/bitbag/adyen-plugin "License") [ ![](https://img.shields.io/packagist/v/bitbag/adyen-plugin.svg) ](https://packagist.org/packages/bitbag/adyen-plugin "Version") [ ![](https://img.shields.io/travis/BitBagCommerce/SyliusAdyenPlugin/master.svg) ](http://travis-ci.org/BitBagCommerce/SyliusAdyenPlugin "Build status") [ ![](https://img.shields.io/scrutinizer/g/BitBagCommerce/SyliusAdyenPlugin.svg) ](https://scrutinizer-ci.com/g/BitBagCommerce/SyliusAdyenPlugin/ "Scrutinizer") [![](https://poser.pugx.org/bitbag/adyen-plugin/downloads)](https://packagist.org/packages/bitbag/adyen-plugin "Total Downloads") [![Slack](https://img.shields.io/badge/community%20chat-slack-FF1493.svg)](http://sylius-devs.slack.com) [![Support](https://img.shields.io/badge/support-contact%20author-blue])](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_adyen)

At BitBag we do believe in open source. However, we are able to do it just because of our awesome clients, who are kind enough to share some parts of our work with the community. Therefore, if you feel like there is a possibility for us working together, feel free to reach us out. You will find out more about our professional services, technologies and contact details at [https://bitbag.io/](https://bitbag.io/?utm_source=github&utm_medium=referral&utm_campaign=plugins_adyen).

## Table of Content
---
* [Overwiev](#overwiev)
* [Support](#we-are-here-to-help)
* [Installation](#installation)
  * [Requirements](#requirements)
  * [Settings](#settings)
  * [Usage](#usage)
* [About us](#about-us)
  * [Community](#community)
* [Demo Sylius shop](#demo-sylius-shop)
* [Additional Sylius resources for developers](#additional-resources-for-developers)
* [License](#license)
* [Contact](#contact)


# Overwiev
---
This plugin allows you to integrate Adyen payment system with Sylius platform app. It includes all Sylius and Adyen payment features.

## We are here to help
This **open-source plugin was developed to help the Sylius community** and make Adyen payments platform available to any Sylius store. If you have any additional questions, would like help with installing or configuring the plugin or need any assistance with your Sylius project - let us know!

[![](https://bitbag.io/wp-content/uploads/2020/10/button-contact.png)](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_adyen)


## Installation
----
### Requirements

We work on stable, supported and up-to-date versions of packages. We recommend you to do the same.

| Package | Version |
| --- | --- |
| PHP | ^7.1 |
| Sylius | 1.0.4 |

----

```bash
$ composer require bitbag/adyen-plugin
```
    
Add plugin dependencies to your `config/bundles.php` file:

```php
return [
    ...
    BitBag\SyliusAdyenPlugin\BitBagSyliusAdyenPlugin::class => ['all' => true],
];
```

Import routing in your `config/routes.yaml` file:

```yaml

# config/routes.yaml
...

bitbag_sylius_adyen_plugin:
    resource: "@BitBagSyliusAdyenPlugin/Resources/config/routing.yml"
```

Import required config in your `config/packages/_sylius.yaml` file:

```yaml

# config/packages/_sylius.yaml

imports:

   ...
   
   - { resource: "@BitBagSyliusAdyenPlugin/Resources/config/config.yml" }
```

## Settings
----
### Signature for notifications

- https://docs.adyen.com/development-resources/webhooks#set-up-notifications-in-your-customer-area

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

https://docs.adyen.com/classic-integration/hosted-payment-pages/skin#configuration

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
----
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

# About us
---

BitBag is an agency that provides high-quality **eCommerce and Digital Experience software**. Our main area of expertise includes eCommerce consulting and development for B2C, B2B, and Multi-vendor Marketplaces.
The scope of our services related to Sylius includes:
- **Consulting** in the field of strategy development
- Personalized **headless software development**
- **System maintenance and long-term support**
- **Outsourcing**
- **Plugin development**
- **Data migration**

Some numbers regarding Sylius:
* **20+ experts** including consultants, UI/UX designers, Sylius trained front-end and back-end developers,
* **100+ projects** delivered on top of Sylius,
* Clients from  **20+ countries**
* **3+ years** in the Sylius ecosystem.

---

If you need some help with Sylius development, don't be hesitate to contact us directly. You can fill the form on [this site](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_adyen) or send us an e-mail to hello@bitbag.io!

---

[![](https://bitbag.io/wp-content/uploads/2020/10/badges-sylius.png)](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_adyen)

## Community
----
For online communication, we invite you to chat with us & other users on [Sylius Slack](https://sylius-devs.slack.com/).

# Demo Sylius shop
---

We created a demo app with some useful use-cases of plugins!
Visit b2b.bitbag.shop to take a look at it. The admin can be accessed under https://b2b.bitbag.shop/admin/login link and sylius: sylius credentials.
Plugins that we have used in the demo:
| BitBag's Plugin | GitHub | Sylius' Store|
| ------ | ------ | ------|
| ACL PLugin | *Private. Available after the purchasing.*| https://plugins.sylius.com/plugin/access-control-layer-plugin/|
| Braintree Plugin | https://github.com/BitBagCommerce/SyliusBraintreePlugin |https://plugins.sylius.com/plugin/braintree-plugin/|
| CMS Plugin | https://github.com/BitBagCommerce/SyliusCmsPlugin | https://plugins.sylius.com/plugin/cmsplugin/|
| Elasticsearch Plugin | https://github.com/BitBagCommerce/SyliusElasticsearchPlugin | https://plugins.sylius.com/plugin/2004/|
| Mailchimp Plugin | https://github.com/BitBagCommerce/SyliusMailChimpPlugin | https://plugins.sylius.com/plugin/mailchimp/ |
| Multisafepay Plugin | https://github.com/BitBagCommerce/SyliusMultiSafepayPlugin |
| Wishlist Plugin | https://github.com/BitBagCommerce/SyliusWishlistPlugin | https://plugins.sylius.com/plugin/wishlist-plugin/|
| **Sylius' Plugin** | **GitHub** | **Sylius' Store** |
| Admin Order Creation Plugin | https://github.com/Sylius/AdminOrderCreationPlugin | https://plugins.sylius.com/plugin/admin-order-creation-plugin/ |
| Invoicing Plugin | https://github.com/Sylius/InvoicingPlugin | https://plugins.sylius.com/plugin/invoicing-plugin/ |
| Refund Plugin | https://github.com/Sylius/RefundPlugin | https://plugins.sylius.com/plugin/refund-plugin/ |

**If you need an overview of Sylius' capabilities, schedule a consultation with our expert.**

[![](https://bitbag.io/wp-content/uploads/2020/10/button_free_consulatation-1.png)](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_adyen)

## Additional resources for developers
---
To learn more about our contribution workflow and more, we encourage ypu to use the following resources:
* [Sylius Documentation](https://docs.sylius.com/en/latest/)
* [Sylius Contribution Guide](https://docs.sylius.com/en/latest/contributing/)
* [Sylius Online Course](https://sylius.com/online-course/)

## License
 ---

This plugin's source code is completely free and released under the terms of the MIT license.

[//]: # (These are reference links used in the body of this note and get stripped out when the markdown processor does its job. There is no need to format nicely because it shouldn't be seen.)

## Contact
---
If you want to contact us, the best way is to fill the form on [our website](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_adyen) or send us an e-mail to hello@bitbag.io with your question(s). We guarantee that we answer as soon as we can!

[![](https://bitbag.io/wp-content/uploads/2020/10/footer.png)](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_adyen)
