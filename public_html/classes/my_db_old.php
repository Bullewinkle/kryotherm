<?php
  class CMyDB
  {
    // ����������
    var $link = null;
    var $result = null;
    // ----------

    // ����������� � �� � ��������� �����������
    function CMyDB($host = "", $db, $user="", $psw = "")
    {
      // ����������� � MySQL
      $this->link = mysql_connect($host, $user, $psw)
          or die("Could not connect : " . mysql_error());

      // ����� ��
      mysql_select_db($db) or die("Could not select database");
    }

    // ���������� �������
    function Execute($sql)
    {
      $this->result = mysql_query($sql)
          or die("Query failed : " . mysql_error());

      // �������� ����������� ������� $sql
      if(!$this->result)
        return null;
      else
        return $this->result;
    }

    // ���������� ���-�� ����� ����������
    function NumRows($result = "")
    {
      if($result != "")
        return mysql_num_rows($result);
      else
        return mysql_num_rows($this->result);

    }

    // ������� ������
    function Free()
    {
      if($this->link != null) mysql_close($this->link);
    }
  }
?>