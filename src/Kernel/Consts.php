<?php
namespace Zento\Kernel;

class Consts  {
    // const ZENTO_KERNEL_VERSION = 'init';
    const ZENTO_KERNEL_PACKAGE_NAME = 'Zento_Kernel';
    const MY_PACKAGES_ROOT_FOLDER = 'mypackages';
    const PACKAGE_COMPOSER_FILE = 'composer.json';
    const PACKAGE_ASSEMBLE_FILE = '_assemble.php';

    //define a package's folders
    const PACKAGE_CONFIG_FOLDER = 'config';
    const PACKAGE_ASSETS_FOLDER = 'resources' . DIRECTORY_SEPARATOR . 'public';
    const PACKAGE_SETUP_DATABASE_FOLDER = 'setup' . DIRECTORY_SEPARATOR . 'database';
    const PACKAGE_VIEWS_FOLDER = 'resources' . DIRECTORY_SEPARATOR . 'views';
    const PACKAGE_ROUTES_FOLDER = 'routes';

    //CACHE KEYS
    const CACHE_KEY_PREFIX = 'zento.';
    const CACHE_KEY_PACKAGES = self::CACHE_KEY_PREFIX . 'packages';
    const CACHE_KEY_PACKAGE_ASSEMBLY = self::CACHE_KEY_PREFIX . 'packages.assembly';
    const CACHE_KEY_ENABLED_PACKAGES = self::CACHE_KEY_PREFIX . 'packages.enabled';
    const CACHE_KEY_EVENTS_LISTENERS = self::CACHE_KEY_PREFIX . 'events.listeners';
    const CACHE_KEY_RAW_EVENTS_LISTENERS = self::CACHE_KEY_PREFIX . 'events.raw.listeners';

    const MODEL_RICH_MODE = 'MODEL_RICH_MODE';

    const ZENTO_PORTAL = 'ZENTO_PORTAL';

    const CACHE_KEY_DESKTOP_THEME = 'app.theme.desktop';
    const CACHE_KEY_MOBILE_THEME = 'app.theme.phone';
    const CACHE_KEY_THEME_BY = 'app.theme.%s';
}