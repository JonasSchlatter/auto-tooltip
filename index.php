<?php
require_once './src/php/parser/main.php';

ob_start('\JMS\autotooltip\parser');
require_once('./src/php/template/header.php');
require_once('./src/php/template/content.php');
require_once('./src/php/template/footer.php');

ob_start('\JMS\autotooltip\parseArticle');
require_once('article.php');
ob_end_clean();
