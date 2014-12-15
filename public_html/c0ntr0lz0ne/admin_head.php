          <table cellpadding=0 cellspacing=0 width=100%>
<? if (empty($_REQUEST['action'])){ ?>
            <tr class=main>
              <td width=20% align="right"><a href="?action=pages"><img src="images/page-b.jpg" width=145 height=145></a></td>
              <td width=20% align="center"><a href="?action=pages" class="modtitle">Страницы</a></td>
              <td width=60% class="">Управление страницами. В этом разделе Вы сможете создавать редактировать и удалять страницы сайта. А также управлять сортировкой.</td>
            </tr>
<? if ($modules->isModuleInstall('news')){ ?>
            <tr><td colspan="3"><div class="dot">&nbsp;</div></td></tr>
            <tr class=main>
              <td width=20% align="right"><a href="?action=news"><img src="images/news-b.jpg" width=145 height=145></a></td>
              <td width=20% align="center"><a href="?action=news" class="modtitle">Новости</a></td>
              <td width=60% class="">Управление новостями. Если у Вас есть свежие новости или желание отредактировать текущие - то вам сюда.</td>
            </tr>
<? } ?>

<? if ($modules->isModuleInstall('catalog')){ ?>
            <tr><td colspan="3"><div class="dot">&nbsp;</div></td></tr>
            <tr class=main>
              <td width=20% align="right"><a href="?action=catalog"><img src="images/catalog-b.jpg" width=145 height=145></a></td>
              <td width=20% align="center"><a href="?action=catalog" class="modtitle">Каталог</a></td>
              <td width=60% class="">Управление каталогом. Тут добавляют и редактируют категории товаров, сами товары и настраивают привязку с производителями.</td>
            </tr>
<? } ?>

<? if ($modules->isModuleInstall('gallery')){ ?>
            <tr><td colspan="3"><div class="dot">&nbsp;</div></td></tr>
            <tr class=main>
              <td width=20% align="right"><a href="?action=gallery"><img src="images/gallery-b.jpg" width=145 height=145></a></td>
              <td width=20% align="center"><a href="?action=gallery" class="modtitle">Галерея</a></td>
              <td width=60%>Создание и редактирование галерей, размещение картинок.</td>
            </tr>
<? } ?>

<? if ($modules->isModuleInstall('banners')){ ?>
            <tr><td colspan="3"><div class="dot">&nbsp;</div></td></tr>
            <tr class=main>
              <td width=20% align="right"><a href="?action=banners"><img src="images/gallery-b.jpg" width=145 height=145></a></td>
              <td width=20% align="center"><a href="?action=banners" class="modtitle">Баннеры</a></td>
              <td width=60%>Управление размещенными баннерами. Размещение новых баннеров.</td>
            </tr>
<? } ?>

            <tr><td colspan="3"><div class="dot">&nbsp;</div></td></tr>
            <tr class=main>
              <td width=20% align="right"><a href="?action=modules"><img src="images/page-b.jpg" width=145 height=145></a></td>
              <td width=20% align="center"><a href="?action=modules" class="modtitle">Модули</a></td>
              <td width=60% class="">Подключение дополнительных модулей. Дополнительные модули способны расширить функционал вашего сайта.</td>
            </tr>
            <tr><td colspan="3"><div class="dot">&nbsp;</div></td></tr>

            <tr class=main>
              <td width=20% align="right"><a href="?action=settings"><img src="images/settings-m.jpg" width=145 height=145></a></td>
              <td width=20% align="center"><a href="?action=settings&case=common" class="modtitle">Настройки</a></td>
              <td width=60% class="">Настройка и редактирование констант.</td>
            </tr>
            <tr><td colspan="3"><div class="dot">&nbsp;</div></td></tr>

<? }else{ ?>
            <tr>
              <td width=25% id="modules" style="padding:25px;" valign=top>
                <div><a href="?action=pages"><img src="images/page-s.jpg" width="44" height="44" align=top></a>&nbsp;<a href="?action=pages">Страницы</a></div>

<? if($modules->isModuleInstall('news')){ ?>
                <div><a href="?action=news"><img src="images/news-s.jpg" width="44" height="44" align=top></a>&nbsp;<a href="?action=news">Новости</a></div>
<? } ?>

<? if($modules->isModuleInstall('catalog')){ ?>
                <div><a href="?action=catalog"><img src="images/catalog-s.jpg" width="44" height="44" align=top></a>&nbsp;<a href="?action=catalog">Каталог</a></div>
               <!-- <div class="submenu" id="submenu"><a  href="?action=manufacturers">Производители</a></div>  -->
                <div class="submenu" id="submenu"><a  href="?action=load_price">Загрузка</a></div>
                <div class="submenu" id="submenu">
                <a  href="javascript: var ans = prompt('Нажатие на кнопку OK приведет \nк полному удалению продуктов из базы\nбез возможности восстановления.\n\nЕсли вы уверены, то введите delete м нажмите OK.\n\n'); if (ans == 'delete')document.location.href='?action=del_all_products';">Удалить товары</a></div>
<? } ?>

<? if($modules->isModuleInstall('gallery')){ ?>
                <div><a href="?action=gallery"><img src="images/gallery-s.jpg" width="44" height="44" align=top></a>&nbsp;<a href="?action=gallery">Галерея</a></div>
<? } ?>

<? if($modules->isModuleInstall('banners')){ ?>
                <div><a href="?action=banners"><img src="images/gallery-s.jpg" width="44" height="44" align=top></a>&nbsp;<a href="?action=banners">Баннеры</a></div>
<? } ?>
 <!--
<div><a href="?action=modules"><img src="images/page-s.jpg" width="44" height="44" align=top></a>&nbsp;<a href="?action=modules">Модули</a></div>
<div><a href="?action=modules"><img src="images/settings-m.jpg" width="44" height="44" align=top></a>&nbsp;<a href="?action=settings&case=common">Настройки</a></div>
              -->
              </td>
              <td width=75% style="padding-top:25px; vertical-align: top;">
<? require_once("operations.php"); ?>
              </td>
            </tr>
<? } ?>
          </table>