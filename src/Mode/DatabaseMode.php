<?php

namespace Tanwencn\Supervisor\Mode;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;

class DatabaseMode
{
    protected $table;

    protected $primaryKey;

    protected $order;

    protected $config;

    protected $line = 0;

    protected $offset = 0;

    /**
     * sql connection
     *
     * @var \Illuminate\Database\ConnectionInterface
     */
    protected $connection;

    public function __construct(string $group, array $config)
    {
        $this->table = $config['table'];
        $this->where = data_get($config, "group.{$group}", []);
        $this->primaryKey = $config['primaryKey'];
        $this->order = $config['order'];
        
        $this->config = $config;
    }

    protected function connection(){
        $db = DB::connection($this->config['connection'])->table($this->table);
        if ($this->config['order']) {
            $db->orderBy($this->primaryKey, $this->order);
        }
        if ($this->where) {
            foreach($this->where as $where){
                $db->where(...$where);
            }
            $db->orderBy($this->primaryKey, $this->order);
        }
        
        return $db;
    }

    public function next(): array
    {
        $connection = $this->connection();
        if($this->offset) $connection->where('id', $this->order == 'desc' ? '<' : '>', $this->offset);
        $first = (array)$connection->first();
        if(empty($first)) return $first;

        $this->line++;
        if($first) $this->offset = $first[$this->primaryKey];
        return array_merge(['supervisorid' => $this->line], $first);
    }
}
