<?php

use Extended\ACF\Fields\Group;
use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\WysiwygEditor;
use Extended\ACF\Fields\Repeater;

return [
    Tab::make("Intestazione")->placement("left"),
    Text::make("Sovratitolo", "surtitle")->helperText(
        "Testo breve visualizzato sopra il titolo principale",
    ),
    Text::make("Titolo", "title")->helperText(
        "Titolo principale della sezione",
    ),
    WYSIWYGEditor::make("Sottotitolo", "subtitle")
        ->toolbar(["bold", "italic"])
        ->tabs("all")
        ->disableMediaUpload()
        ->helperText("Inserisci il sottotitolo"),
    Text::make("Note", "notes")->helperText(
        "Testo aggiuntivo o disclaimer da mostrare nella sezione",
    ),
    Tab::make("Città", "cities_tab")->placement("left"),
    Group::make("Città", "cities")->fields([
        Repeater::make("Date", "dates")->fields([
            Text::make("Nome città", "city_name"),
            Text::make("Data", "date"),
        ]),
    ]),
];
