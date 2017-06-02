<?php

return PhpCsFixer\Config::create()
    ->setIndent("\t")
    ->setLineEnding("\r\n")
    ->setRules([
        '@Symfony' => true,
        'full_opening_tag' => false,
    ]);
