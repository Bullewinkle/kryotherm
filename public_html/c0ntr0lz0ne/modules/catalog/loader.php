<?  if ($action == 'load_price')
    {
      $loader->header = '�������� ������';

      if (!empty($_FILES['price']['tmp_name']))
      {
        // ������ ������
        $parsed_data = $loader->parser($_FILES['price']['tmp_name']);

        // ������������ �� ����������
        $codes = $loader->preloader($parsed_data);

        // ���������� ������ � ������
        $_SESSION['parsed_data'] = $parsed_data;
        $_SESSION['errors'] = $loader->error;
        $_SESSION['incorrect_codes'] = $codes['incorrect'];
        $_SESSION['correct_categories'] = $codes['correct'];

        if (is_array($codes['incorrect']))
            $common->redirect('/c0ntr0lz0ne/index.php?action=load');
        else
            $common->redirect('/c0ntr0lz0ne/index.php?action=final_load');
      }
      else
          $loader->error = '�������� ���� ��� ��������';
    }
    elseif ($action == 'load')
    {
        $loader->header = '������ ������ ������������ � �����������';
        $start = (!empty($_REQUEST['start'])?$_REQUEST['start']:0);

        if (is_array($_POST['cat_name']) && is_array($_POST['root_category']))

            $errors = $loader->insert_into_base($_POST['cat_name'],$_POST['root_category'],
                                                $_POST['code'],$_SESSION['incorrect_codes'],
                                                $_SESSION['correct_categories']);

        // ���� �������������� ����� �� �������� - ��������� � �������� ���������
        $try_codes = implode('',$_SESSION['incorrect_codes']);
        if (empty($try_codes))
            $common->redirect('?action=final_load');

    }
    elseif ($action == 'final_load')
    {
        $loader->header = '�������� �������';

        // ������� ��� ������ � ������ ���������
        $loader->delete_old_products($_SESSION['correct_categories']);
        // ���������� ����� ������
        $_SESSION['errors'] .= $loader->load_products($_SESSION['parsed_data'],
                                                      $_SESSION['incorrect_codes'],
                                                      $_SESSION['correct_categories']);
    }
    elseif ($action == 'del_all_products')
    {
        $loader->header = '��� �������� �������.';
        $delete_message = '<p>������� ���� ������, ������� �������� ������ � ��������� ���� �������.</p>';
        $loader->total_products_delete();
    }

?>
<style type="text/css">
#errors {font-size: 12px;}
</style>
<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
  <td width="150"><img width="120" height="120" src="images/page-m.jpg"></td>
  <td width="100%" valign="top" align="left">
    <h1><?=$loader->header?></h1>
<? if ($action == 'load_price'){ ?>

    <form method="post" action="index.php?action=load_price" enctype="multipart/form-data" name="FormName">
      <input type="file" value="" name="price">
      <input type="submit" value="���������">
    </form>

<? }elseif ($action == 'load'){?>

    <p style="margin-top: 20px">��������: <?=$loader->preload_listing($_SESSION['incorrect_codes'],$start)?></p>

<? }elseif ($action == 'final_load'){ ?>

    <p>��������� �������: <?=$loader->counter;?></p>
<? } ?>
  </td>
</tr>
</table>
<div id="dataTable">
<? if ($action == 'load_price'){ ?>

   <p>�������� ����� ��� ��������.</p>

<? }elseif ($action == 'load'){ ?>

<form action="?action=load&start=<?=$start?>" method="post">
<?=$loader->get_preload_list($_SESSION['incorrect_codes'], $errors, $start);?>
</form>

<? }elseif ($action == 'final_load'){ ?>

<p><?=(!empty($_SESSION['errors'])?$_SESSION['errors']:'�������� ������ �������.');?></p>

<? } ?>
<?=$delete_message;?>
</div>