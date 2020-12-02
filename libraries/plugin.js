/*
* Plugin name:  ImageGen
*
* @author Notari Kevin
* @version 1.0
*/

( function() {
    CKEDITOR.plugins.add( 'ImageGen',
        {
            icons: 'ImageGenIcon', // %REMOVE_LINE_CORE%
            hidpi: true, // %REMOVE_LINE_CORE%
            init: function( editor )
            {
                CKEDITOR.dialog.add( 'ImageGenDialog', function (instance)
                {
                    return {
                        title : 'ImageGen',
                        minWidth : 350,
                        minHeight : 200,
                        contents :
                            [
                                {
                                    id : 'ImageGen',
                                    expand : true,
                                    elements :[
                                        {
                                            id : 'text',
                                            type : 'text',
                                            label : 'Texte',
                                            validate: CKEDITOR.dialog.validate.notEmpty("Le champ 'texte' doit être renseigné !"),
                                            default: editor.config.ImageGenButton_text,
                                        },
                                        {
                                            type: 'hbox',
                                            align: 'left',
                                            widths: [ '30%', '30%' ],
                                            children: [
                                                {
                                                    id : 'width',
                                                    type : 'text',
                                                    label : 'largeur',
                                                    validate: CKEDITOR.dialog.validate.notEmpty("Le champ 'largeur' doit être renseigné !"),
                                                    width: '100px',
                                                    default: editor.config.ImageGenButton_width,
                                                },
                                                {
                                                    id : 'height',
                                                    type : 'text',
                                                    label : 'Hauteur',
                                                    validate: CKEDITOR.dialog.validate.notEmpty("Le champ 'hauteur' doit être renseigné !"),
                                                    width: '100px',
                                                    default: editor.config.ImageGenButton_height,
                                                },
                                            ]
                                        },

                                        {
                                            id : 'bg',
                                            type : 'text',
                                            label : 'Couleur de fond',
                                            validate: CKEDITOR.dialog.validate.notEmpty("Le champ 'couleur de fond' doit être renseigné !"),
                                            width: '100px',
                                            default: editor.config.ImageGenButton_bg.substring(1),
                                        },

                                        {
                                            id : 'color',
                                            type : 'text',
                                            label : 'Couleur de texte',
                                            validate: CKEDITOR.dialog.validate.notEmpty("Le champ 'couleur de texte' doit être renseigné !"),
                                            width: '100px',
                                            default: editor.config.ImageGenButton_color.substring(1),
                                        }
                                    ]
                                }
                            ],
                        onOk: function() {
                            var img = instance.document.createElement('img');
                            var width = this.getContentElement('ImageGen','width').getValue();
                            var height = this.getContentElement('ImageGen','height').getValue();
                            var bg = this.getContentElement('ImageGen','bg').getValue();
                            var color = this.getContentElement('ImageGen','color').getValue();
                            var text = this.getContentElement('ImageGen','text').getValue();
                            img.setAttribute("width", width);
                            img.setAttribute("height", height);
                            img.setAttribute("bg", bg);
                            img.setAttribute("color", color);
                            img.setAttribute("alt", text);
                            img.setAttribute("src", editor.config.ImageGenButton_url + '/'
                                + width + '/'
                                + height + '/'
                                + bg + '/'
                                + color + '/'
                                + text);
                            img.setAttribute("class",  'img-gen');
                            instance.insertElement(img);
                        }
                    };
                } );

                editor.addCommand( 'ImageGenCommand', new CKEDITOR.dialogCommand( 'ImageGenDialog',
                    { allowedContent: 'img' }
                ) );

                editor.ui.addButton( 'ImageGenButton',
                    {
                        label: 'ImageGen',
                        command: 'ImageGenCommand',
                        icon: 'plugins/ImageGenIcon.png'
                    } );
            }
        } );
} )();