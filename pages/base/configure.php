<?php
/**
 * -----------------------------------------------------------------------------
 * @package     smartVISU
 * @author      Martin Gleiß
 * @copyright   2012 - 2015
 * @license     GPL [http://www.gnu.de]
 * -----------------------------------------------------------------------------
 */


// get config-variables 
require_once '../../lib/includes.php';

// init parameters
$request = array_merge($_GET, $_POST);


$line = "<?php\n";
$line .= "/**\n";
$line .= "  * -----------------------------------------------------------------------------\n";
$line .= "  * @package     smartVISU\n";
$line .= "  * @author      Martin Gleiß\n";
$line .= "  * @copyright   2012 - 2015\n";
$line .= "  * @license     GPL [http://www.gnu.de]\n";
$line .= "  * -----------------------------------------------------------------------------\n";
$line .= "  */\n\n\n";

if($request['clear_cache']) {
	delTree(const_path.'temp/pagecache');
	$ret = array('title' => 'Configuration', 'text' => 'Pagecache cleared.');
	echo json_encode($ret);
	die();
}

touch(const_path.'config.php');

// is it writeable?
if (is_writeable(const_path.'config.php'))
{
	foreach ($request as $var => $val)
	{
		if($var == 'cache' && $val != 'true') {
			delTree(const_path.'temp/pagecache');
		}

		if (defined('config_'.$var))
		{
			if ($val == 'true' || $val == 'false')
				$line .= "\tdefine('config_".$var."', ".$val.");\n";
			else
				$line .= "\tdefine('config_".$var."', '".$val."');\n";
		}
	}

	$line .= "\n".'?'.'>';

	// write config
	filewrite('config.php', $line);
	
	opcache_invalidate(const_path.'config.php', true);
	
	$ret = array('title' => 'Configuration', 'text' => 'Configuration changes saved.');
	echo json_encode($ret);
}
else
{
	header("HTTP/1.0 600 smartVISU Config Error");

	$ret[] = array('title' => 'Configuration',
		'text' => 'Error saving configuration!<br />Please check the file permissions on "config.php" (it must be writeable)!');

	echo json_encode($ret);
}
?>