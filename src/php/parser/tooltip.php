<?php
namespace JMS\autotooltip;

class TooltipParser
{
    private function translate($tag, $value)
    {
        global $dict;

        if ($dict[$tag][$value])
        {
            return $dict[$tag][$value];
        }

        if ($dict[$tag]['*'])
        {
            return $value . $dict[$tag]['*'];
        }

        return $value;
    }

    private function parseTooltip($tooltip)
    {
        $array = explode('=', $tooltip);
        $array[0] = trim($array[0]);
        $array[1] = trim($array[1]);

        $output .= $this->translate('tag', $array[0]);
        if (isset($array[1]))
        {
            $output .= ' ' . $this->translate($array[0], $array[1]);
        }

        return $output;
    }

    private function parseTooltips($toolTipSource)
    {
        $source = explode(';', $toolTipSource[1]);
        $source = array_filter($source);
        $tooltips = array_map(array($this, 'parseTooltip'), $source);

        $index = 0;
        $output .= '<span class="tooltip">[';
        foreach ($source as $src)
        {
            if ($index !== 0)
            {
                $output .= ';';
            }

            $output .= $src;
            $index++;
        }

        $output .= ']<span class="tooltiptext">';
        foreach ($tooltips as $tooltip)
        {
            $output .= $tooltip . '<br><br>';
        }
        $output = substr($output, 0, -8);
        $output .= '</span></span>';

        return $output;
    }

    private function addTooltips($matches)
    {
        if ($matches[2] === 'strong')
        {
            $extendedContent = preg_replace_callback(
                '/\[(?=[^[]*$)(.+?)]/s',
                array($this, 'parseTooltips'),
                $matches[3]
            );

            return "<$matches[2]>$extendedContent$matches[4]";
        }

        return $matches[0];
    }

    public function parse($buffer)
    {
        return preg_replace_callback(
            '/(<([^.]+)>)([^<]+)(<\\/\\2>)/s',
            array($this, 'addTooltips'),
            $buffer
        );
    }
}
