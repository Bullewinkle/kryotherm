          <table cellpadding=0 cellspacing=0 width=100%>
<? if (empty($_REQUEST['action'])){ ?>
            <tr class=main>
              <td width=20% align="right"><a href="?action=pages"><img src="images/page-b.jpg" width=145 height=145></a></td>
              <td width=20% align="center"><a href="?action=pages" class="modtitle">��������</a></td>
              <td width=60% class="">���������� ����������. � ���� ������� �� ������� ��������� ������������� � ������� �������� �����. � ����� ��������� �����������.</td>
            </tr>
<? if ($modules->isModuleInstall('news')){ ?>
            <tr><td colspan="3"><div class="dot">&nbsp;</div></td></tr>
            <tr class=main>
              <td width=20% align="right"><a href="?action=news"><img src="images/news-b.jpg" width=145 height=145></a></td>
              <td width=20% align="center"><a href="?action=news" class="modtitle">�������</a></td>
              <td width=60% class="">���������� ���������. ���� � ��� ���� ������ ������� ��� ������� ��������������� ������� - �� ��� ����.</td>
            </tr>
<? } ?>

<? if ($modules->isModuleInstall('catalog')){ ?>
            <tr><td colspan="3"><div class="dot">&nbsp;</div></td></tr>
            <tr class=main>
              <td width=20% align="right"><a href="?action=catalog"><img src="images/catalog-b.jpg" width=145 height=145></a></td>
              <td width=20% align="center"><a href="?action=catalog" class="modtitle">�������</a></td>
              <td width=60% class="">���������� ���������. ��� ��������� � ����������� ��������� �������, ���� ������ � ����������� �������� � ���������������.</td>
            </tr>
<? } ?>

<? if ($modules->isModuleInstall('gallery')){ ?>
            <tr><td colspan="3"><div class="dot">&nbsp;</div></td></tr>
            <tr class=main>
              <td width=20% align="right"><a href="?action=gallery"><img src="images/gallery-b.jpg" width=145 height=145></a></td>
              <td width=20% align="center"><a href="?action=gallery" class="modtitle">�������</a></td>
              <td width=60%>�������� � �������������� �������, ���������� ��������.</td>
            </tr>
<? } ?>

<? if ($modules->isModuleInstall('banners')){ ?>
            <tr><td colspan="3"><div class="dot">&nbsp;</div></td></tr>
            <tr class=main>
              <td width=20% align="right"><a href="?action=banners"><img src="images/gallery-b.jpg" width=145 height=145></a></td>
              <td width=20% align="center"><a href="?action=banners" class="modtitle">�������</a></td>
              <td width=60%>���������� ������������ ���������. ���������� ����� ��������.</td>
            </tr>
<? } ?>

            <tr><td colspan="3"><div class="dot">&nbsp;</div></td></tr>
            <tr class=main>
              <td width=20% align="right"><a href="?action=modules"><img src="images/page-b.jpg" width=145 height=145></a></td>
              <td width=20% align="center"><a href="?action=modules" class="modtitle">������</a></td>
              <td width=60% class="">����������� �������������� �������. �������������� ������ �������� ��������� ���������� ������ �����.</td>
            </tr>
            <tr><td colspan="3"><div class="dot">&nbsp;</div></td></tr>

            <tr class=main>
              <td width=20% align="right"><a href="?action=settings"><img src="images/settings-m.jpg" width=145 height=145></a></td>
              <td width=20% align="center"><a href="?action=settings&case=common" class="modtitle">���������</a></td>
              <td width=60% class="">��������� � �������������� ��������.</td>
            </tr>
            <tr><td colspan="3"><div class="dot">&nbsp;</div></td></tr>

<? }else{ ?>
            <tr>
              <td width=25% id="modules" style="padding:25px;" valign=top>
                <div><a href="?action=pages"><img src="images/page-s.jpg" width="44" height="44" align=top></a>&nbsp;<a href="?action=pages">��������</a></div>

<? if($modules->isModuleInstall('news')){ ?>
                <div><a href="?action=news"><img src="images/news-s.jpg" width="44" height="44" align=top></a>&nbsp;<a href="?action=news">�������</a></div>
<? } ?>

<? if($modules->isModuleInstall('catalog')){ ?>
                <div><a href="?action=catalog"><img src="images/catalog-s.jpg" width="44" height="44" align=top></a>&nbsp;<a href="?action=catalog">�������</a></div>
               <!-- <div class="submenu" id="submenu"><a  href="?action=manufacturers">�������������</a></div>  -->
                <div class="submenu" id="submenu"><a  href="?action=load_price">��������</a></div>
                <div class="submenu" id="submenu">
                <a  href="javascript: var ans = prompt('������� �� ������ OK �������� \n� ������� �������� ��������� �� ����\n��� ����������� ��������������.\n\n���� �� �������, �� ������� delete � ������� OK.\n\n'); if (ans == 'delete')document.location.href='?action=del_all_products';">������� ������</a></div>
<? } ?>

<? if($modules->isModuleInstall('gallery')){ ?>
                <div><a href="?action=gallery"><img src="images/gallery-s.jpg" width="44" height="44" align=top></a>&nbsp;<a href="?action=gallery">�������</a></div>
<? } ?>

<? if($modules->isModuleInstall('banners')){ ?>
                <div><a href="?action=banners"><img src="images/gallery-s.jpg" width="44" height="44" align=top></a>&nbsp;<a href="?action=banners">�������</a></div>
<? } ?>
 <!--
<div><a href="?action=modules"><img src="images/page-s.jpg" width="44" height="44" align=top></a>&nbsp;<a href="?action=modules">������</a></div>
<div><a href="?action=modules"><img src="images/settings-m.jpg" width="44" height="44" align=top></a>&nbsp;<a href="?action=settings&case=common">���������</a></div>
              -->
              </td>
              <td width=75% style="padding-top:25px; vertical-align: top;">
<? require_once("operations.php"); ?>
              </td>
            </tr>
<? } ?>
          </table>