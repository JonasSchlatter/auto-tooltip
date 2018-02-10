<?php
require './src/php/parser/main.php';

function parser($buffer)
{
    static $parser;
    if (is_null($parser))
    {
        $parser = new Output_Parser;
    }

    return $parser->parse($buffer);
}

function parse_article($buffer)
{
    return parser('%ARTICLE%' . $buffer);
}

ob_start("parser");
require('./src/php/template/header.php');
require('./src/php/template/content.php');
require('./src/php/template/footer.php');

ob_start("parse_article");
require('article.php');
ob_end_clean();

require './src/php/parser/dict.php';
