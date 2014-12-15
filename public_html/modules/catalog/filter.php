<?  $idCat = $_REQUEST['idCat'];

    $category = $product = '';

    // если не пустой id категории
    if ($idCat)
    {
        $_SESSION['filter_search'] = null;
        // определяем предка категории
        $parent = $filter->define_parent($idCat);
        // если предок нулевой
        if (!$parent)
        {
            $category = $idCat;  $product = '';
        }
        else
        {
            $category = $parent; $product = $idCat;
        }
     }

    if (count($_POST)>0 && $_POST['filter'] == '1')

        $electro = $_POST;

    elseif(count($_SESSION['filter_search'])>0)

        $electro = $_SESSION['filter_search'];

    else
        $electro = array('I_max' => array(), 'Q_max' => array(), 'U' => array(), 'R' => array(),
                         'A' => '', 'B' => '', 'C' => '', 'D' => '', 'Drus' => '',
                         'di' => '', 'sealing_type' => '', 'HT' => '', 'category' => $category, 'product' => $product);

    //$filter->define_lists($cat_prod);

    $filter->electro_filter_values($electro, $_REQUEST['idCat']);
?>
<div class="title filter_header">Фильтр<span></span></div>
<div class="filter_conteiner">

<div class="load">Загрузка<br /><img src="../../img/loading_100x100.gif">
<!--[if lte IE 6.5]><iframe></iframe><![endif]-->
</div>
<div class="loading">
<!--[if lte IE 6.5]><iframe></iframe><![endif]-->
</div>

<form name="filter" action="/filter_result.php" method="post">
<? include(CATALOG_SCRIPT_DIR.'filter_html.php'); ?>
</form>




<div id="result"></div>
</div>