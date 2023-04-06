<?php

/**
 * -------------------------------------------------------------------------
 * Mreporting plugin for GLPI
 * -------------------------------------------------------------------------
 *
 * LICENSE
 *
 * This file is part of Mreporting.
 *
 * Mreporting is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Mreporting is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Mreporting. If not, see <http://www.gnu.org/licenses/>.
 * -------------------------------------------------------------------------
 * @copyright Copyright (C) 2003-2022 by Mreporting plugin team.
 * @license   GPLv2 https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/pluginsGLPI/mreporting
 * -------------------------------------------------------------------------
 */

class PluginMreportingTag Extends PluginMreportingBaseclass {

   /**
    * Default pie graph for the use of tags.
    * For all linked itemtypes without filter.
    *
    * @param array   $config (optionnal)
    * @return array  $datas array of query results (tag => count number)
    */
   function reportPieTag($config = []) {
      global $DB;

      if (!Plugin::isPluginActive('tag')) {
         return [];
      }

      $_SESSION['mreporting_selector'][__FUNCTION__] = [];

      $datas = [];

      $result = $DB->query("SELECT COUNT(*) as count_tag, glpi_plugin_tag_tags.name as name
                     FROM glpi_plugin_tag_tagitems
                     LEFT JOIN glpi_plugin_tag_tags ON plugin_tag_tags_id = glpi_plugin_tag_tags.id
                     GROUP BY plugin_tag_tags_id
                     ORDER BY count_tag DESC");
      while ($datas_tag = $DB->fetchAssoc($result)) {
         $label = $datas_tag['name'];
         $datas['datas'][$label] = $datas_tag['count_tag'];
      }

      return $datas;
   }

   /**
    * Pie graph for the use of tags in Ticket,
    * with itilcategory filter.
    *
    * @param array   $config (optionnal)
    * @return array  $datas array of query results (tag => count number)
    */
   function reportPieTagOnTicket($config = []) {
      global $DB;

      if (!Plugin::isPluginActive('tag')) {
         return [];
      }

      $_SESSION['mreporting_selector'][__FUNCTION__] = ['category'];

      $sql_itilcat = isset($_SESSION['mreporting_values']['itilcategories_id']) && $_SESSION['mreporting_values']['itilcategories_id'] > 0 ?
                     " AND glpi_tickets.itilcategories_id = ".$_SESSION['mreporting_values']['itilcategories_id'] : "";

      $datas = [];

      $result = $DB->query("SELECT COUNT(*) as count_tag, glpi_plugin_tag_tags.name
                           FROM glpi_plugin_tag_tagitems
                           LEFT JOIN glpi_plugin_tag_tags ON plugin_tag_tags_id = glpi_plugin_tag_tags.id
                           LEFT JOIN glpi_tickets ON glpi_tickets.id = glpi_plugin_tag_tagitems.items_id
                           WHERE itemtype = 'Ticket'
                           $sql_itilcat
                           GROUP BY plugin_tag_tags_id
                           ORDER BY count_tag DESC");
      while ($datas_tag = $DB->fetchAssoc($result)) {
         $label = $datas_tag['name'];
         $datas['datas'][$label] = $datas_tag['count_tag'];
      }

      return $datas;
   }

}