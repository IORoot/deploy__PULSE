<?php

namespace MatthiasWeb\RealMediaLibrary\lite\folder;

use MatthiasWeb\RealMediaLibrary\Core;
// @codeCoverageIgnoreStart
\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
// @codeCoverageIgnoreEnd
trait Creatable {
    // Documented in IFolderActions
    public function resetSubfolderOrder() {
        delete_media_folder_meta($this->id, 'lastSubOrderBy');
        $this->debug("Deleted subfolder order of the folder {$this->id}", __METHOD__);
        return \true;
    }
    // Documented in IFolderActions
    public function orderSubfolders($orderby, $writeMetadata = \true) {
        $orders = self::getAvailableSubfolderOrders();
        $core = \MatthiasWeb\RealMediaLibrary\Core::getInstance();
        $core->debug("Try to order the subfolders of {$this->id} by {$orderby}...", __METHOD__);
        if (\in_array($orderby, \array_keys($orders), \true)) {
            global $wpdb;
            // Get order
            $split = \explode('_', $orderby);
            $order = $orders[$orderby];
            $direction = $split[1];
            $table_name = $core->getTableName();
            // Run SQL
            // phpcs:disable WordPress.DB.PreparedSQL
            $sql = $wpdb->prepare(
                "UPDATE {$table_name} AS rmlo2\n                LEFT JOIN (\n                \tSELECT @rownum := @rownum + 1 AS ord, t.id\n                \tFROM ( SELECT rmlo.id\n                \t\tFROM {$table_name} AS rmlo\n                \t\tWHERE rmlo.parent = %d\n                \t\tORDER BY " .
                    $order['sqlOrder'] .
                    " {$direction} ) AS t, (SELECT @rownum := 0) AS r\n                ) AS rmlonew ON rmlo2.id = rmlonew.id\n                SET rmlo2.ord = rmlonew.ord\n                WHERE rmlo2.parent = %d",
                $this->id,
                $this->id
            );
            $wpdb->query($sql);
            // phpcs:enable WordPress.DB.PreparedSQL
            // Save in the metadata
            if ($writeMetadata) {
                update_media_folder_meta($this->id, 'lastSubOrderBy', $orderby);
            }
            $core->debug('Successfully ordered folder', __METHOD__);
            return \true;
        } else {
            $core->debug("'{$orderby}' is not a valid order...", __METHOD__);
            return \false;
        }
    }
    /**
     * Check if the current folders parent is automatically ordered by a criteria so
     * the order can be applied. This should be called when the hierarchy of the
     * folder is changed or when a new folder is added to that parent.
     *
     * @return boolean
     */
    protected function applySubfolderOrderBy() {
        $parent = wp_rml_get_object_by_id($this->getParent());
        if (!is_rml_folder($parent)) {
            return \false;
        }
        $orderAutomatically = (bool) $parent->getRowData('subOrderAutomatically');
        if ($orderAutomatically) {
            $order = $parent->getRowData('lastSubOrderBy');
            if (!empty($order)) {
                $this->debug(
                    'New subfolder ' .
                        $this->getId() .
                        ' in folder ' .
                        $parent->getId() .
                        ", automatically order by {$order} ...",
                    __METHOD__
                );
                return $parent->orderSubfolders($order, \false);
            }
        }
        return \false;
    }
    /**
     * Delete the subOrderAutomatically metadata when deleting the subfolder
     * order and also reset the subfolder order. It also handles the content order.
     *
     * @param int[] $meta_ids
     * @param int $object_id
     * @param string $meta_key
     */
    public static function deleted_realmedialibrary_meta($meta_ids, $object_id, $meta_key) {
        global $wpdb;
        if (empty($object_id)) {
            return;
        }
        if ($meta_key === 'lastSubOrderBy') {
            // The default is to order by ID ascending
            $folder = wp_rml_get_object_by_id($object_id);
            if (is_rml_folder($folder)) {
                $folder->orderSubfolders('id_asc', \false);
            }
            // phpcs:disable WordPress.DB.PreparedSQL
            $wpdb->query(
                $wpdb->prepare(
                    'UPDATE ' .
                        \MatthiasWeb\RealMediaLibrary\Core::getInstance()->getTableName() .
                        ' SET ord=NULL, oldCustomOrder=NULL WHERE id=%d',
                    $object_id
                )
            );
            // phpcs:enable WordPress.DB.PreparedSQL
            delete_media_folder_meta($object_id, 'subOrderAutomatically');
        }
        if ($meta_key === 'orderby') {
            // phpcs:disable WordPress.DB.PreparedSQL
            $wpdb->query(
                $wpdb->prepare(
                    'UPDATE ' .
                        \MatthiasWeb\RealMediaLibrary\Core::getInstance()->getTableName('posts') .
                        ' SET nr=NULL, oldCustomNr=NULL WHERE fid=%d',
                    $object_id
                )
            );
            $wpdb->query(
                $wpdb->prepare(
                    'UPDATE ' .
                        \MatthiasWeb\RealMediaLibrary\Core::getInstance()->getTableName() .
                        ' SET contentCustomOrder=0 WHERE id=%d',
                    $object_id
                )
            );
            // phpcs:enable WordPress.DB.PreparedSQL
            delete_media_folder_meta($object_id, 'orderAutomatically');
        }
    }
}
