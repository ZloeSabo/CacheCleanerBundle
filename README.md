Cache cleaner bundle
---

Motivation
==========

Have you ever wasted some hours setting up project/bundle and then realizing the fact your issues caused by old symfony cache?
Then this bundle is for you. It watches over config, bundle directories and removes cache in case something changes there.

**You need this for development purposes only**

Installation
============

Step 0 (optional): Install inotify extension
--------------------------------------------
You can skip this step, but then functionality will be limited (can't detect file deletions for example) 

```bash
sudo pecl install inotify
# steps can differ for your linux distibution
sudo echo "extension=inotify.so" > /etc/php5/mods-available/inotify.ini
cd /etc/php5/cli/conf.d && ln -s ../../mods-available/inotify.ini
```


Step 1: Download the Bundle
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```bash
$ composer require zloesabo/cache-cleaner-bundle:dev-master
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Step 2: Enable the Bundle
-------------------------

Then, enable the bundle by adding the following line in the `app/AppKernel.php`
file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new ZloeSabo\CacheCleanerBundle();
        }
        // ...
    }

    // ...
}
```

Usage
============

```bash
app/console cache:autoclean
```

The cleaner will remain running