<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 21/05/18
 * Time: 05:36 م
 */

require_once __DIR__."/vendor/autoload.php";

use DebugBar\DataCollector\AssetProvider;
use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;

class WordpressDatabaseCollector extends DataCollector implements Renderable, AssetProvider
{
    protected $wpdb;

    public function __construct($wpdb)
    {
        $this->wpdb = $wpdb;
    }

    public function collect()
    {
        $queries = array();
        $totalExecTime = 0;
        foreach ($this->wpdb->queries as $q) {
            list($query, $duration, $caller) = $q;
            $queries[] = array(
                'sql' => $query,
                'duration' => $duration,
                'duration_str' => $this->formatDuration($duration)
            );
            $totalExecTime += $duration;
        }

        return array(
            'nb_statements' => count($queries),
            'accumulated_duration' => $totalExecTime,
            'accumulated_duration_str' => $this->formatDuration($totalExecTime),
            'statements' => $queries
        );
    }

    public function getName()
    {
        return 'wpdb';
    }

    public function getWidgets()
    {
        return array(
            "database" => array(
                "icon" => "arrow-right",
                "widget" => "PhpDebugBar.Widgets.SQLQueriesWidget",
                "map" => "wpdb",
                "default" => "[]"
            ),
            "database:badge" => array(
                "map" => "wpdb.nb_statements",
                "default" => 0
            )
        );
    }

    public function getAssets()
    {
        return array(
            'css' => 'widgets/sqlqueries/widget.css',
            'js' => 'widgets/sqlqueries/widget.js'
        );
    }
}