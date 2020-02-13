<?php

class DB
{

    public $count = 0;

    private $SQL;

    private $prefix;

    private $config = [];

    private $error;

    /**
     * DB 构造函数.
     *
     * @param $host     string 主机名
     * @param $user     string 用户名
     * @param $password string 密码
     * @param $db       string 数据库名
     * @param $prefix   string 前缀
     */
    public function __construct(
        $host,
        $user,
        $password,
        $db,
        $prefix,
        $port = 3306
    )
    {
        $this->config = [
            'host' => $host,
            'user' => $user,
            'password' => $password,
            'db' => $db,
        ];
        $this->prefix = $prefix;
        try {
            $link = new mysqli($host, $user, $password, $db, $port);
        } catch (Exception $exception) {
            $this->error = [
                'error' => true,
                'msg' => $this->SQL->connect_error,
                'en' => $this->SQL->connect_errno,
            ];
            ErrorHandle::Error(
                500,
                "数据库连接错误",
                'MySQL Connect',
                '-50',
                ['dberr' => $this->SQL->connect_error]
            );
            echo '数据库无法连接';
            exit(502);
        }

        if ($link->connect_error) {
            $this->error = [
                'error' => true,
                'msg' => $this->SQL->connect_error,
                'en' => $this->SQL->connect_errno,
            ];
            ErrorHandle::Error(
                500,
                '数据库连接错误',
                'MySQL Connect',
                '-50',
                ['dberr' => $this->SQL->connect_error]
            );
            exit(502);
        } else {
            $this->SQL = $link;
        }
        $this->Query("set names utf8mb4;");
    }

    /**
     * 进行数据库查询
     *
     * @param string $sqll MySQL语句
     *
     * @return mysqli_result MySQL查询结果
     */
    public function Query($sqll)
    {
        $this->count++;

        //echo $sqll;
        return $this->SQL->query($sqll);
    }

    /**
     * DB 解构函数
     * 用来销毁链接
     * 请注意,销毁后无法再次链接
     */
    public function __destruct()
    {
        if ($this->SQL) {
            $this->SQL->close();
        }
    }

    /**
     * 删除数据Delete
     *
     * @param string $table 表名
     * @param array $where where条件
     */
    public function Delete($table, $where)
    {
        $w = "";
        if (count($where)) {
            $w = ' WHERE ';
            $w .= $this->ArrayToWhere($where);
        }
        //DELETE FROM `mengcy_com`.`mcy_follow_relation` WHERE `follower` = 1 AND `followwho` = 1 LIMIT 1
        $sqll = 'DELETE FROM `' . $this->prefix . $table . '` ' . $w . ';';
        $this->Query($sqll);
    }

    /**
     * 将数组变换为Where表达式
     *
     * @param array $where
     *
     * @return string
     */
    private function ArrayToWhere($where)
    {
        $temp = "";
        if (count($where)) {
            if (array_keys($where) === range(0, count($where) - 1)) {
                //使用方法1
                $first = true;
                foreach ($where as $item) {
                    if (!isset($item['c'])) {
                        $item['c'] = '=';
                    }
                    if (!$first) {
                        $temp .= ' and `' . $item['p'] . '` ' . $item['c'] . ' '
                            . $this->check_input($item['v']);
                    } else {
                        $temp .= ' `' . $item['p'] . '` ' . $item['c'] . ' '
                            . $this->check_input($item['v']);
                    }
                    $first = false;
                }
            } else {
                $first = true;
                foreach ($where as $k => $v) {
                    if (!$first) {
                        $temp .= ' and `' . $k . '` = ' . $this->check_input(
                                $v
                            );
                    } else {
                        $temp .= ' `' . $k . '` = ' . $this->check_input($v);
                    }
                    $first = false;
                }
            }
        }

        return $temp;
    }

