<?php
namespace JMS\autotooltip;

class OutputParser
{
    private $tooltipParser;
    private $debugLog;
    private $input;
    private $newline = "[...]\r\n\r\n";

    public function __construct()
    {
        $this->tooltipParser = new TooltipParser;
    }

    private function catchArticle($buffer)
    {
        if (substr($buffer, 0, 9) == '%ARTICLE%') {
            $this->debugLog .= "Adding article to input cache\r\n";
            $this->debugLog .= '<< "' . substr($buffer, 9, 20) . $this->newline;

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

            $this->debugLog .= "Buffer: Adding anchor to \"$matches[0]\"\r\n";
            $this->debugLog .= '>> <a id="' . $dict["h1"][$counter] . "\" class=\"offsetAnchor\"></a>\r\n\r\n";

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
        $this->debugLog .= "Parsing tooltips...\r\n\r\n";
        $buffer = $this->tooltipParser->parse($buffer);
        return $buffer;
    }

    private function addInput($buffer)
    {
        $this->debugLog .= "Buffer: replacing %OUTPUT% with input cache\r\n";
        $this->debugLog .= '>> ' . substr($this->input, 0, 20) . $this->newline;
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
            htmlspecialchars(substr($this->debugLog, 0) . "Done. ツ"),
            $buffer
        );
    }

    private function printDictionary($buffer)
    {
        global $dict;

        $output = '';

        foreach ($dict as $entry => $value) {
            $output .= "$entry { \r\n";

            foreach ($value as $key => $value) {
                $output .= "    \"$key\": " . preg_replace('/\s\s+/', ' \r\n ', $value) . "\r\n";
            }

            $output .= "}\r\n\r\n";
        }

        $this->debugLog .= "Buffer: replacing %DICT% with dict.php\r\n";
        $this->debugLog .= '>> ' . substr($output, 0, strpos($output, "\r\n")) . $this->newline;
        return preg_replace(
            '/%DICT%/',
            $output,
            $buffer
        );
    }

    private function printInputLog($buffer)
    {
        $this->debugLog .= "Buffer: replacing %INPUT% with input cache\r\n";
        $this->debugLog .= '>> ' . substr($this->input, 0, 20) . $this->newline;
        return preg_replace(
            '/%INPUT%/',
            htmlspecialchars(substr($this->input, 0, -1)) . "\r\n",
            $buffer
        );
    }

    public function parse($buffer)
    {
        if ($this->catchArticle($buffer)) {
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
