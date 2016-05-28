DtBundleDemo
============

Symfony3 Demo Project For My DatatablesBundle

## Install

### 1. Clone the project with Git:

```
git clone git@github.com:stwe/DtBundleDemo.git
```

### 2. Install assets and dump js routing

```
php bin/console assets:install --symlink
```

```
php bin/console fos:js-routing:dump
```

### 3. Run fixtures

```
php bin/console doctrine:fixtures:load --no-interaction
```

## Login

**Admin:**

- Username: root
- Password: root

**User:**

- Username: user
- Password: user

## License

This bundle is under the MIT license. See the complete license in the bundle:

    Resources/meta/LICENSE
