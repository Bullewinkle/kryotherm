<?
  if(!empty($_REQUEST['action']))         // если выбрали действие
    switch($_REQUEST['action'])           // определяем какое это действие
    {
      case "move_page":                   // перемещение между разделами
      case "del_page":                    // удаление страницы
      case "pages":                       // просмотр
      case "insert_page":                 // добавление страницы
      case "update_page":                 // обновление страницы
      case "add_page":                    // создание
      case "edit_page":                   // редактирование
      case "lock_page":                   // запретить отображение страницы
      case "unlock_page":                 // разрешить отображение страницы
      case "up_page":                     // поднять страницу
      case "down_page":                   // опустить страницу
          require_once("admin_head.php"); // меню администрирования
          require_once("page.php");       // страниц
          break;

    case "modules":
    case "add_modules":
    case "del_modules":
          require_once("modules.php");
          break;

    case "news":
    case "add_news":
    case "edit_news":
    case "insert_news":
    case "update_news":
    case "delete_news":
    case "lock_news":
    case "unlock_news":
          require_once("modules/news/a_news.php");
          break;

    case "gallery":
    case "add_gallery":
    case "insert_gallery":
    case "edit_gallery":
    case "update_gallery":
    case "delete_gallery":
    case "lock_gallery":
    case "unlock_gallery":
    case "moveUp_gallery":
    case "moveDown_gallery":
    case "add_gallery_images":
    case "insert_gallery_images":
    case "edit_gallery_images":
    case "update_gallery_images":
    case "delete_gallery_images":
    case "lock_gallery_images":
    case "unlock_gallery_images":
    case "moveUp_gallery_images":
    case "moveDown_gallery_images":
    case "link_gallery":
    case "update_gallery_link":
          require("classes/class_gallery.php");
          $gallery = new Gallery($auth, $common);
          require_once("modules/gallery/a_gallery.php");
          break;

    case "settings":
          require("settings.php");
          break;

    case "catalog":
    case "add_category":
    case "insert_category":
    case "edit_category":
    case "update_category":
    case"delete_category":
    case"lock_category":
    case"unlock_category":
    case"moveUp_category":
    case"moveDown_category":
    case"replace_category":
    case "add_product":
    case "insert_product":
    case "edit_product":
    case "update_product":
    case"delete_product":
    case"lock_product":
    case"unlock_product":
    case"moveUp_product":
    case"moveDown_product":
    case"replace_product":
    case "manufacturers":
    case "add_manufacturers":
    case "insert_manufacturers":
    case "edit_manufacturers":
    case "update_manufacturers":
    case"delete_manufacturers":
    case"lock_manufacturers":
    case"unlock_manufacturers":
    case"moveUp_manufacturers":
    case"moveDown_manufacturers":
          require("classes/class_catalog.php");
          $catalog = new Catalog($auth, $common);
          require("modules/catalog/a_catalog.php");
          break;

    case "load_price":
    case "load":
    case "final_load":
    case "del_all_products":
        require("classes/class_loader.php");
        $loader = new loader($auth, $common);
        require("modules/catalog/loader.php");
    break;

    case "banners":
    case "add_banners_category":
    case "insert_banners_category":
    case "edit_banners_category":
    case "update_banners_category":
    case "delete_banners_category":
    case "lock_banners_category":
    case "unlock_banners_category":
    case "add_banners":
    case "insert_banners":
    case "edit_banners":
    case "update_banners":
    case "delete_banners":
    case "lock_banners":
    case "unlock_banners":
    case "moveUp_banners":
    case "moveDown_banners":

        require("classes/class_banners.php");
        $banners = new Banners($auth, $common);;
        require("modules/banners/a_banners.php");
    break;


    }
?>