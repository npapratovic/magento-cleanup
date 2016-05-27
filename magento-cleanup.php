<?php
/**
 * Magento db & files Maintenance Script
 *
 * @version    1.0.0
 * @author     Nikola Papratović <nikola.papratovic@gmail.com> 
 * @copyright  Copyright (c) 2016 Nikola Papratović 
 * @link       nikolapapratovic.iz.hr
 *
 * Instructions: place the following script inside the root direcotory of your magento
 *
 * In the Add Cron Job section (cpanel), select Once a day from the Common Settings dropdown list. 
 * In the Command field, enter the following line of code:
 *
 * curl -s -o /dev/null http://yourwebsite.com/magento-cleanup.php?clean=log
 * 
 * and for files cleaning:
 *
 * curl -s -o /dev/null http://yourwebsite.com/magento-cleanup.php?clean=var
 *
 * It's a good idea to set the Email Address to something other than your username, 
 * otherwise your mail/new directory will fill up very quickly every time a cron job runs 
 * (assuming it produces output). You can leave it blank or use an actual email address.
 */

switch($_GET['clean']) {
    case 'log':
        clean_log_tables();
    break;
    case 'var':
        clean_var_directory();
    break;
}

/*
Clean the database
*/

function clean_log_tables() {
    $xml = simplexml_load_file('./app/etc/local.xml', NULL, LIBXML_NOCDATA);
    
    if(is_object($xml)) {
        $db['host'] = $xml->global->resources->default_setup->connection->host;
        $db['name'] = $xml->global->resources->default_setup->connection->dbname;
        $db['user'] = $xml->global->resources->default_setup->connection->username;
        $db['pass'] = $xml->global->resources->default_setup->connection->password;
        $db['pref'] = $xml->global->resources->db->table_prefix;
        
        $tables = array(
            'aw_core_logger',
            'dataflow_batch_export',
            'dataflow_batch_import',
            'log_customer',
            'log_quote',
            'log_summary',
            'log_summary_type',
            'log_url',
            'log_url_info',
            'log_visitor',
            'log_visitor_info',
            'log_visitor_online',
            'index_event',
            'report_event',
            'report_viewed_product_index',
            'report_compared_product_index',
            'catalog_compare_item',
            'catalogindex_aggregation',
            'catalogindex_aggregation_tag',
            'catalogindex_aggregation_to_tag'
        );
        
        mysql_connect($db['host'], $db['user'], $db['pass']) or die(mysql_error());
        mysql_select_db($db['name']) or die(mysql_error());
        
        foreach($tables as $table) {
            @mysql_query('TRUNCATE `'.$db['pref'].$table.'`');
        }
        
        echo "Clean log: ok";

    } else {
        exit('Unable to load local.xml file');
    }
}

/**
 * Recursively removes a folder along with all its files and directories
 * 
 * @param String $dirPath 
 */

function deleteDirectory($dirPath) {
    if (is_dir($dirPath)) {
        $objects = scandir($dirPath);
        foreach ($objects as $object) {
            if ($object != "." && $object !="..") {
                if (filetype($dirPath . DIRECTORY_SEPARATOR . $object) == "dir") {
                    deleteDirectory($dirPath . DIRECTORY_SEPARATOR . $object);
                } else {
                    unlink($dirPath . DIRECTORY_SEPARATOR . $object);
                }
            }
        }
    reset($objects);
    rmdir($dirPath);
    }
}

function clean_var_directory() {
    $dirs = array(
        'media/import/',
        'var/report/',
        'var/cache/',
        'var/log/',
        'var/session/',
        'var/tmp/'
    );
    
    foreach($dirs as $dir) { 
        deleteDirectory($dir);
    }
    echo "Clean dir: ok";

    createDirectories();
}

function createDirectories() {
    $dirs = array(
        'media/import/',
        'var/report/',
        'var/cache/',
        'var/log/',
        'var/session/',
        'var/tmp/'
    );
    
    foreach($dirs as $dir) { 
        mkdir($dir);
    }
    echo "\n \n";
    echo "Created missing direcotries: ok";
}
