/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
    config.toolbar = 'MyToolbar';

    config.toolbar_MyToolbar =
    [
        ['Cut','Copy','Paste','-'/*,'SpellChecker', 'Scayt'*/],
        ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
        ['Styles','Format','FontSize'],
        '/',
        ['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
        ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],
        ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
        ['HorizontalRule','Smiley','SpecialChar'],
        ['TextColor','BGColor'],
    ];

    config.toolbar_MyToolbar_ws =
    [
        ['Cut','Copy','Paste','-'],
        ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
        ['Styles','Format','FontSize'],
        '/',
        ['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
        ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],
        ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
        ['HorizontalRule','SpecialChar'],
        ['TextColor','BGColor'],
    ];
    config.startupFocus = true
    config.height = 200; // altura
    config.width  = 650; // largura
    config.resize_enabled = false;
    config.resize_minHeight = 200;    
    config.resize_maxHeight = 350;
    config.resize_minWidth = 650;
    config.resize_maxWidth = 750;
    config.tabSpaces = 4;
    
    config.smiley_path = '../js/ckeditor/plugins/smiley/images/';
    config.entities_latin = false;
    //config.skin = 'office2003';

    config.smiley_images = [
    'regular_smile.gif','sad_smile.gif','wink_smile.gif','teeth_smile.gif','confused_smile.gif','tounge_smile.gif',
    'embaressed_smile.gif','omg_smile.gif','whatchutalkingabout_smile.gif','angry_smile.gif','angel_smile.gif','shades_smile.gif',
    'devil_smile.gif','cry_smile.gif','lightbulb.gif','thumbs_down.gif','thumbs_up.gif','heart.gif',
    'broken_heart.gif','kiss.gif','envelope.gif', 
    '3m_sie.gif', 'cfaniak.gif', 'jupi.gif', 'kwasny.gif', 'nonono.gif', 'ok.gif', 'ok2.gif', 'papa.gif', 'poklon.gif',
    'spiewa.gif', 'takiego_wala.gif', 'wow.gif', 'yahoo.gif', 'zygi.gif', 'aniol.gif', 'brawa.gif', 'chory.gif', 'dobani.gif',
    'fuck.gif', 'glodny.gif', 'hahaha.gif', 'hmmm.gif', 'kwasny.gif', 'lol.gif', 'upset.gif', 'usmiech.gif', 'ysz2.gif', 'ysz4.gif', 'ysz.gif'];
    config.smiley_columns = 10;
};
