<?php
echo '<body>
    <ul class="menu">
        <a href="#debug"><li>Debug (Log)</li></a>
        <a href="#dict"><li>Dictionary (PHP)</li></a>
        <a href="#input"><li>Input (Article)</li></a>
        <a href="#output"><li>Output (Parsed)</li></a>
    </ul>

    <div class="block">
        <h1>Debug</h1>
        <pre>
%DEBUG%</pre>
    </div>

    <div class="block">
        <h1>Dictionary</h1>
        <pre>
%DICT%</pre>
    </div>

    <div class="block">
        <h1>Input</h1>
        <pre>
%INPUT%</pre>
    </div>

    <div class="block">
        <h1>Output</h1>
        <p>
%OUTPUT%
        </p>
    </div>
';
