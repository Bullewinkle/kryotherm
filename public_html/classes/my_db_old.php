<?php
class CMyDB
{
  // переменные
  var $link = null;
  var $result = null;
  // ----------

  var $case = null, $select = null, $where = null,
      $order = null, $group = null, $limit = null, $join_table = null;

  // подключение к БД с заданными параметрами
  function CMyDB($host = "127.0.0.1", $db = "u565477573_kryo", $user = "u565477573_therm", $psw = "1992rhbjnthv")
  {
    // подключение к MySQL
    $this->link = mysql_connect($host, $user, $psw)
    or die("Could not connect : " . mysql_error($this->link));

    // выбор БД
    mysql_select_db($db, $this->link) or die("Could not select database");
    mysql_query('SET NAMES cp1251');
    mysql_query('SET CHARACTER SET cp1251');
    mysql_query('SET COLLATION_CONNECTION=cp1251_general_ci');
  }


  // выполнение запроса
  function Execute($sql)
  {
    $this->result = mysql_query($sql, $this->link)
    or die("Query failed : " . mysql_error($this->link));

    // проверка результатов запроса $sql
    if(!$this->result)
      return null;
    else
      return $this->result;
  }

  // возвращает кол-во строк результата
  function NumRows($result = "")
  {
    if($result != "")
      return mysql_num_rows($result);
    else
      return mysql_num_rows($this->result);

  }

  function insert_id()
  {
    return mysql_insert_id($this->link);
  }

  public function case_definer($case)
  {
    if (!empty($case))
    {
      $this->case = null;
      $this->case = $case;
    }
  }

  public function resset_vars()
  {
    $this->where = $this->select = $this->order =
    $this->group = $this->limit = $this->join_table = null;
  }

  public function total_resset($case)
  {
    $this->case_definer($case);
    $this->resset_vars();
  }

  /*  Вставка в базу
  */
  public function insert($data, $case = null)
  {
    $this->total_resset($case);
    $this->Execute("INSERT INTO ".TBL_PREF.$this->case." SET ".$this->get_set($data));

    return $this->insert_id();
  }

  /*  Апдейт
  */
  public function update($data, $where = null, $case = null)
  {
    $this->total_resset($case);
    $this->Execute("UPDATE ".$this->case." SET ".TBL_PREF.$this->get_set($data)."
                       ".(!empty($where)?"WHERE ".$this->get_where($where):""));

  }

  /*  Удаление
  */
  public function del($where = null, $case = null)
  {
    $this->total_resset($case);
    $this->Execute("DELETE FROM ".TBL_PREF.$this->case."
                       ".(!empty($where)?"WHERE ".$this->get_where($where):""));
  }

  /*  Проверка на существование
  */
  public function exist($data, $case = null)
  {
    $this->total_resset($case);
    $this->where = $this->get_where($data);
    $this->sql();

    return $this->NumRows();
  }

  /*  Получить массив с данными
	  $case - таблица с данными
	  $where - условия для выборки (массив)
	  $operator - оператор сравнения для условия
  */
  public function get_data($where = null, $case = null, $operator = '=', $ch1 = '', $ch2 = '')
  {
    $result = null;                  //print $ch1;
    $this->case_definer($case);

    if (is_array($where)) $this->where = $this->get_where($where, $operator, $ch1, $ch2);

    $q = $this->sql();

    while ($r = mysql_fetch_assoc($q)) $result[] = $r;

    return $result;
  }

  /*  Возвращает строку `имя поля`='значение',`имя поля`='значение', ... для вставки\апдейта
	  $operator - операторы сравнения ( =,>,< )
	  $ch1, $ch2 - спецсимволы для выражений типа LIKE('$поиск$')
  */
  protected function get_set($array, $operator = '=', $ch1 = '"', $ch2 = '"')
  {
    $set = null;
    foreach ($array as $k => $v)
    {
      if ($k == 'password' && !empty($v))
        $set .= ", ".$k." ".$operator." ".$ch1.trim(md5($v)).$ch2."";

      elseif($k !== 'password')
        $set .= ", ".$k." ".$operator." ".$ch1.trim($v).$ch2."";
    }

    return substr($set,2);
  }

  /*  Возвращает строку `имя поля`='значение' AND `имя поля`='значение' AND ...
	  $operator - операторы сравнения ( =,>,< )
	  $ch1, $ch2 - спецсимволы для выражений типа LIKE('$поиск$')
  */
  protected function get_where($array, $operator = '=', $ch1 = '', $ch2 = '')
  {
    return str_replace(","," AND ", $this->get_set($array, $operator, $ch1, $ch2));
  }

  /*  Возвращает строку 'значение','значение'...
  */
  protected function get_select($array)
  {
    return implode(',', $array);
  }

  /*  Менеджер sql запросов
  */
  protected function sql()
  {
    $sql = "SELECT ".(!empty($this->select)?$this->select:"*")."
                FROM ".TBL_PREF.$this->case."
               ".(!empty($this->join_table)?$this->join_table:"")."
               ".(!empty($this->where)?"WHERE ".$this->where:"")."
               ".(!empty($this->order)?"ORDER BY ".$this->order:"")."
               ".(!empty($this->group)?"GROUP BY ".$this->group:"")."
               ".(!empty($this->limit)?"LIMIT ".$this->limit:"");
    $this->resset_vars();
    //print($sql);
    return $this->Execute($sql);
  }


  // очитска памяти
  function Free()
  {
    if($this->link != null) mysql_close($this->link);
  }
}
?>