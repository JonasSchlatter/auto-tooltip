<?php
class Tooltip_Parser
{
    private function translate($tag, $value)
    {
        global $dict;

        if ($dict[$tag][$value])
        {
            return $dict[$tag][$value];
        }

        if ($dict[$tag]["*"])
        {
            return $value . $dict[$tag]['*'];
        }
        return $value;
    }

    private function parse_tooltip($tooltip)
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

    private function parse_tooltips($tt_code)
    {
        $source = explode(';', $tt_code[1]);
        $source = array_filter($source);
        $tooltips = array_map(array($this, 'parse_tooltip'), $source);

        $i = 0;
        foreach ($source as $src)
        {
            if ($i === 0)
            {
                $output .= '<span class="tooltip">[';
            }
            else 
            {
                $output .= ';';
            }
            $output .= $src;

            $i++;
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

    private function add_tooltip($matches)
    {
        if ($matches[2] === "strong")
        {
            $tag_start = strpos($matches[3], '[');
            if ($tag_start === false)
            {
                return $matches[0];
            }

            $tag_end = strpos($matches[3], ']') - $tag_start;

            if ($tag_end)
            {
                $ttip = preg_replace_callback(
                    "/\[(?=[^[]*$)(.+?)]/s",
                    array($this, 'parse_tooltips'),
                    $matches[3]
                );

                return "<$matches[2]>$ttip$matches[4]";
            }
        }

        return $matches[0];
    }

    public function parse($buffer)
    {
        return preg_replace_callback(
            "/(<([^.]+)>)([^<]+)(<\\/\\2>)/s",
            array($this, 'add_tooltip'),
            $buffer
        );
    }
}
