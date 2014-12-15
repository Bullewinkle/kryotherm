<?
  require_once($_SERVER['DOCUMENT_ROOT']."/classes/my_db.php");

  require_once($_SERVER['DOCUMENT_ROOT']."/settings.inc.php");

  $url_name = URL;

  class CAuthorization
  {
    // ���������� ---
    var $db  = null;
    var $idp = null;
    var $result = null;
    // --------------

    function CAuthorization()
    {
      $this->db = new CMyDB(HOST_NAME, DB_NAME, USER, PSW);
    }

    function HTTPAuthorizate()
    {
      header('WWW-Authenticate: Basic realm='.URL.'');
      header("HTTP/1.0 401 Unauthorized");
      exit;
    }

    function Authorizate()
    {
      if(!isset($_SERVER['PHP_AUTH_USER']) and !isset($_SERVER['PHP_AUTH_PW']))
        $this->HTTPAuthorizate();
      elseif($this->FindPeople($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) == null)
        $this->HTTPAuthorizate();
    }

    // ����� IDP �� $name, $psw �� ������� security
    function FindPeople($name, $psw)
    {
      $sql = "select id from ".TBL_PREF."users
              where username='$name' and password='$psw'";
      $this->result = $this->db->Execute($sql);

      // ��������� ���������� (idp) �� �������
      $this->idp = $this->GetFieldValue($this->result);

      // ���� � ���������� ������� �� �������� ������ 1 ������
      if($this->db->NumRows() != 1)  $this->idp = null;

      return $this->idp;
    }

    // ��������� ���� ������������
    function GetRole($name, $psw)
    {
      $sql = "select r.role
              from ".TBL_PREF."users u inner join ".TBL_PREF."role r
                                       on u.id_role=r.id
              where u.username='$name' and u.password='$psw'";
      return $this->QueryExecute($sql, 0);
    }

    // ��������� ���� ������������
    function GetIdRole($name, $psw)
    {
      $sql = "select r.id
              from ".TBL_PREF."users u inner join ".TBL_PREF."role r
                                       on u.id_role=r.id
              where u.username='$name' and u.password='$psw'";
      return $this->QueryExecute($sql, 0);
    }

    // ��������� ������ ������������
    function GetSection($name, $psw)
    {
      $sql = "select id_section from ".TBL_PREF."users
              where username='$name' and password='$psw'";
      return $this->QueryExecute($sql, 0);
    }

    // ��������� ������ ����������� �������
    function GetPermissPages($name, $psw)
    {
      $sql = "select p.id_page, p.id_user
              from ".TBL_PREF."users u left join ".TBL_PREF."permissions p
                                       on p.id_user=u.id
              where u.username='$name' and u.password='$psw'";
      return $this->QueryExecute($sql, array(0, 1));
    }

    // ������������ ������ � ���������� ���������(�) ��� ����������
    function QueryExecute($sql = "", $arrOfResult = -1)
    {
      if(count($arrOfResult) > 1)               // ���� ������� - ������
        $retResult = array_fill(0, count($arrOfResult), null );
      else                                      // �����
        $retResult = null;                      // ��������� - ���� ��������

      if($sql != "")
      {
        $sqlResult = $this->db->Execute($sql);  // ���������� ���������� �������
        if($arrOfResult != -1)                  // ���� ���������� �������� ���.
          $retResult = $this->GetFieldValue($sqlResult, $arrOfResult);
      }

      return $retResult;
    }

    // ��������� �������� ���� �� ���������� ������� �� ��� ������
    function GetFieldValue($queryResult = "", $fieldNumber = 0)
    {
      if($queryResult=="") $queryResult = $this->result;
      if(count($fieldNumber) > 1)  // ���� ��������. > 1 ���� - ������ ������
        $this->result = array_fill(0, count($fieldNumber), null);
      else                         // ����� ������ �
        $this->result = null;            // ����� ����������

      if($this->db->NumRows($queryResult) >= 1)
        while ($row = mysql_fetch_array($queryResult, MYSQL_BOTH))
          if(count($fieldNumber) > 1)
            for($i=0; $i<count($fieldNumber); $i++)
              $this->result[$i][] = $row[$fieldNumber[$i]];
          else
            $this->result[] = $row[$fieldNumber];

      return $this->result;
    }

    // ��������� �������������� ��������
    function GetImgId($id=0)
    {
      if(empty($id)) $sql = "select max(id) from ".TBL_PREF."images";
      else $sql = "select id from ".TBL_PREF."images where id_pages=".$id;

      $this->QueryExecute($sql, 0);

      return ((empty($this->result))?0:$this->result);
    }

    // ��������� �������������� ���������
    function GetDocId($id=0)
    {
      if(empty($id)) $sql = "select max(id) from ".TBL_PREF."docs";
      else $sql = "select id from ".TBL_PREF."docs where id_pages=".$id;

      $this->QueryExecute($sql, 0);

      return ((empty($this->result))?0:$this->result);
    }

    // ��������� �������������� �������� �� ����� � �������������� ������
    function GetPageId($name='', $par=-1)
    {
      if(!empty($name) and ($par>=0))
      {
        $sql = "select id from ".TBL_PREF."pages
                where name='$name' and id_parent=$par";
        $this->QueryExecute($sql, 0);
        return ((empty($this->result))?0:$this->result);
      }
      else return 0;
    }

    // ������� ������
    function Free()
    {
      if($this->db != null) $this->db->Free();
    }
  }
?>