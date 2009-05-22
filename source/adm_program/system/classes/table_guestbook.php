<?php
/******************************************************************************
 * Klasse fuer Datenbanktabelle adm_guestbook
 *
 * Copyright    : (c) 2004 - 2009 The Admidio Team
 * Homepage     : http://www.admidio.org
 * Module-Owner : Markus Fassbender
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Diese Klasse dient dazu ein Gaestebucheintragsobjekt zu erstellen. 
 * Eine Gaestebucheintrag kann ueber diese Klasse in der Datenbank verwaltet werden
 *
 * Neben den Methoden der Elternklasse TableAccess, stehen noch zusaetzlich
 * folgende Methoden zur Verfuegung:
 *
 * getText($type = 'HTML') - liefert den Text je nach Type zurueck
 *          type = 'PLAIN'  : reiner Text ohne Html oder BBCode
 *          type = 'HTML'   : BB-Code in HTML umgewandelt
 *          type = 'BBCODE' : Text mit BBCode-Tags
 *
 *****************************************************************************/

require_once(SERVER_PATH. '/adm_program/system/classes/table_access.php');
require_once(SERVER_PATH. '/adm_program/system/classes/ubb_parser.php');

class TableGuestbook extends TableAccess
{
    var $bbCode;

    // Konstruktor
    function TableGuestbook(&$db, $gbo_id = 0)
    {
        $this->db            =& $db;
        $this->table_name     = TBL_GUESTBOOK;
        $this->column_praefix = 'gbo';
        
        if($gbo_id > 0)
        {
            $this->readData($gbo_id);
        }
        else
        {
            $this->clear();
        }
    }
    
    // prueft die Gueltigkeit der uebergebenen Werte und nimmt ggf. Anpassungen vor
    function setValue($field_name, $field_value)
    {
        if(strlen($field_value) > 0)
        {
            if($field_name == 'gbo_homepage')
            {
                // Die Webadresse wird jetzt, falls sie nicht mit http:// oder https:// beginnt, entsprechend aufbereitet
                if (substr($field_value, 0, 7) != 'http://' && substr($field_value, 0, 8) != 'https://' )
                {
                    $field_value = 'http://'. $field_value;
                }
            }
            elseif($field_name == 'gbo_email')
            {
                if (!isValidEmailAddress($field_value))
                {
                    // falls die Email ein ungueltiges Format aufweist wird sie einfach auf null gesetzt
                    $field_value = '';
                }
            }
        }
        parent::setValue($field_name, $field_value);
    }

    // liefert den Text je nach Type zurueck
    // type = 'PLAIN'  : reiner Text ohne Html oder BBCode
    // type = 'HTML'   : BB-Code in HTML umgewandelt
    // type = 'BBCODE' : Text mit BBCode-Tags
    function getText($type = 'HTML')
    {
        global $g_preferences;
        $description = '';

        // wenn BBCode aktiviert ist, den Text noch parsen, ansonsten direkt ausgeben
        if($g_preferences['enable_bbcode'] == 1 && $type != 'BBCODE')
        {
            if(is_object($this->bbCode) == false)
            {
                $this->bbCode = new ubbParser();
            }

            $description = $this->bbCode->parse($this->getValue('gbo_text'));

            if($type == 'PLAIN')
            {
                $description = strStripTags($description);
            }
        }
        else
        {
            $description = nl2br($this->getValue('gbo_text'));
        }
        return $description;
    }

    // Methode, die Defaultdaten fur Insert und Update vorbelegt
    function save()
    {
        global $g_current_organization, $g_current_user;
        
        if($this->new_record)
        {
            $this->setValue('gbo_timestamp', DATETIME_NOW);
            $this->setValue('gbo_usr_id', $g_current_user->getValue('usr_id'));
            $this->setValue('gbo_org_id', $g_current_organization->getValue('org_id'));
            $this->setValue('gbo_ip_address', $_SERVER['REMOTE_ADDR']);
        }
        else
        {
            // Daten nicht aktualisieren, wenn derselbe User dies innerhalb von 15 Minuten gemacht hat
            if(time() > (strtotime($this->getValue('gbo_timestamp')) + 900)
            || $g_current_user->getValue('usr_id') != $this->getValue('gbo_usr_id') )
            {
                $this->setValue('gbo_timestamp_change', DATETIME_NOW);
                $this->setValue('gbo_usr_id_change', $g_current_user->getValue('usr_id'));
            }
        }
        parent::save();
    }
    
    // die Methode loescht den Gaestebucheintrag mit allen zugehoerigen Kommentaren
    function delete()
    {
        //erst einmal alle vorhanden Kommentare zu diesem Gaestebucheintrag loeschen...
        $sql = 'DELETE FROM '. TBL_GUESTBOOK_COMMENTS. ' WHERE gbc_gbo_id = '. $this->getValue('gbo_id');
        $result = $this->db->query($sql);
        
        return parent::delete();
    }    
}
?>