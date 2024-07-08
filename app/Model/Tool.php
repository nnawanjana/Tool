<?php
App::uses('AppModel', 'Model');

class Tool extends AppModel {
    public $useTable = false;
    
    public function exportDatabase() {
        App::uses('ConnectionManager', 'Model');
        $dataSource = ConnectionManager::getDataSource('default');
        $database = $dataSource->config['database'];

        $content  = "--\n";

        $tables = $this->query('show tables');
        foreach($tables as $k => $v){
            foreach($v['TABLE_NAMES'] as $table) {
                if (in_array($table, array('submissions','customers', 'users'))) {
                    continue;
                }
                $structures = $this->query("show create table `".$table."`");
                foreach($structures as $s) {
                    $content .= $s[0]['Create Table'].";\n\n";
                    $entries = $this->query("SELECT * FROM `".$table."`");
                    foreach($entries as $entry) {
                        $content .= "INSERT INTO ".$table." (";
                        $i = 0;
                        foreach($entry[$table] as $entryKey => $vv) {
                            $entryKey = addslashes($entryKey);
                            $content .= "`".$entryKey."`";
                            $i++;
                            if($i < count($entry[$table]))
                                $content .=", ";
                        }
                        $content .= ") VALUES (";
                        $j = 0;
                        foreach($entry[$table] as $ee => $entryValue) {
                            $entryValue = addslashes($entryValue);
                            debug($entryValue);
                            $content .= "'".$entryValue."'";
                            $j++;
                            if($j < count($entry[$table]))
                                $content .=", ";
                        }
                        $content .= ");\n";
                    }
                    $content .= "\n\n";
                    $content .= "-- --------------------------------------------------------\n\n\n";

                }
            }
        }

        App::uses('File', 'Utility');

        $filename = "sql_dump".DS .$database. "_" .date("d-m-Y_H-i-s"). ".sql";

        $file = new File($filename, true);

        $d['file'] = $filename;
        $d['database'] = $database;

        if($file->append($content, true)) {
            return $d;
        } else {
            return false;
        }
    }
}