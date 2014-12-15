<?php
  class CMyDB
  {
    // переменные
    var $link = null;
    var $result = null;
    // ----------

    // подключение к БД с заданными параметрами
    function CMyDB($host = "", $db, $user="", $psw = "")
    {
      // подключение к MySQL
      $this->link = mysql_connect($host, $user, $psw)
          or die("Could not connect : " . mysql_error());

      // выбор БД
      mysql_select_db($db) or die("Could not select database");
    }

    // выполнение запроса
    function Execute($sql)
    {
      $this->result = mysql_query($sql)
          or die("Query failed : " . mysql_error());

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

    // очитска памяти
    function Free()
    {
      if($this->link != null) mysql_close($this->link);
    }
  }
?>