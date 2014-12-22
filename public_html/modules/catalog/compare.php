<?
    if (!empty($_REQUEST['clear_compare']))
        $_SESSION['compare'] = array();

    $compare_data = $_SESSION['compare'];
    $count = count($compare_data);

   // print_r($compare_data);

    $txt .= '<div style="height: 54px;">
                 <a href="/">В каталог</a> &gt; <span class="active">Сравнение</span>
             </div>
             <h1>Сравнение товаров</h1>
            ';

    $order = (!empty($_REQUEST['order'])?$_REQUEST['order']:'name_asc');
    $limit_begin = (!empty($_REQUEST['limit_begin'])?$_REQUEST['limit_begin']:0);
    $per_page    = (!empty($_REQUEST['per_page'])?$_REQUEST['per_page']:10);
    $href = '/compare.php'.(!empty($order)?'&order='.$order:'');

    $header = products_header_data();
    $txt .= viewPages($count, $limit_begin, $per_page, 4, $href.'&limit_begin=').
            '<table width="100%" border="0" cellpadding="0" cellspacing="1" class="items" >
             <tr class="grey">';
    $txt .= get_products_header($header, $order, '/compare.php&limit_begin='.$limit_begin.'&','compare');

    if ($count > 0)
    {
        $compare_data = prepare_to_perpage($compare_data, $limit_begin, $per_page);

        $compare_data = sort_array($compare_data, $order, 'compare');

        if (is_array($compare_data)) foreach ($compare_data as $k => $v)
        {
            //if ($k < ($limit_begin*$per_page+$per_page) && $k >= $limit_begin*$per_page)
           // {
                $v = compare_values($v);

                $txt .= '<tr '.((($k+1)%2 == 0)?"class='grey'":"").'>
                        '.get_product_content($v,'compare').'
                         </tr>';
            //}
        }
    }
    else
        $txt .= '<tr><td colspan="17" align="center" height="26"> Нет товаров для сравнения </td></tr>';

    $txt .= '</table>';

    if ($count > 0)

        $txt .= '<p><input type="button" value="Очистить сравнение" onClick="javascript: document.location.href=\''.$href.'&limit_begin='.$limit_begin.'&clear_compare=1\'"></p>';




?>