#!/usr/bin/php
<?php
$arguments = $_SERVER['argv'];
$count = $_SERVER['argc'];
$script = array_shift($arguments);


if (empty($arguments)) {
	error_log("\e[31m[Erreur]\e[0m - Vous devez définir le nom de la table pour exporter les datasets!", 4);
	exit(1);
}

$table =  current($arguments);

$user = 'root';
$passwd ='x55dsx-mysql';
$base = 'reservoteich';

$dsn = sprintf('mysql:dbname=%s;host=localhost', $base);
try {
    $db = new PDO($dsn, $user, $passwd, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $result = $db->query("SELECT * FROM `$table`");
    $datas = $result->fetchAll(PDO::FETCH_ASSOC);
    $dom = new DOMDocument('1.0', 'utf-8');
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dataset = $dom->createElement('dataset');
    $dom->appendChild($dataset);
    foreach ($datas as $field) {
    	$node = $dom->createElement($table);
    	foreach ($field as $column => $value) {
    		$node->setAttribute($column, $value);
    	}
    	$dataset->appendChild($node);
    }

    $xml = $dom->saveXML();     
    foreach (array('dataset', 'select', 'insert', 'update', 'delete') as $file) {
    	file_put_contents($file . '.xml', $xml);
    }
    echo "Fichiers xml crée - \e[32m[Ok]\e[0m";  
}  catch (PDOException $e) {
    echo 'Connexion error : ' . $e->getMessage();
}
echo PHP_EOL;
exit;