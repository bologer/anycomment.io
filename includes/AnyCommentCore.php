<?php

namespace AnyComment;

use AnyComment\Admin\AnyCommentGenericSettings;

use AnyComment\Base\AnyCommentBaseObject;
use AnyComment\Migrations\AnyCommentMigrationManager;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Stash\Driver\FileSystem;
use Stash\Pool;

/**
 * Main AnyComment Class.
 *
 */
class AnyCommentCore extends AnyCommentBaseObject
{
    /**
     * @var string AnyComment version.
     */
    public $version = '0.0.99';

    /**
     * @var Pool
     */
    protected static $cache;

    /**
     * @var Logger
     */
    protected static $log;

    /**
     * @var null|AnyCommentCore Instance of AnyComment.
     */
    private static $_instance = null;

    /**
     * Init method to invoke starting scripts.
     */
    public function init()
    {
        $this->includes();
        $this->init_textdomain();
        $this->init_hooks();
    }

    /**
     * Load locale.
     */
    public function init_textdomain()
    {
        load_plugin_textdomain("anycomment", false, basename(dirname(__FILE__)) . '/languages');
    }

    /**
     * Main AnyComment Instance.
     *
     * Ensures only one instance of AnyComment is loaded or can be loaded.
     *
     * @return AnyCommentCore Instance of plugin.
     * @see AnyComment()
     * @since 2.1
     * @static
     */
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();

            /**
             * Fires after AnyComment was loaded.
             *
             * @since 0.0.3
             */
            do_action( 'anycomment/loaded' );
        }

        return self::$_instance;
    }

    /**
     * Initiate hooks.
     */
    private function init_hooks()
    {
        register_activation_hook(__FILE__, [$this, 'activation']);
        register_uninstall_hook(__FILE__, sprintf('%s::uninstall', get_called_class()));

        add_action('init', function () {
            if (version_compare(AnyCommentOptions::get_migration(), $this->version, '<')) {
                (new AnyCommentMigrationManager())->apply_all();
            }
        });
    }

    /**
     * Activation method.
     */
    public function activation()
    {
        // Apply migrations
        (new AnyCommentMigrationManager())->apply_all();
    }

    /**
     * Uninstall method.
     */
    public static function uninstall()
    {
        remove_role(AnyCommentGenericSettings::DEFAULT_ROLE_SOCIAL_SUBSCRIBER);

        // Clean directory with files
        $dir_name = AnyCommentUploadHandler::get_save_dir();

        if (is_dir($dir_name)) {
            @rmdir($dir_name);
        }

        // Drop all migrations
        (new AnyCommentMigrationManager())->drop_all();
    }

    /**
     * Get the plugin url.
     * @return string
     */
    public function plugin_url()
    {
        return untrailingslashit(plugins_url('/', ANYCOMMENT_PLUGIN_FILE));
    }

    /**
     * Get the plugin path.
     * @return string
     */
    public function plugin_path()
    {
        return untrailingslashit(plugin_dir_path(ANYCOMMENT_PLUGIN_FILE));
    }

    /**
     * Include required core files used in admin and on the frontend.
     */
    public function includes()
    {
        AnyCommentLoader::load();
    }

    /**
     * Get instance of cache.
     *
     * @return Pool
     */
    public static function cache()
    {
        if (static::$cache == null) {
            $cache_path = ABSPATH . str_replace('/', DIRECTORY_SEPARATOR, 'wp-content/cache/anycomment');

            if ( ! @file_exists($cache_path)) {
                @mkdir($cache_path, 0755, true);
            }

            $cacheDriver = new FileSystem([
                'path' => $cache_path,
            ]);

            static::$cache = new Pool($cacheDriver);
        }

        return static::$cache;
    }

    /**
     * Trying to build log instance or returns existing one.
     *
     * @return Logger
     */
    public static function logger()
    {
        if (static::$log === null) {

            try {
                $log_path = ABSPATH . str_replace('/', DIRECTORY_SEPARATOR, 'wp-content/uploads/anycomment/logs');

                if (@is_dir($log_path)) {
                    @mkdir($log_path, '0755', true);
                }

                // Create .htaccess and index.html to hide direct access to log data
                $htaccess_path = $log_path . DIRECTORY_SEPARATOR . '.htaccess';
                $index_path    = $log_path . DIRECTORY_SEPARATOR . 'index.html';

                if ( ! @file_exists($htaccess_path)) {
                    @file_put_contents($htaccess_path, 'deny from all');
                }

                if ( ! @file_exists($index_path)) {
                    @file_put_contents($index_path, '');
                }

                $log_file = $log_path . DIRECTORY_SEPARATOR . 'debug.log';

                // create a log channel
                $log = new Logger('anycomment');
                $log->pushHandler(new StreamHandler($log_file, Logger::WARNING));

                static::$log = $log;
            } catch (\Exception $exception) {
            }
        }

        return static::$log;
    }
}
