<?php

// Forked from https://gist.github.com/1809044
// Available from https://gist.github.com/nichtich/5290675#file-deploy-php

$allowed = true;

if (!$allowed) {
	header('HTTP/1.1 403 Forbidden');
 	echo "<span style=\"color: #ff0000\">Sorry, no hamster - better convince your parents!</span>\n";
    exit;
}

flush();

// Actually run the update

$commands = array(
	'cd /www/sothis.local/',
	'whoami',
	'git pull origin main',
	'git status',
	'git submodule sync',
	'git submodule update',
	'git submodule status',
    'php /www/sothis.local/bin/console doctrine:schema:update --force',
    'php /www/sothis.local/bin/console cache:clean',
    'php /www/sothis.local/bin/console cache:warmup',
);

$output = "\n";

$log = "####### ".date('Y-m-d H:i:s'). " #######\n";

foreach($commands AS $command){
    // Run it
    $tmp = shell_exec("$command 2>&1");
    // Output
    $output .= "<span style=\"color: #6BE234;\">\$</span> <span style=\"color: #729FCF;\">{$command}\n</span>";
    $output .= htmlentities(trim($tmp)) . "\n";

    $log  .= "\$ $command\n".trim($tmp)."\n";
}

$log .= "\n";

file_put_contents ('/www/sothis.local/var/log/deploy.php',$log,FILE_APPEND);

echo $output; 

?>
</pre>
</body>
</html>