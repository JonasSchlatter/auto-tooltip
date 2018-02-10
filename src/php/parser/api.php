<?php
namespace JMS\autotooltip;

function parser($buffer)
{
    static $parser;

    if ( is_null($parser) )
    {
        $parser = new OutputParser;
    }

    return $parser->parse($buffer);
}

function parseArticle($buffer)
{
    return parser('%ARTICLE%' . $buffer);
}
