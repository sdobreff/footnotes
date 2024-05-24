/* 
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License along
    with this program; if not, write to the Free Software Foundation, Inc.,
    51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/
(function() {
    var { __ } = wp.i18n;
    tinymce.PluginManager.add('awesome-footnotes', function( editor, url ) {
        editor.addButton( 'awesome-footnotes', {
            title: __( 'Add / remove footnote', 'awesome-footnotes' ),
            icon: 'awesome-footnotes-admin-button',
            onclick: function() {
                //if text is highlighted, wrap that text in a footnote
                //otherwise, show an editor to insert a footnote
                editor.focus();
                var content = editor.selection.getContent();
                if (content.length > 0) {
                    if (content.indexOf(awef_gut.open) == -1 && content.indexOf(awef_gut.close) == -1) {
                        editor.selection.setContent(awef_gut.open + content + awef_gut.close);
                    } else if (content.indexOf(awef_gut.open) != -1 && content.indexOf(awef_gut.close) != -1) {
                        editor.selection.setContent(content.replace(awef_gut.open, '').replace(awef_gut.close, ''));
                    } else {
                        //we don't have a full tag in the selection, do nothing
                    }
                } else {
                    editor.windowManager.open( {
                        title: __( 'Insert Footnote', 'awesome-footnotes' ),
                        body: [{
                            type: 'textbox',
                            name: 'footnote',
                            label: __( 'Foot note', 'awesome-footnotes' ),
                        }],
                        onsubmit: function( e ) {
                            editor.insertContent( awef_gut.open + e.data.footnote + awef_gut.close);
                        }
                    });
                }
            }
    
        });
    });
    })();