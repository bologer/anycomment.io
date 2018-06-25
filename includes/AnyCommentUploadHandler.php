<?php

namespace AnyComment\Handlers;

/**
 * Class AnyCommentUploadHandler is used to handle avatar upload for social medias
 * as some of the give access to the avatar for the period of access token.
 *
 * @since 0.0.2
 * @package AnyComment\Handlers
 */
class AnyCommentUploadHandler
{
    /**
     * Upload specified image.
     * @param string $profileUrl Profile URL used to upload image and return local URL to it.
     * @param array $metaIdentifier Some list of params to be used as unique identifier of the avatar.
     * @link https://wordpress.stackexchange.com/a/251512
     * @return bool
     */
    public static function upload($profileUrl, $metaIdentifier)
    {
        $profileUrl = trim($profileUrl);

        if (empty($profileUrl)) {
            return false;
        }

        $timeout_seconds = 5;

        require_once(ABSPATH . 'wp-admin/includes/file.php');

        // Download file to temp dir
        $temp_file = download_url($profileUrl, $timeout_seconds);

        if (is_wp_error($temp_file)) {
            return false;
        }

        // Array based on $_FILE as seen in PHP file uploads
        $file = array(
            'name' => static::getFileName($metaIdentifier),
            'type' => 'image/jpg',
            'tmp_name' => $temp_file,
            'error' => 0,
            'size' => filesize($temp_file),
        );

        $overrides = [
            // Tells WordPress to not look for the POST form
            // fields that would normally be present as
            // we downloaded the file from a remote server, so there
            // will be no form fields
            // Default is true
            'test_form' => false,

            // Setting this to false lets WordPress allow empty files, not recommended
            // Default is true
            'test_size' => true,
        ];

        // Move the temporary file into the uploads directory
        $results = wp_handle_sideload($file, $overrides);

        if (!empty($results['error'])) {
            return false;
        }

        $fileName = $results['file']; // Full path to the file
        $localUrl = $results['url'];  // URL to the file in the uploads dir
        $type = $results['type']; // MIME type of the file

        // Perform any actions here based in the above results
        return $localUrl;
    }


    /**
     * Generate file name based on passed meta data.
     * File name could not be 100% in this method.
     * This method is considered to be used together with wp_handle_sideload(),
     * which is responsible for uniquness and postfix file when non-unique.
     *
     * @param array $meta Some meta data to be used to generate the hash.
     * @return false|string False on failure.
     */
    public static function getFileName($meta)
    {
        if (empty($meta)) {
            return false;
        }

        $meta[] = uniqid(time());
        $fileName = md5(serialize($meta));

        return sprintf('%s.jpg', $fileName);
    }
}