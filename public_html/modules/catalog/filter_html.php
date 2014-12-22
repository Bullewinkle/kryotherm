    <p class="tar"><a href="javascript: void[0];" onClick="resset();">Очистить >></a> &nbsp; <a href="" onClick='javascript:document.filter.submit(); return false;'><strong style="font-size:14px;">Искать >></strong></a></p>

    <p class="sub_head">Рубрика</p><br class='clearfloat' />
    <select size="1" name="category" onChange="list_processing(this);" >
    <option value=""> Весь список </option><?=$filter->roots_list?></select>

    <p class="sub_head">Базовый товар<img src="/img/help.gif" border="0" onClick="show_help(this, 9, 120)"></p>
    <select size="1" name="product" onChange="list_processing(this);" >
    <option value=""> Весь список </option><?=$filter->child_list?></select>

    <p class="sub_head">Электрические параметры</p>
    <table width="100%" cellpadding="0" cellpadding="0" class="electro_filter">
    <?=$filter->electro_filter($filter->I_max, $filter->Q_max, $filter->U, $filter->R, $filter->A, $filter->B, $filter->C, $filter->D, $filter->Drus, $filter->di, $filter->sealing_type, $filter->HT);?>
    </table>
    <p class="tar"><a href="javascript: void[0];" onClick="resset();">Очистить >></a> &nbsp; <a href="" onClick='javascript:document.filter.submit(); return false;'><strong style="font-size:14px;">Искать >></strong></a></p>
    <input name="filter" type="hidden" value="1">