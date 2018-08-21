<?php
//Disallow direct Initialization for extra security.

if(!defined("IN_MYBB"))
{
    die("You Cannot Access This File Directly. Please Make Sure IN_MYBB Is Defined.");
}

// Hooks
$plugins->add_hook('global_start', 'guestwelcome_global_start');

// Information
function guestwelcome_info()
{
return array(
        "name"  => "Guest Welcome",
        "description"=> "Displays welcome message to guest",
        "website"        => "https://oseax.com",
        "author"        => "Wires <i>(AndreRl)</i>",
        "authorsite"    => "https://oseax.com",
        "version"        => "2.0",
        "guid"             => "",
        "compatibility" => "18*"
    );
}

// Activate
function guestwelcome_activate() {
global $db;

$setting_group = array(
    'name' => 'guestwelcome',
    'title' => 'Guest Welcome',
    'description' => 'Settins For Guest Welcome',
    'disporder' => 5,
    'isdefault' => 0
);

$gid = $db->insert_query("settinggroups", $setting_group);

$guestwelcome_group = array(
        'gid'    => 'NULL',
        'name'  => 'guestwelcome',
        'title'      => 'Guest Welcome',
        'description'    => 'Settings For Guest Welcome',
        'disporder'    => "1",
        'isdefault'  => "0",
    );
$db->insert_query('settinggroups', $guestwelcome_group);
 $gid = $db->insert_id();
 
$setting_array = array(
    // Enable
    'guestwelcome_enable' => array(
        'title' => 'Enable Guest Welcome',
        'description' => 'If you set this option to yes, this plugin be active on your board.',
        'optionscode' => 'yesno',
        'value' => 1,
        'disporder' => 1
    ),
    // Message
    'guestwelcome_message' => array(
        'title' => 'Guest Welcome Message',
        'description' => 'Message you want the guests to see.',
        'optionscode' => "textarea",
        'value' => 'Hello guest, if you are reading this it means you are not registered to the forum. Click <a href="member.php?action=register" style="color: red;">here</a> to register in a few simple steps, and enjoy all the features of our forum. ',
        'disporder' => 2
    ),
    // Group Select
    'guestwelcome_groupcontrol' => array(
        'title' => 'Select Group',
        'description' => 'Group to display message to. By default, guests.',
        'optionscode' => 'groupselect',
        'value' => '1',
        'disporder' => 3
    ),
);

foreach($setting_array as $name => $setting)
{
    $setting['name'] = $name;
    $setting['gid'] = $gid;

    $db->insert_query('settings', $setting);
}

	require_once MYBB_ROOT."/inc/adminfunctions_templates.php";
	find_replace_templatesets("index",'#'.preg_quote('{$header}').'#','{$header}{$guestwelcome}');
	find_replace_templatesets("headerinclude",'#'.preg_quote('{$stylesheets}').'#','{$stylesheets}
	<script type="text/javascript" src="{$mybb->settings[\'bburl\']}/jscripts/guestwelcome.js"></script>');
  rebuild_settings();
}

// Deactivate
function guestwelcome_deactivate()
  {
  global $db;
 $db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name IN ('guestwelcome_enable', 'guestwelcome_message', 'guestwelcome_groupcontrol')");
    $db->query("DELETE FROM ".TABLE_PREFIX."settinggroups WHERE name='guestwelcome'");
	require "../inc/adminfunctions_templates.php";
	find_replace_templatesets("index", '#'.preg_quote('{$guestwelcome}').'#', '',0);
	find_replace_templatesets("headerinclude", '#'.preg_quote('<script type="text/javascript" src="{$mybb->settings[\'bburl\']}/jscripts/guestwelcome.js"></script>').'#', '',0);
rebuild_settings();
 }

function guestwelcome_global_start(){
global $mybb, $db, $guestwelcome;

if($mybb->settings['guestwelcome_enable'] != 1)
{
	return;
}
	// explode...
$groups_allowed = explode(',', $mybb->settings['guestwelcome_groupcontrol']);

if ($mybb->settings['guestwelcome_groupcontrol'] == '-1' || in_array($mybb->user['usergroup'], $groups_allowed)){

$guestwelcometext = $mybb->settings['guestwelcome_message'];
 $guestwelcome = "<div class=\"guestwelcome\" style=\"display: none;\">
     <span id=\"closeButton\" style=\"float: right; cursor: pointer;\"> [Close]</span>
	 $guestwelcometext </div>";
}

} 
?>