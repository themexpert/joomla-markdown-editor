<?php
/**
 * @package      jme
 *
 * @author       ThemeXpert
 * @copyright    Copyright (C) 2012-2013. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl.html GNU/GPL, see LICENSE.txt
 */

defined('_JEXEC') or die();


class pkg_JmeInstallerScript
{
    /**
     * Called before any type of action
     *
     * @param     string              $route      Which action is happening (install|uninstall|discover_install)
     * @param     jadapterinstance    $adapter    The object responsible for running this script
     *
     * @return    boolean                         True on success
     */
    public function preflight($route, JAdapterInstance $adapter)
    {
        return true;
    }


    /**
     * Called after any type of action
     *
     * @param     string              $route      Which action is happening (install|uninstall|discover_install)
     * @param     jadapterinstance    $adapter    The object responsible for running this script
     *
     * @return    boolean                         True on success
     */
    public function postflight($route, JAdapterInstance $adapter)
    {
        self::enablePlugins();
        return true;
    }

    /**
    * enable necessary plugins to avoid bad experience
    */
    function enablePlugins()
    {

        $db = JFactory::getDBO();
        $sql = "SELECT `element`,`folder` from `#__extensions` WHERE `type` = 'plugin' AND `folder` in ('content', 'editors') AND `name` like '%jme%' AND `enabled` = '0'";
        $db->setQuery($sql);

        $plugins = $db->loadObjectList();
        if(!count($plugins)) return false;
        foreach ($plugins as $key => $value) 
        {
            $query = $db->getQuery(true);
            $query->update($db->quoteName('#__extensions'));
            $query->set($db->quoteName('enabled') . ' = '.$db->quote('1'));
            $query->where($db->quoteName('type') . ' = '.$db->quote('plugin'));
            $query->where($db->quoteName('element') . ' = '.$db->quote($value->element));
            $query->where($db->quoteName('folder') . ' = '.$db->quote($value->folder));
            $db->setQuery($query);
            $db->execute();    
        }
        
        return true;

    }
}