    /**
     * @param string $value 用户输入的
     *
     * @return string 安全的
     */
    private function check_input($value)
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }
        if (get_magic_quotes_gpc()) {
            $value = stripslashes($value);
        }
        if (!is_numeric($value)/* && !empty($value)*/) {
            $value = "'" . mysqli_real_escape_string($this->SQL, $value) . "'";
        }

        return $value;
    }

    /**
     * 返回Select后的数组
     *
     * @param string $table 表名
     * @param array $where where条件
     * @param array $place 取出哪些
     * @param string $other 其他的语句
     *
     * @return array
     */
    public function SelectDataArray(
        $table,
        $where = [],
        $place = [],
        $other = ""
    )
    {
        $r = $this->SelectData($table, $where, $place, $other);
        if ($r) {
            return $this->FetchResult($r, MYSQLI_BOTH);
        } else {
            return [];
        }
    }

    /**
     * 选择数据Select
     *
     * @param string $table 表名
     * @param array $where where条件
     * @param array $place 取出哪些
     * @param string $other 其他的语句
     *
     * @return mysqli_result 返回mysql_result结果,自行转换
     */
    public function SelectData(
        $table,
        $where = [],
        $place = [],
        $other = ""
    )
    {
        if (count($where)) {
            $w = ' WHERE ';
            $temp = $this->ArrayToWhere($where);
        } else {
            $w = '';
            $temp = '';
        }

        $p = '';
        if (!count($place)) {
            $p = ' * ';
        } else {
            $first = true;
            foreach ($place as $value) {
                if ($first) {
                    $p .= ' `' . $value . '`';
                } else {
                    $p .= ',`' . $value . '`';
                }
                $first = false;
            }
        }

        $sqll = "SELECT" . $p . ' FROM `' . $this->prefix . $table . '` ' . $w
            . $temp . $other . ';';

        return $this->Query($sqll);
    }

    /**
     * MySQL结果转换为数组
     *
     * @param mysqli_result $result 结果
     * @param int $type
     * @param bool $all 输出单行(array)或多行(all)
     *
     * @return array
     */
    public function FetchResult($result, $type = MYSQLI_ASSOC, $all = true)
    {
        if ($all) {
            $tmp = mysqli_fetch_all($result, $type);
            foreach ($tmp as $key => $v) {
                foreach ($v as $k => $va) {
                    $a = json_decode($va);
                    if (!is_null($a)) {
                        $tmp[$key][$k] = $a;
                    }
                }
            }

            return $tmp;
        } else {
            $v = mysqli_fetch_array($result, $type);
            foreach ($v as $k => $va) {
                $a = json_decode($va);
                if (!is_null($a)) {
                    $v[$k] = $a;
                }
            }

            return $v;
        }
    }

    /**
     * 插入数据
     *
     * @param string $table 表名
     * @param array $data 数据对
     *
     * @return bool
     */
    public function InsertData($table, $data)
    {
        $table = $this->prefix . $table;
        $sql = "INSERT INTO `{$table}` ";
        $keys = "(";
        $values = "(";
        $first = true;
        foreach ($data as $key => $value) {
            if (!$first) {
                $keys .= ',`' . $key . '`';
                $values .= ',' . $this->check_input($value);
            } else {
                $keys .= '`' . $key . '`';
                $values .= $this->check_input($value);
                $first = false;
            }
        }
        $sql .= $keys . ') VALUES ' . $values . ');';
        if ($this->Query($sql) && $this->getAffectedRow() == 1) {
            return $this->SQL->insert_id ? $this->SQL->insert_id : true;
        } else {
            return false;
        }
    }

    /**
     * 获取上次查询所英雄行数
     *
     * @return int 上次查询所用行数
     */
    public function GetAffectedRow()
    {
        return $this->SQL->affected_rows;
    }

    /**
     * @return array
     */
    public function getError()
    {
        return [
            'error' => true,
            'msg' => $this->SQL->error,
            'en' => $this->SQL->errno,
        ];
    }

    /**
     * @param string $table
     * @param array $where
     * @param array $data
     *
     * @return bool
     */
    public function UpdateData($table, $data, $where = [])
    {
        $w = "";
        if (count($where)) {
            $w = ' WHERE';
        }
        $w .= $this->ArrayToWhere($where);
        $v = "";
        $first = true;
        foreach ($data as $k => $va) {
            if (!$first) {
                $v .= ', ';
            }
            $first = false;
            $v .= "`{$k}` = " . $this->check_input($va);
        }
        $sqll = 'UPDATE `' . $this->prefix . $table . '` SET ' . $v . $w;
        if ($this->Query($sqll) == false) {
            return false;
        } else {
            return true;
        }
    }

}

