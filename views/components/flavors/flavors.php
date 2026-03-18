<?php

use Extended\ACF\Fields\Group;
use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\WysiwygEditor;

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
    Tab::make("Arctic Mint", "arctic__tab")->placement("left"),
    Group::make("Arctic Mint", "arctic")->fields([
        Text::make("Claim", "claim")->helperText("Claim del prodotto"),
        WYSIWYGEditor::make("Descrizione", "description")
            ->toolbar(["bold", "italic"])
            ->tabs("all")
            ->disableMediaUpload()
            ->helperText("Inserisci una descrizione del prodotto"),
    ]),
    Tab::make("Wild Berry", "wild__tab")->placement("left"),
    Group::make("Wild Berry", "wild")->fields([
        Text::make("Claim", "claim")->helperText("Claim del prodotto"),
        WYSIWYGEditor::make("Descrizione", "description")
            ->toolbar(["bold", "italic"])
            ->tabs("all")
            ->disableMediaUpload()
            ->helperText("Inserisci una descrizione del prodotto"),
    ]),
];
