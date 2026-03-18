<?php

use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\WYSIWYGEditor;

return [
    Text::make("Titolo", "title")->helperText(
        "Inserisci il titolo principale in homepage",
    ),
    WYSIWYGEditor::make("Sottotitolo", "subtitle")
        ->toolbar(["bold", "italic"])
        ->tabs("all")
        ->disableMediaUpload()
        ->helperText("Inserisci il sottotitolo"),
    Text::make("Testo link scroll", "scroll_link_text")->helperText(
        "Testo del link per scorrere verso il basso",
    ),
];
