<?
    $txt .= '<div style="height: 54px;">
                 <a href="/">В каталог</a> &gt; <span class="active">Поиск по сайту</span>
             </div>
             <h1>Результаты поиска</h1>
            ';

    if (!empty($_POST['search']))
    {
        $search = $_POST['search'];

        // категории
        $auth->db->select = '*, REPLACE(name, "'.$search.'", "<b>'.$search.'</b>") as search';
        $categories = $auth->db->get_data(array('name' => str_replace(',','%',$search), 'published' => '1'),
                                          'catalog_categories','LIKE', '("%', '%")');

        if (is_array($categories))
        {
            $txt .= '<p><i>В категориях товаров:</i></p>';
            foreach ($categories as $k => $v)

            $txt .= '<p><a href="/index.php?idCat='.$v['id'].'" title="'.$v['name'].'">'.$v['name'].'</a><br />
                     ...'.$v['search'].'...</p>';
        }


        // товары

        $auth->db->select = 'p.*, p2c.id_category, REPLACE(p.name, "'.$search.'", "<b>'.$search.'</b>") as search';
        $auth->db->join_table = 'LEFT JOIN catalog_p2c p2c ON p.id = p2c.id_product';
        $categories = $auth->db->get_data(array('p.name' => str_replace(',','%',$search), 'p.published' => '1'),
                                          'catalog_products p','LIKE', '("%', '%")');

        if (is_array($categories))
        {
            $txt .= '<p><i>В товарах:</i></p>';
            foreach ($categories as $k => $v)

            $txt .= '<p><a href="/index.php?idCat='.$v['id_category'].'" title="'.$v['name'].'">'.$v['name'].'</a><br />
                     ...'.$v['search'].'...</p>';
        }

        // страницы
        $products = $auth->db->get_data(array('story_text' => str_replace(',','%',$search), 'published' => '1'),
                                        'pages','LIKE', '("%', '%")');

        if (is_array($products))
        {
            $txt .= '<p><i>В страницах:</i></p>';
            foreach ($products as $k => $v)
            {
            $find = str_replace($search,'<b>'.$search.'</b>',strip_tags($v['story_text']));
            $txt .= '<p><a href="/index.php?page_id='.$v['id'].'" title="'.$v['name'].'">'.$v['name'].'</a><br />
                     ...'.substr($find, strpos($find, $search) - 3, 200).'...</p>';
            }
        }

        if (!is_array($categories) && !is_array($products))
            $txt .= '<p>По запросу "'.$search.'" ничего не найдено.</p>';
    }
    else
        $txt .= '<p>Введите слово для поиска.</p>';

?>