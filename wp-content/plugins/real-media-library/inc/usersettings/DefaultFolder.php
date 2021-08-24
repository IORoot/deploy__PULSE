<?php

namespace MatthiasWeb\RealMediaLibrary\usersettings;

use MatthiasWeb\RealMediaLibrary\api\IUserSettings;
use MatthiasWeb\RealMediaLibrary\base\UtilsProvider;
use WP_Query;
// @codeCoverageIgnoreStart
\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
// @codeCoverageIgnoreEnd
/**
 * Add an option so the user can set the default startup folder.
 * It replaces this plugin: https://wordpress.org/plugins/default-startup-folder-for-real-media-library/
 *
 * @since 4.6.0
 */
class DefaultFolder implements \MatthiasWeb\RealMediaLibrary\api\IUserSettings {
    use CommonUserSettingsTrait;
    use UtilsProvider;
    const FIELD_NAME = 'defaultFolder';
    const OPTION_NAME = 'rmlDefaultFolder';
    const ID_NONE = -2;
    const ID_LAST_QUERIED = -3;
    /**
     * C'tor.
     */
    public function __construct() {
        add_filter('RML/Localize', [$this, 'localize']);
        add_filter('RML/Filter/PostsClauses', [$this, 'posts_clauses'], 10, 4);
        if (isset($_GET['rml_folder']) && \intval($_GET['rml_folder']) === self::ID_NONE) {
            add_filter('gettext', [$this, 'gettext'], 10, 3);
        }
    }
    /**
     * Modify text in list table.
     *
     * @param string[] $translation
     * @param string $text
     * @param string $domain
     * @return string
     */
    public function gettext($translation, $text, $domain) {
        return $text === 'No media files found.' && $domain === 'default'
            ? __('Please select a folder to show items.', RML_TD)
            : $translation;
    }
    /**
     * Modify fields to a fast select query when the setting "None at startup" is configured.
     *
     * @param array $clauses
     * @param WP_Query $query
     * @param int $folderId
     * @param int $root
     * @return array
     */
    public function posts_clauses($clauses, $query, $folderId, $root) {
        if ($folderId === self::ID_NONE) {
            $clauses['where'] = ' AND 1=0';
            $clauses['limits'] = 'LIMIT 0, 1';
            $clauses['_restrictRML'] = \true;
        }
        return $clauses;
    }
    /**
     * Localize frontend.
     *
     * @param array $arr
     * @return array
     */
    public function localize($arr) {
        $arr['defaultFolder'] = self::getDefaultFolder();
        return $arr;
    }
    // Documented in IMetadata
    public function content($content, $user) {
        $default = $this->getDefaultFolder();
        $selectedNone = $default === self::ID_NONE ? 'selected="selected"' : '';
        $selectedLastQueried = $default === self::ID_LAST_QUERIED ? 'selected="selected"' : '';
        $content .=
            '<label>' .
            __('Default startup folder', RML_TD) .
            '</label><select name="' .
            self::FIELD_NAME .
            '"><option value="' .
            self::ID_NONE .
            '" ' .
            $selectedNone .
            '>' .
            __('No folder at startup', RML_TD) .
            '</option><option value="' .
            self::ID_LAST_QUERIED .
            '" ' .
            $selectedLastQueried .
            '>' .
            __('Last opened folder', RML_TD) .
            '</option>' .
            wp_rml_dropdown($default, []) .
            '</select>';
        return $content;
    }
    // Documented in IMetadata
    public function save($response, $user, $request) {
        self::getDefaultFolder($request->get_param(self::FIELD_NAME));
        $response['data']['lastQueried'] = wp_rml_last_queried_folder();
        return $response;
    }
    // Documented in IMetadata
    public function scripts($assets) {
        // Silence is golden.
    }
    // Documented in CommonUserSettingsTrait
    public static function getDefaultFolder($persist = null) {
        $default = self::get(self::OPTION_NAME, $persist);
        return empty($default) ? '' : \intval($default);
    }
}
