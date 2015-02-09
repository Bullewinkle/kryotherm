<!doctype html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<meta name="google-site-verification" content="YhctPoYyqqfMEjorlzFjBjp-LKawZ0vMqiBBVl3pArA"/>
	<title><?= ((!empty($title)) ? $title : DEF_TITLE); ?></title>
	<meta name="description" content="<?= ((!empty($desc)) ? $desc : DEF_DESC); ?>"/>
	<meta name="keywords" content="<?= ((!empty($keyw)) ? $keyw : DEF_KEYW); ?>"/>
	<link href="styles/style.css" rel="stylesheet" type="text/css"/>
	<?= (file_exists("styles/news_style.css") ? '<link href="styles/news_style.css" rel="stylesheet" type="text/css" />' : ''); ?>
	<?= (file_exists("styles/gallery_style.css") ? '<link href="styles/gallery_style.css" rel="stylesheet" type="text/css" />' : ''); ?>
	<?= (file_exists("styles/catalog_style.css") ? '<link href="styles/catalog_style.css" rel="stylesheet" type="text/css" />' : ''); ?>
	<!-- -------------------------------------------------------------- DEPS -------------------------------------------------------------- -->
	<script type="text/javascript" src="scripts/libs/jquery.js"></script>
	<script type="text/javascript" src="scripts/libs/jquery.validate.min.js"></script>
	<script type="text/javascript" src="scripts/libs/underscore-min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/json3/3.3.2/json3.min.js"></script>
	<!-- ----------------------------------------------------------- REQUIREMENTS -------------------------------------------------------------- -->
	<script type="text/javascript" src="scripts/api.js"></script>
	<!-- ------------------------------------------------------------- COMMON -------------------------------------------------------------- -->
	<script type="text/javascript" src="scripts/scripts.js"></script>
	<script type="text/javascript" src="scripts/instructions.js"></script>

	<!-- -------------------------------------------------------------- SERVER SDATA-------------------------------------------------------------- -->
	<script type="text/javascript">
		window.kryotherm || (window.kryotherm = {});
		window.kryotherm.$_REQUEST = 	<?= json_encode($_REQUEST); ?>;
		window.kryotherm.$_GET = 		<?= json_encode($_GET); ?>;
	</script >
	<!-- ------------------------------------------------------------ END SERVER SDATA-------------------------------------------------------------- -->


	<!--[if IE 6]>
	<script type="text/javascript" src="/script/ie6fix.js"></script>
	<script type="text/javascript">
		DD_belatedPNG.fix('.png24');
	</script>
	<style type="text\css">
		html body {
			height: 100%;
		}
	</style>
	<![endif]-->

</head>

<body <?= (!empty($_REQUEST['galleryId']) ? "onLoad='LoadImages();'" : "") ?> >

