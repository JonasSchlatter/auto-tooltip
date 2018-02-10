<?php
namespace JMS\autotooltip;

class OutputParser
{
    public function __construct()
    {
        $this->tooltip_parser = new TooltipParser;
        $this->debug_log = "";
        $this->input = "";
    }

    private function catchArticle($buffer)
    {
        if (substr($buffer, 0, 9) == '%ARTICLE%')
        {
            $this->debug_log .= "Adding article to input cache\r\n";
            $this->debug_log .= '<< "' . substr($buffer, 9, 20) . '[...]"' . "\r\n\r\n";

            $this->input .= substr($buffer, 9);
            return true;
        }
    }

    private function addAnchors($matches)
    {
        static $counter = -1;

        if ($matches[2] === "h1") {
            global $dict;
            $counter++;

            $this->debug_log .= "Buffer: Adding anchor to \"$matches[0]\"\r\n";
            $this->debug_log .= '>> <a id="' . $dict["h1"][$counter] . "\" class=\"offsetAnchor\"></a>\r\n\r\n";

            return '<a id="' . $dict["h1"][$counter] . "\" class=\"offsetAnchor\"></a>
        $matches[0]\r\n";
        }

        return $matches[0];
    }

    private function addAnchorsToH1($buffer)
    {
        return preg_replace_callback(
            "/(<([^.]+)>)([^<]+)(<\\/\\2>)/s",
            array($this, 'addAnchors'),
            $buffer
        );
    }

    private function addTooltips($buffer)
    {
        $this->debug_log .= "Parsing tooltips...\r\n\r\n";
        $buffer = $this->tooltip_parser->parse($buffer);
        return $buffer;
    }

    private function addInput($buffer)
    {
        $this->debug_log .= "Buffer: replacing %OUTPUT% with input cache\r\n";
        $this->debug_log .= '>> ' . substr($this->input, 0, 20) . "[...]\r\n\r\n";
        return preg_replace(
            '/%OUTPUT%/',
            substr($this->input, 0, -1) . "\r\n",
            $buffer
        );
    }

    private function printDebugLog($buffer)
    {
        return preg_replace(
            '/%DEBUG%/',
            htmlspecialchars(substr($this->debug_log, 0) . "Done. ツ"),
            $buffer
        );
    }

    private function printDictionary($buffer)
    {
        global $dict;

        foreach ($dict as $entry=>$value)
        {
            $output .= "$entry { \r\n";

            foreach ($value as $key=>$value)
            {
                $output .= "    \"$key\": " . preg_replace('/\s\s+/', ' \r\n ', $value) . "\r\n";
            }

            $output .= "}\r\n\r\n";
        }

        $this->debug_log .= "Buffer: replacing %DICT% with dict.php\r\n";
        $this->debug_log .= '>> ' . substr($output, 0, strpos($output, "\r\n")) . "[...]\r\n\r\n";
        return preg_replace(
            '/%DICT%/',
            $output,
            $buffer
        );
    }

    private function printInputLog($buffer)
    {
        $this->debug_log .= "Buffer: replacing %INPUT% with input cache\r\n";
        $this->debug_log .= '>> ' . substr($this->input, 0, 20) . "[...]\r\n\r\n";
        return preg_replace(
            '/%INPUT%/',
            htmlspecialchars(substr($this->input, 0, -1)) . "\r\n",
            $buffer
        );
    }

    public function parse($buffer)
    {
        if ($this->catchArticle($buffer))
        {
            return null;
        }

        $buffer = $this->addInput($buffer);
        $buffer = $this->addAnchorsToH1($buffer);
        $buffer = $this->addTooltips($buffer);

        $buffer = $this->printInputLog($buffer);
        $buffer = $this->printDictionary($buffer);
        $buffer = $this->printDebugLog($buffer);

        return $buffer;
    }
}
