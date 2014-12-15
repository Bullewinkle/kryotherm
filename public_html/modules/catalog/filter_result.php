<?
    $f_data = null;
    $f_data = $_SESSION['filter_data'];

    $count = count($f_data);

    $txt .= '<div style="height: 54px;">
                 <a href="/">В каталог</a> &gt; <span class="active">Фильтр</span>
             </div>
             <h1>Результаты поиска (всего '.$count.')</h1>
            ';

    $order = (!empty($_REQUEST['order'])?$_REQUEST['order']:'name_asc');
    $limit_begin = (!empty($_REQUEST['limit_begin'])?$_REQUEST['limit_begin']:0);
    $per_page    = (!empty($_REQUEST['per_page'])?$_REQUEST['per_page']:20);
    $href = '/filter_result.php'.(!empty($order)?'&order='.$order:'');

    $f_data = prepare_to_perpage($f_data, $limit_begin, $per_page);

    $f_data = sort_array($f_data, $order);

    $header = products_header_data();

    $txt .= viewPages($count, $limit_begin, $per_page, 4, $href.'&limit_begin=').
            '<div id="help_holder"></div>
             <table width="100%" border="0" cellpadding="0" cellspacing="1" class="items" >
             <tr class="grey">';

    $help_ids = $filter->get_help_id();
    $txt .= get_products_header($header, $order, '/filter_result.php&limit_begin='.$limit_begin.'&', '', $help_ids);



    if (is_array($f_data)) foreach ($f_data as $k => $v)
    {
        //if ($k < ($limit_begin*$per_page+$per_page) && $k >= $limit_begin*$per_page)
        //{
            $txt .= '<tr '.((($k+1)%2 == 0)?"class='grey'":"").'>
                    '.get_product_content($v).'
                     </tr>';
        //}
    }

    $txt .= '</table>';

    //$txt .= $html;

?>