<!-- Start Header -->
<div class="wr">
	<div class="header">
		<div class="slog">Производство термоэлектрических модулей и&nbsp;комплексных систем охлаждения</div>
		<div class="logo"><a href="http://kryotherm.ru/ru/" title="Главнвя страница сайта kryotherm.ru"><img
					src="/img/logo.jpg" alt="криотерм" border="0" align="left"/></a></div>
		<div class="flags">
			<ul>
				<li><a href="http://kryotherm.ru/ru/" title=""><img src="/img/flag_rf.jpg" border="0" alt="rf"/></a>
				</li>
				<li><a href="http://kryothermtec.com/" title=""><img src="/img/flag_gb.jpg" border="0" alt="gb"/></a>
				</li>
				<li><a href="http://kryothermtec.com/french.html" title=""><img src="/img/flag_fr.jpg" border="0"
																				alt="fr"/></a></li>
				<li><a href="http://kryothermtec.com/espania.html" title=""><img src="/img/flag_esp.jpg" border="0"
																				 alt="esp"/></a></li>
				<li><a href="http://kryothermtec.com/portugal.html" title=""><img src="/img/flag_xz.jpg" border="0"
																				  alt="xz"/></a></li>
				<li><a href="http://kryothermtec.com/italia.html" title=""><img src="/img/flag_it.jpg" border="0"
																				alt="it"/></a></li>
			</ul>
		</div>
		<div class="lh"></div>
		<div class="rh"></div>
		<div class="icons">
			<ul>
				<li><a href="/" title=""><img src="/img/icon_home.gif" border="0" alt="home"/></a></li>
				<li><a href="" title=""><img src="/img/icon_mail.gif" border="0" alt="mail"/></a></li>
				<li><a href="" title=""><img src="/img/icon_map.gif" border="0" alt="map"/></a></li>
				<li><a href="/cart.php" title=""><img src="/img/icon_shop.gif" border="0" alt="shop"/></a></li>
			</ul>
		</div>
		<div class="mh">
			<ul>
				<li><a href="http://kryotherm.ru/ru/" title="">Главная</a></li>
				<li><a href="http://kryotherm.ru/ru/about-company.html" title="">О компании</a></li>
				<li><a href="http://kryotherm.ru/ru/tem-construction/" title="">Технологии</a></li>
				<li><a href="/" title="">Продукция</a></li>
				<li><a href="/cart.php" title="">Корзина</a></li>
				<li><a href="http://kryotherm.ru/ru/technical-support.html" title="">Тех.поддержка</a></li>
				<li><a href="http://kryotherm.ru/ru/tests.html" title="">Испытания</a></li>
				<li><a href="http://kryotherm.ru/ru/download-catalog.html" title="">Электронные каталоги</a></li>
				<li><a href="http://kryotherm.ru/ru/contact-us.html" title="">Контакты</a></li>
				<li><a href="http://kryotherm.ru/ru/vacancy.html" title="">Вакансии</a></li>
			</ul>
		</div>
		<!--begin of Top100--><a href="http://top100.rambler.ru/top100/"><img
				src="http://counter.rambler.ru/top100.cnt?324007" alt="Rambler's Top100" width=1 height=1 border=0></a><!--end of Top100 code-->
	</div>
	<!-- End Header -->

	<!-- Start Content -->
	<div class="wr1">
		<div class="wr2">
			<div class="lc">
				<div class="menu">
					<form action="search.php" name="search_form" method="post" class="magaz">
						<input type="text" name="search" value="<?= $_POST['search']; ?>" class="search"/>
						<input type="image" src="/img/blank.gif" width="24" height="28"
							   onClick="javascript: this.form.subbmit();" align="right"/>
					</form>

					<p class="title dec-none"><a href="/" style="color: black; text-decoration: none;">
							Интернет-магазин </a></p>
					<ul><?= getCatalog($auth, 2, 0, (!empty($_REQUEST['idCat']) ? $_REQUEST['idCat'] : 0)) ?></ul>
					<?

					if ($show_right) {
						?>
						<span class="dec-none">
               <a href="/cart.php" style="text-decoration: none;">
				   <span class="korzina-t title"><b>Корзина</b></span></a></span>
						<p class="ot-37"><?= ((function_exists("cart_status")) ? cart_status($_SESSION['user_cart']) : ""); ?></p>

						<br/>
						<span class="dec-none">
               <a href="/compare.php" style="text-decoration: none;">
				   <span class="compare-t title"><b>Сравнение</b></span></a></span>
						<p class="ot-37"><?= ((function_exists("compare_status")) ? compare_status($_SESSION['compare']) : ""); ?></p>

						<? require_once(CATALOG_SCRIPT_DIR . "filter.php"); ?>

						<ul style="margin: 40px 0"><? include("menu/dop_menu.php"); ?></ul>
					<? }

					if (!empty($page_id) || $_SERVER['REQUEST_URI'] == '/' || strpos($_SERVER['REQUEST_URI'], 'search.php') || strpos($_SERVER['REQUEST_URI'], 'order.php')) {
						?>
						<span class="dec-none">
               <a href="/cart.php">
				   <span class="korzina-t title"><b>Корзина</b></span></a></span>
						<p class="ot-37"><?= ((function_exists("cart_status")) ? cart_status($_SESSION['user_cart']) : ""); ?></p>

						<br/>
						<span class="dec-none">
               <a href="/cart.php">
				   <span class="compare-t title"><b>Сравнение</b></span></a></span>
						<p class="ot-37"><?= ((function_exists("compare_status")) ? compare_status($_SESSION['compare']) : ""); ?></p>

						<? require_once(CATALOG_SCRIPT_DIR . "filter.php");?>

					<?
					}
					?>
				</div>
			</div>
			<div class="center content">   <!-- колонка с содержимым -->
				<br/>
				<div class="warning" style="font-size:16px; font-weight: bold;">
					Уважаемые покупатели! Для оформления заказов доступна оплата банковскими картами. Приятных покупок!
				</div>
				<br/>


				<div class="cont" <?= ($show_right ? "style='width: 100%'" : "") ?>>         <!-- вторая колонка -->
<?= ((!empty($txt)) ? $txt : ""); ?>